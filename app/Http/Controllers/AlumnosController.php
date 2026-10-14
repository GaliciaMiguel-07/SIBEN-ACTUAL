<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AlumnoBachillerato;
use App\Models\Bachillerato;
use App\Models\Baja;
use App\Models\BecasPractica;
use App\Models\Discapacidad;
use App\Models\Documento;
use App\Models\Entidad;
use App\Models\EntidadNal;
use App\Models\Escuela;
use App\Models\Escuela2;
use App\Models\EscuelaLicenciatura;
use App\Models\EscuelaPractica;
use App\Models\Especialidad;
use App\Models\Historia;
use App\Models\Lengua;
use App\Models\Licenciatura;
use App\Models\Municipio;
use App\Models\Necesidad;
use App\Models\Pais;
use App\Models\Registro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumnoController extends sibenController
{
    /**
     * index
     * Genera el menú principal de acuerdo al nivel del usuario autenticado.
     */
    public function index()
    {
        $ctx        = $this->obtenerContextoUsuario();
        $nivel      = $ctx['nivel'] ?? null;
        $captura    = $ctx['captura'] ?? false;
        $id_escuela = $ctx['id_escuela'] ?? null;

        $mensaje = "";
        $opcMenu = [];

        switch ($nivel) {
            case 2: // Menú para enlaces
                $opcMenu = [
                    'Captura' => [
                        ['alumnos', 'Editar Alumno', 'listaEscuelas']
                    ],
                    'Bajas' => [
                        ['bajas', 'Definitiva', 'busquedaAlumnos/d/'],
                        ['bajas', 'Listado Bajas Temp.', 'busquedaAlumnos2/t/'],
                        ['bajas', 'Listado Bajas Def.', 'busquedaAlumnos2/d/']
                    ],
                    'Consultas' => [
                        ['consultasHistorico', 'Individual', 'busquedaGlobal/p'],
                        ['consultasHistorico', 'Global', 'busquedaGlobal/g']
                    ]
                ];
                break;

            case 3: // Menú para supervisores
                $opcMenu = [
                    'Captura' => [
                        ['alumnos', 'Editar Alumno', 'busquedaAlumnos/e/' . $id_escuela]
                    ],
                    'Bajas' => [
                        ['bajas', 'Lista B. Temp.', 'listadosBajas/t'],
                        ['bajas', 'Lista B. Def.', 'listadosBajas/d'],
                    ],
                    'Egresados' => [
                        ['egresados', 'Egresados', 'busquedax']
                    ],
                    'Consultas' => [
                        ['consultasHistorico', 'Individual', 'busquedaGlobal/p'],
                        ['consultasHistorico', 'Global', 'busquedaGlobal/g']
                    ],
                    'EscuelaPractica' => [
                        ['escuelasPractica', 'Edición', 'buscarEscuelas']
                    ]
                ];
                if (!$captura) {
                    $mensaje = '<div style="color:red;"><h2>La captura está finalizada</h2></div>';
                }
                break;

            case 4: // Menú para capturistas
                $opcMenu = [
                    'Captura' => [
                        ['alumnos', 'Altas Alumno', 'opcionesAltaAlumno'],
                        ['alumnos', 'Reinscribir', 'busquedaAlumnos/i/' . $id_escuela],
                        ['alumnos', 'Editar Alumno', 'busquedaAlumnos/e/' . $id_escuela],
                        ['alumnos', 'Cambio Escuela', 'opcionCambioEscuela']
                    ],
                    'Bajas' => [
                        ['alumnos', 'Temporales', 'busquedaAlumnos/t/' . $id_escuela],
                        ['bajas', 'Lista B. Temp.', 'listadosBajas/t'],
                        ['bajas', 'Lista B. Def.', 'listadosBajas/d']
                    ],
                    'Egresados' => [
                        ['egresados', 'Egresados', 'busquedax']
                    ],
                    'Consultas' => [
                        ['consultasHistorico', 'Individual', 'busquedaGlobal/p'],
                        ['consultasHistorico', 'Global', 'busquedaGlobal/g']
                    ]
                ];
                if (!$captura) {
                    $mensaje = '<div style="color:red;"><h2>La captura está finalizada</h2></div>';
                }
                break;

            case 6: // Menú para observador
                $opcMenu = [
                    'Bajas' => [
                        ['bajas', 'Listado Bajas Temp.', 'busquedaAlumnos2/t/'],
                        ['bajas', 'Listado Bajas Def.', 'busquedaAlumnos2/d/']
                    ],
                    'Consultas' => [
                        ['consultasHistorico', 'Individual', 'busquedaGlobal/p']
                    ]
                ];
                break;
        }

        return view('alumnos.index', compact('mensaje', 'opcMenu'));
    }

    /**
     * listaEscuelas
     * Muestra la lista de escuelas pertenecientes a la entidad del usuario.
     */
    public function listaEscuelas()
    {
        $ctx        = $this->obtenerContextoUsuario();
        $modulo     = $ctx['modulo'] ?? null;
        $id_entidad = $ctx['id_entidad'] ?? null;

        $escuelas = Escuela2::where('status', 'a')
            ->where('unica', '<', 2)
            ->where('id_entidad', $id_entidad)
            ->where('modulo', $modulo)
            ->get(['id', 'escuela', 'escuela_key', 'captura']);

        return view('alumnos.lista_escuelas', compact('escuelas'));
    }

    /**
     * setIdEscuela
     * Guarda la escuela seleccionada en la sesión.
     */
    public function setIdEscuela(Request $request)
    {
        $id_escuela = $request->input('Escuela.id', $request->input('id_escuela'));
        session(['Usuario.id_escuela' => $id_escuela]);

        return response('');
    }

    /**
     * busquedaAlumnos
     * Muestra el formulario con los catálogos necesarios para filtrar alumnos.
     */
    public function busquedaAlumnos($opc = null, $id_escuela = null)
    {
        if (!$opc) {
            return redirect()->route('alumnos.index');
        }

        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $id_escuela ?? ($ctx['id_escuela'] ?? null);

        $licenciaturas = EscuelaLicenciatura::with(['licenciaturaModel', 'especialidadModel'])
            ->where('id_escuela', $id_escuela)
            ->get();

    }
}            