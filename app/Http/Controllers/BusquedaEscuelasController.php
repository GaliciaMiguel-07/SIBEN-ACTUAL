<?php

namespace App\Http\Controllers;

use App\Models\DomicilioEscuela;
use App\Models\Entidad;
use App\Models\Escuela;
use App\Models\Escuela2;
use App\Models\Especialidad;
use App\Models\Licenciatura;
use App\Models\Localidad;
use App\Models\Municipio;
use App\Models\Personal;
use App\Models\Funcion;
use App\Models\EncuestaSanitaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusquedaEscuelasController extends Controller 
{
    public function index() 
    {
        return redirect()->route('siben.busqueda_escuelas.busqueda');
    }

    public function busqueda() 
    {
        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');
        $unica = [0 => 'Instituciones', 2 => 'Sedes'];
        $turno = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];
        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');

        return view('layouts.busquedaEscuelas.busqueda', compact(
            'entidades',
            'unica',
            'turno',
            'licenciaturas'
        ));
    }

    public function resultadoBusqueda(Request $request) 
    {
        // 1. Obtenemos los valores limpiamente (sin necesidad de buscar 'data.Escuela')
        $nombreEscuela  = $request->input('Escuela.escuela');
        $idEntidad      = $request->input('Escuela.id_entidad');
        $sostenimiento  = $request->input('Escuela.sostenimiento');
        $turno          = $request->input('Escuela.turno');
        $idLicenciatura = $request->input('EscuelaLicenciatura.id_licenciatura');
        
        // 2. Laravel maneja los checkboxes con filled() de forma segura
        $tieneCorreo    = $request->filled('Correo.op1');
        $tienePagina    = $request->filled('Pagina.op2');

        // 3. Consulta base
        $query = Escuela2::query()
            ->where('status', 'a')
            ->where('modulo', 'l');

        if (!empty($idEntidad)) {
            $query->where('id_entidad', $idEntidad);
        }

        if (!empty($sostenimiento)) {
            $query->where('sostenimiento', $sostenimiento);
        }

        if (!empty($turno)) {
            $query->where('turno', $turno);
        }

        // Subconsulta de licenciatura
        if (!empty($idLicenciatura)) {
            $query->where('unica', '<', 2)
                  ->whereIn('id', function ($subquery) use ($idLicenciatura) {
                      $subquery->select('id_escuela')
                               ->from('escuelas_licenciaturas')
                               ->where('id_licenciatura', $idLicenciatura);
                  });
        } else {
            $query->where('unica', '>', 0);
        }

        if ($tieneCorreo) {
            $query->whereNotNull('correo_electronico')->where('correo_electronico', '!=', ' ');
        }

        if ($tienePagina) {
            $query->whereNotNull('pagina_web')->where('pagina_web', '!=', ' ');
        }

        if (!empty($nombreEscuela)) {
            $query->where('escuela', 'LIKE', '%' . trim($nombreEscuela) . '%');
        }


        $escuelas = $query->orderBy('id_entidad')->paginate(20)->withQueryString();

        return view('layouts.busquedaEscuelas.resultado', compact('escuelas'));
    }
    
    public function detallesEscuela(Request $request, $id_escuela = null) 
    {
        if (!$id_escuela) {
            $id_escuela = $request->input('Escuela2.id') ?? $request->input('Escuela.id') ?? $request->input('id_escuela');
        }

        $domicilioEscuela = null;
        $localidad        = null;
        $escuela          = null;
        $personal         = collect();
        $funciones        = collect();
        $licenciaturas    = collect();
        $sostenimiento    = '';
        $turno            = '';
        $imagenes         = [];
        $sedes            = false;
        $otro             = null;

        if (!empty($id_escuela)) {
            $escuela = Escuela::find($id_escuela);

            if ($escuela) {
                $domicilioEscuela = DomicilioEscuela::where('id_escuela', $id_escuela)->first();

                if ($domicilioEscuela) {
                    $localidad = Localidad::where('id_localidad', $domicilioEscuela->cve_loc)
                        ->where('id_municipio', $domicilioEscuela->cve_mun)
                        ->where('id_entidad', $domicilioEscuela->cve_ent)
                        ->first();
                }

                $municipio = Municipio::find($escuela->id_municipio);
                if ($municipio) {
                    $escuela->municipio_nombre = $municipio->municipio;
                }
                
                $funciones = Funcion::pluck('funcion', 'id');
                $personal = Personal::where('id_escuela', $id_escuela)
                    ->where('id_funcion', '<', 5)
                    ->where('status', 'a')
                    ->orderBy('id_funcion', 'asc')
                    ->get(['nombre', 'apellido_paterno', 'apellido_materno', 'id_funcion']);
                    
                foreach ($personal as $directivo) {
                    $directivo->funcion = $funciones->get($directivo->id_funcion);
                }
                
                $licenciaturas = DB::table('escuelas_licenciaturas as EscuelaLicenciatura')
                    ->leftJoin('licenciaturas as Licenciatura', 'EscuelaLicenciatura.id_licenciatura', '=', 'Licenciatura.id')
                    ->leftJoin('especialidades as Especialidad', 'EscuelaLicenciatura.id_especialidad', '=', 'Especialidad.id')
                    ->where('EscuelaLicenciatura.id_escuela', $id_escuela)
                    ->distinct()
                    ->get();

                switch ($escuela->sostenimiento) {
                    case 'p':
                        $sostenimiento = 'Pública';
                        break;
                    case 'q':
                        $sostenimiento = 'Particular';
                        break;
                }

                switch ($escuela->turno) {
                    case 'm':
                        $turno = 'Matutino';
                        break;
                    case 'v':
                        $turno = 'Vespertino';
                        break;
                    case 'd':
                        $turno = 'Discontinuo';
                        break;
                }

                for ($i = 1; $i <= 6; $i++) {
                    $imgPath = public_path('img/escuelas/big/' . $escuela->escuela_key . '_' . $i . '.jpg');
                    $imagenes[$i] = file_exists($imgPath) ? $i : 0;
                }

                if ($escuela->unica == 2) {
                    $sedes = true;
                    $otro = DB::table('sedes')
                        ->join('escuelas as Escuela2', 'sedes.id_sede', '=', 'Escuela2.id')
                        ->where('sedes.id_escuela', $id_escuela)
                        ->select('Escuela2.id', 'Escuela2.escuela_key', 'Escuela2.escuela')
                        ->get();
                }
            }
        }

        return view('layouts.busquedaEscuelas.detalles', compact(
            'escuela',
            'domicilioEscuela',
            'localidad',
            'personal',
            'funciones',
            'licenciaturas',
            'sostenimiento',
            'turno',
            'imagenes',
            'sedes',
            'otro'
        ));
    }
}