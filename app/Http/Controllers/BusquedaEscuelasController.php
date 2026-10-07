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
        $entidades     = Entidad::where('id', '<', 33)->pluck('entidad', 'id');
        $unica         = [0 => 'Instituciones', 2 => 'Sedes'];
        $turno         = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];
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
        $nombreEscuela  = $request->input('Escuela.escuela');
        $idEntidad      = $request->input('Escuela.id_entidad');
        $sostenimiento  = $request->input('Escuela.sostenimiento');
        $turno          = $request->input('Escuela.turno');
        $idLicenciatura = $request->input('EscuelaLicenciatura.id_licenciatura');
        
        $tieneCorreo    = $request->filled('Correo.op1');
        $tienePagina    = $request->filled('Pagina.op2');

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
        $domicilioData    = [];
        $localidad        = null;
        $municipio        = null;
        $entidad          = null;
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
                // Entidad y Municipio
                $entidad   = Entidad::find($escuela->id_entidad);
                $municipio = Municipio::find($escuela->id_municipio);

                // Domicilio y Localidad
                $domicilioEscuela = DomicilioEscuela::where('id_escuela', $id_escuela)->first();

                if ($domicilioEscuela) {
                    $localidad = Localidad::where('id_localidad', $domicilioEscuela->cve_loc)
                        ->where('id_municipio', $domicilioEscuela->cve_mun)
                        ->where('id_entidad', $domicilioEscuela->cve_ent)
                        ->first();
                }

                // Construcción de $domicilioData combinando domicilio_escuela y escuela
                $vialidadDom     = trim(($domicilioEscuela->tipovial ?? '') . ' ' . ($domicilioEscuela->nomvial ?? ''));
                $asentamientoDom = trim(($domicilioEscuela->tipoasen ?? '') . ' ' . ($domicilioEscuela->nomasen ?? ''));

                $domicilioData = [
                    'vialidad'       => !empty($vialidadDom) ? $vialidadDom : ($escuela->calle ?? ''),
                    'num_ext1'       => trim(($domicilioEscuela->numextnum1 ?? '') . ' ' . ($domicilioEscuela->numextalf1 ?? '')),
                    'num_ext2'       => $domicilioEscuela->numextnum2 ?? '',
                    'num_int'        => trim(($domicilioEscuela->numintnum ?? '') . ' ' . ($domicilioEscuela->numintalf ?? '')),
                    'asentamiento'   => !empty($asentamientoDom) ? $asentamientoDom : ($escuela->colonia ?? ''),
                    'referencia1'    => trim(($domicilioEscuela->tiporef1 ?? '') . ' ' . ($domicilioEscuela->nomref1 ?? '')),
                    'referencia2'    => trim(($domicilioEscuela->tiporef2 ?? '') . ' ' . ($domicilioEscuela->nomref2 ?? '')),
                    'referencia3'    => trim(($domicilioEscuela->tiporef3 ?? '') . ' ' . ($domicilioEscuela->nomref3 ?? '')),
                    'complementaria' => $domicilioEscuela->descrubic ?? '',
                    'cp'             => (!empty($domicilioEscuela->cp) && $domicilioEscuela->cp !== '00000') ? $domicilioEscuela->cp : ($escuela->cp ?? ''),
                    'telefono1'      => $escuela->telefono1 ?? '',
                    'telefono2'      => $escuela->telefono2 ?? '',
                    'telefono3'      => $escuela->telefono3 ?? '',
                    'lada'           => $escuela->lada ?? '',
                    'fax'            => $escuela->fax ?? '',
                ];

                // Directivos y Funciones
                $funciones = Funcion::pluck('funcion', 'id');
                $personal  = Personal::where('id_escuela', $id_escuela)
                    ->where('id_funcion', '<', 5)
                    ->where('status', 'a')
                    ->orderBy('id_funcion', 'asc')
                    ->get(['nombre', 'apellido_paterno', 'apellido_materno', 'id_funcion']);
                    
                foreach ($personal as $directivo) {
                    $directivo->funcion = $funciones->get($directivo->id_funcion);
                }
                
                // Licenciaturas
                $licenciaturas = DB::table('escuelas_licenciaturas as EscuelaLicenciatura')
                    ->leftJoin('cat_licenciaturas as Licenciatura', 'EscuelaLicenciatura.id_licenciatura', '=', 'Licenciatura.id')
                    ->leftJoin('cat_especialidades as Especialidad', 'EscuelaLicenciatura.id_especialidad', '=', 'Especialidad.id')
                    ->where('EscuelaLicenciatura.id_escuela', $id_escuela)
                    ->distinct()
                    ->get();

                // Sostenimiento
                switch (strtolower($escuela->sostenimiento ?? '')) {
                    case 'p':
                        $sostenimiento = 'Pública';
                        break;
                    case 'q':
                    case 'pr':
                        $sostenimiento = 'Particular';
                        break;
                    default:
                        $sostenimiento = $escuela->sostenimiento;
                        break;
                }

                // Turno
                switch (strtolower($escuela->turno ?? '')) {
                    case 'm':
                        $turno = 'Matutino';
                        break;
                    case 'v':
                        $turno = 'Vespertino';
                        break;
                    case 'd':
                        $turno = 'Discontinuo';
                        break;
                    default:
                        $turno = $escuela->turno;
                        break;
                }

                // Búsqueda de imágenes
                if (!empty($escuela->escuela_key)) {
                    $clave = trim($escuela->escuela_key);
                    $carpetas = ['img/escuelas/big/', 'images/escuelas/big/', 'img/escuelas/', 'images/escuelas/'];
                    
                    foreach ($carpetas as $carpeta) {
                        for ($i = 1; $i <= 6; $i++) {
                            $patron = public_path($carpeta . $clave . '_' . $i . '.{jpg,JPG,jpeg,JPEG,png,PNG}');
                            $coincidencias = glob($patron, GLOB_BRACE);
                            if (!empty($coincidencias)) {
                                $imagenes[] = asset($carpeta . basename($coincidencias[0]));
                            }
                        }
                        if (count($imagenes) > 0) {
                            break;
                        }
                    }
                }

                // Sedes
                if ($escuela->unica == 2) {
                    $sedes = true;
                    $otro  = DB::table('sedes')
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
            'domicilioData',
            'localidad',
            'municipio',
            'entidad',
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