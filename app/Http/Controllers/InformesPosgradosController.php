<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Persona;
use App\Models\AlumnosPosgrado;
use App\Models\Posgrado;
use App\Models\Escuela;
use App\Models\EscuelaStatus;
use App\Models\Usuario;
use App\Models\DocentesPosgrado;
use App\Models\Estudios;
use App\Models\PosgradosDocentes;
use App\Models\Entidad;
use Illuminate\Http\Request;

class InformesPosgradosController extends sibenController
{
    /**
     * index
     */
    public function index()
    {
        return view('informes_posgrados.index');
    }

    /**
     * Listado para niveles 3 y 4
     */
    public function listados()
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        $opcionesMenu = array_fill(0, 9, '');
        if ($nivel == 3) {
            $opcionesMenu[0] = ['informesPosgrados', 'Ver Avance', 'AvanceEscuela', 0];
        }
        $opcionesMenu[1] = ['informesPosgrados', 'Listado Alumnos', 'listadoPosgradoAlumnos', 1];
        $opcionesMenu[2] = ['informesPosgrados', 'Listado Docentes', 'listadoGeneralDocentes', 1];
        $opcionesMenu[3] = ['informesPosgrados', 'Docentes Posgrado', 'listadoPosgradoDocentes', 1];

        return view('informes_posgrados.listados', compact('opcionesMenu'));
    }

    /**
     * Menu de listados para enlaces estatales
     */
    public function listadosEnlace($id_escuela = null)
    {
        if (!$id_escuela) {
            return redirect('/siben');
        }

        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        $opcionesMenu = array_fill(0, 9, '');
        $opcionesMenu[1] = ['informesPosgrados', 'Listado Alumnos', 'listadoPosgradoAlumnos', 1, $id_escuela];
        $opcionesMenu[2] = ['informesPosgrados', 'Listado Docentes', 'listadoGeneralDocentes', 1, $id_escuela];
        $opcionesMenu[3] = ['informesPosgrados', 'Docentes Posgrado', 'listadoPosgradoDocentes', 1, $id_escuela];

        return view('informes_posgrados.listados_enlace', compact('opcionesMenu', 'id_escuela'));
    }

    /**
     * Listado general de alumnos por posgrado
     */
    public function listadoPosgradoAlumnos($id_escuela = null)
    {
        $ctx = $this->contexto();
        if (!$id_escuela) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        }
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with(['municipio', 'entidad'])->find($id_escuela);
        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();
        $captura = $status->captura ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';
        
        $title = 'Listado Alumnos, ' . $escuela_key;

        // Se obtienen los nombres del capturista y supervisor de la escuela
        $encargados = Usuario::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->pluck('nombre', 'nivel')
            ->toArray();

        $enlace = Usuario::where('id_renapo', $id_renapo)
            ->where('modulo', $modulo)
            ->where('nivel', 2)
            ->pluck('nombre', 'nivel')
            ->toArray();

        $encargados = $encargados + $enlace;

        $posgrados = Posgrado::where('id_escuela', $id_escuela)->get();
        
        $posgradosArray = [];
        foreach ($posgrados as $posgrado) {
            $alumnos = AlumnosPosgrado::where('id_escuela', $id_escuela)
                ->where('status', 'a')
                ->where('id_posgrado', $posgrado->id)
                ->orderBy('periodo')
                ->get();
            
            $pArray = $posgrado->toArray();
            $pArray['AlumnosPosgrado'] = $alumnos->toArray();
            $posgradosArray[] = $pArray;
        }

        $variables = [];
        foreach ($encargados as $nivelUsr => $encargado) {
            switch ($nivelUsr) {
                case 2:
                    $variables['enlace'] = $encargado;
                    break;
                case 3:
                    $variables['supervisor'] = $encargado;
                    break;
                case 4:
                    $variables['capturista'] = $encargado;
                    break;
            }
        }

        return view('informes_posgrados.listado_posgrado_alumnos', [
            'title'      => $title,
            'encargados' => $variables,
            'ancho'      => '720',
            'posgrados'  => $posgradosArray,
            'escuela'    => $escuela,
            'captura'    => $captura,
        ]);
    }

    /**
     * Listado general de docentes
     */
    public function listadoGeneralDocentes($id_escuela = null)
    {
        $ctx = $this->contexto();
        if (!$id_escuela) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        }
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with(['municipio', 'entidad'])->find($id_escuela);
        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();
        $captura = $status->captura ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';
        $title = 'Listado General Docentes, ' . $escuela_key;

        $encargados = Usuario::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->pluck('nombre', 'nivel')
            ->toArray();

        $enlace = Usuario::where('id_renapo', $id_renapo)
            ->where('modulo', $modulo)
            ->where('nivel', 2)
            ->pluck('nombre', 'nivel')
            ->toArray();

        $encargados = $encargados + $enlace;

        $docentes = Persona::whereHas('docentesPosgrado', function($q) use ($id_escuela) {
            $q->where('id_escuela', $id_escuela)->where('status', 'a');
        })
        ->with(['docentesPosgrado' => function($q) use ($id_escuela) {
            $q->where('id_escuela', $id_escuela);
        }])
        ->orderBy('apellido_paterno')
        ->orderBy('apellido_materno')
        ->orderBy('nombre')
        ->get();

        $estudios = Estudios::pluck('estudios', 'id')->toArray();

        $variables = [];
        foreach ($encargados as $nivelUsr => $encargado) {
            switch ($nivelUsr) {
                case 2:
                    $variables['enlace'] = $encargado;
                    break;
                case 3:
                    $variables['supervisor'] = $encargado;
                    break;
                case 4:
                    $variables['capturista'] = $encargado;
                    break;
            }
        }

        return view('informes_posgrados.listado_general_docentes', [
            'title'      => $title,
            'docentes'   => $docentes,
            'estudios'   => $estudios,
            'encargados' => $variables,
            'escuela'    => $escuela,
            'ancho'      => '720',
            'captura'    => $captura,
        ]);
    }

    /**
     * Listado de docentes por posgrado
     */
    public function listadoPosgradoDocentes($id_escuela = null)
    {
        $ctx = $this->contexto();
        if (!$id_escuela) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        }
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with(['municipio', 'entidad'])->find($id_escuela);
        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();
        $captura = $status->captura ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';
        $title = 'Listado de Docentes por Posgrado, ' . $escuela_key;
        
        $posgrados = Posgrado::where('id_escuela', $id_escuela)->get()->toArray();
        
        $docentes = Persona::whereHas('docentesPosgrado', function($q) use ($id_escuela) {
            $q->where('id_escuela', $id_escuela)->where('status', 'a');
        })
        ->with(['docentesPosgrado' => function($q) use ($id_escuela) {
            $q->where('id_escuela', $id_escuela);
        }])
        ->orderBy('apellido_paterno')
        ->orderBy('apellido_materno')
        ->orderBy('nombre')
        ->get()
        ->toArray();

        $posgrados2 = [];
        foreach ($posgrados as $posgrado) {
            $posgradosDocentes = PosgradosDocentes::whereHas('docentesPosgrado', function($q) use ($id_escuela) {
                $q->where('id_escuela', $id_escuela)->where('status', 'a');
            })
            ->where('id_posgrado', $posgrado['id'])
            ->get()
            ->toArray();

            $posgrado['PosgradosDocentes'] = $posgradosDocentes;
            $posgrados2[] = $posgrado;
        }

        foreach ($posgrados2 as $j => $posgrado) {
            foreach ($posgrado['PosgradosDocentes'] as $i => $docente) {
                foreach ($docentes as $dato) {
                    if (isset($dato['docentes_posgrado']['id']) && $dato['docentes_posgrado']['id'] == $docente['id_docente']) {
                        $docente['Persona'] = $dato;
                    }
                }
                $posgrado['PosgradosDocentes'][$i] = $docente;
            }
            $posgrados2[$j] = $posgrado;
        }

        $encargados = Usuario::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->pluck('nombre', 'nivel')
            ->toArray();

        $enlace = Usuario::where('id_renapo', $id_renapo)
            ->where('modulo', $modulo)
            ->where('nivel', 2)
            ->pluck('nombre', 'nivel')
            ->toArray();

        $encargados = $encargados + $enlace;

        $variables = [];
        foreach ($encargados as $nivelUsr => $encargado) {
            switch ($nivelUsr) {
                case 2:
                    $variables['enlace'] = $encargado;
                    break;
                case 3:
                    $variables['supervisor'] = $encargado;
                    break;
                case 4:
                    $variables['capturista'] = $encargado;
                    break;
            }
        }

        return view('informes_posgrados.listado_posgrado_docentes', [
            'title'      => $title,
            'posgrados'  => $posgrados,
            'posgrados2' => $posgrados2,
            'encargados' => $variables,
            'escuela'    => $escuela,
            'ancho'      => '720',
            'captura'    => $captura,
        ]);
    }

    /**
     * Avance de alumnos muestra el total de alumnos y docentes activos
     */
    public function AvanceEscuela($id_escuela = null)
    {
        $ctx = $this->contexto();
        if (!$id_escuela) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        }
        $modulo = $ctx['modulo'] ?? null;

        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();
        $captura = $status->captura ?? null;

        $cuenta_alumnos = AlumnosPosgrado::where('id_escuela', $id_escuela)
            ->where('status', 'a')
            ->count();

        $cuenta_docentes = DocentesPosgrado::where('id_escuela', $id_escuela)
            ->where('status', 'a')
            ->count();

        $cuenta_posgrados = Posgrado::where('id_escuela', $id_escuela)->count();

        return view('informes_posgrados.avance_escuela', compact('cuenta_alumnos', 'cuenta_docentes', 'cuenta_posgrados', 'captura'));
    }

    /**
     * Avance de enlaces estatales
     */
    public function AvanceEnlace()
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $modulo = $ctx['modulo'] ?? null;
        $id_renapo = $ctx['id_renapo'] ?? null;

        $escuelas2 = [];
        $entidad = null;

        if ($nivel == 2) {
            $entidad = Entidad::where('id_renapo', $id_renapo)->first();
            if ($entidad) {
                $escuelas = Escuela::where('id_entidad', $entidad->id)
                    ->select('id', 'escuela', 'escuela_key')
                    ->get();

                foreach ($escuelas as $escuela) {
                    $posgrado_modulo = EscuelaStatus::where('modulo', $modulo)
                        ->where('id_escuela', $escuela->id)
                        ->where('status', 'a')
                        ->first();

                    if ($posgrado_modulo && $posgrado_modulo->id_escuela == $escuela->id) {
                        $cuenta_alumnos = AlumnosPosgrado::where('id_escuela', $escuela->id)
                            ->where('status', 'a')
                            ->count();

                        $cuenta_docentes = DocentesPosgrado::where('id_escuela', $escuela->id)
                            ->where('status', 'a')
                            ->count();

                        $escuelas2[] = [
                            'Escuela'        => $escuela->toArray(),
                            'EscuelaStatus'  => $posgrado_modulo->toArray(),
                            'CuentaAlumnos'  => $cuenta_alumnos,
                            'CuentaDocentes' => $cuenta_docentes,
                        ];
                    }
                }
            }
        }

        return view('informes_posgrados.avance_enlace', compact('entidad', 'escuelas2'));
    }

    /**
     * Cambiar estatus de captura
     */
    public function cambiaCaptura(\Illuminate\Http\Request $request, $id_escuela = null, $captura = null, $modulo = null, $id_entidad = null)
    {
        if ($id_escuela) {
            $status = EscuelaStatus::where('id_escuela', $id_escuela)
                ->where('modulo', 'p')
                ->first();
            $escuela = Escuela::find($id_escuela);

            if ($status && $escuela) {
                if (!$captura) {
                    $captura = ($status->captura == '0') ? '1' : '0';
                }
                $status->captura = $captura;
                $status->save();
                
                return redirect('/InformesPosgrados/captura/' . $escuela->id_entidad);
            }
        }

        return redirect('/InformesPosgrados');
    }

    /**
     * Captura general (Solo DGESPE)
     */
    public function captura(\Illuminate\Http\Request $request, $id_entidad = null, $modulo = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        if ($nivel != 1) {
            return redirect('/usuarios/logout');
        }
        
        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id')->toArray();

        $escuelas = Escuela::where('id_entidad', $id_entidad)
            ->whereIn('unica', ['0', '1'])
            ->orderBy('escuela_key')
            ->get();

        $escuelasData = [];
        foreach ($escuelas as $escuela) {
            $status = EscuelaStatus::where('id_escuela', $escuela->id)
                ->where('modulo', 'p')
                ->where('status', 'a')
                ->first();
            
            $escuelaArray = $escuela->toArray();
            $escuelaArray['captura'] = $status->captura ?? null;
            $escuelasData[] = ['Escuela' => $escuelaArray];
        }

        return view('informes_posgrados.captura', [
            'escuelas'  => $escuelasData,
            'entidades' => $entidades,
        ]);
    }
}