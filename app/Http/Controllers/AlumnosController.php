<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AlumnoBachillerato;
use App\Models\Bachillerato;
use App\Models\Baja;
use App\Models\BecasPractica;
use App\Models\Conflicto;
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

class AlumnosController extends sibenController
{
    /**
     * index
     * Genera el menú principal de acuerdo al nivel del usuario autenticado.
     */
    public function index()
    {
        $ctx        = $this->contexto();
        $nivel      = $ctx['nivel'] ?? null;
        $captura    = $ctx['captura'] ?? false;
        $id_escuela = $ctx['id_escuela'] ?? null;

        // Seguridad: Verificar que el usuario esté autenticado
        if (!$nivel) {
            return redirect()->route('login');
        }

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
                    $mensaje = '<div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; text-align: center;"><h2>La captura está finalizada</h2></div>';
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
                    $mensaje = '<div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; text-align: center;"><h2>La captura está finalizada</h2></div>';
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
     * Muestra la lista de escuelas de la entidad para enlaces.
     */
    public function listaEscuelas(?Request $request = null)
    {
        $ctx        = $this->contexto();
        $modulo     = $ctx['modulo'] ?? null;
        $id_entidad = $ctx['id_entidad'] ?? null;

        $escuelas = Escuela2::where('status', 'a')
            ->where('unica', '<', 2)
            ->where('id_entidad', $id_entidad)
            ->where('modulo', $modulo)
            ->get(['id', 'escuela', 'escuela_key', 'captura']);

        $anterior = route('alumnos.index');

        return view('alumnos.lista_escuelas', compact('escuelas', 'anterior'));
    }

    /**
     * setIdEscuela
     * Fija el id_escuela en la variable de sesión.
     */
    public function setIdEscuela(Request $request)
    {
        $id_escuela = $request->input('Escuela.id', $request->input('id_escuela'));
        session(['Usuario.id_escuela' => $id_escuela]);

        return view('elements.vacio');
    }

    /**
     * busquedaAlumnos
     * Muestra el formulario de búsqueda para funciones de alumnos.
     */
    public function busquedaAlumnos($opc = null, $id_escuela = null)
    {
        if (!$opc) {
            return redirect()->route('alumnos.index');
        }

        $ctx           = $this->contexto();
        $id_escuela    = $id_escuela ?? ($ctx['id_escuela'] ?? null);
        $nombreEntidad = $ctx['id_renapo'] ?? null;

        $licenciaturas = EscuelaLicenciatura::with(['licenciaturaModel', 'especialidadModel'])
            ->where('id_escuela', $id_escuela)
            ->get();

        $modalidadMap = ['e' => 'Escolarizada', 'm' => 'Mixta'];
        $turnoMap     = ['m' => 'Matutino', 'n' => 'Nocturno', 'v' => 'Vespertino', 'd' => 'Discontinuo'];

        $lic = [];
        $esp = [];
        $mod = [];
        $tur = [];

        foreach ($licenciaturas as $l) {
            if ($l->licenciaturaModel) {
                $lic[$l->licenciaturaModel->id] = $l->licenciaturaModel->licenciatura;
            }
            if ($l->especialidadModel) {
                $esp[$l->especialidadModel->id] = $l->especialidadModel->especialidad;
            }
            if (isset($modalidadMap[$l->modalidad])) {
                $mod[$l->modalidad] = $modalidadMap[$l->modalidad];
            }
            if (isset($turnoMap[$l->turno])) {
                $tur[$l->turno] = $turnoMap[$l->turno];
            }
        }

        $semestre = [];
        $paridadCaptura = ((int) $this->captura_actual) % 2;
        for ($i = 1; $i <= 13; $i++) {
            if (($i % 2) === $paridadCaptura) {
                $semestre[$i] = $i;
            }
        }

        if ($nombreEntidad == 'TS' || in_array($id_escuela, ['180', '193', 477, 372])) {
            if ($id_escuela == 372) {
                $semestre[6] = 6;
            } else {
                $semestre[8] = 8;
            }
        }

        if ($opc !== 't') {
            $status = ['i' => 'Inscrito', 'x' => 'No Inscrito', 't' => 'Baja Temporal', 'd' => 'Baja Definitiva'];
            $url    = route('alumnos.listasAlumnos', ['opc' => $opc, 'id_escuela' => $id_escuela]);
        } else {
            $status = ['i' => 'Inscrito', 'x' => 'No Inscrito'];
            $url    = route('bajas.listasAlumnos', ['opc' => $opc, 'id_escuela' => $id_escuela]);
        }

        $tituloTabla = [
            'i' => 'Para la Reinscripción',
            'e' => 'Para la Edición',
            't' => 'Para las Bajas Temporales'
        ];

        return view('alumnos.busqueda_alumnos', [
            'licenciaturas'  => $lic,
            'especialidades' => $esp,
            'modalidades'    => $mod,
            'turnos'         => $tur,
            'semestres'      => $semestre,
            'status'         => $status,
            'opc'            => $opc,
            'url'            => $url,
            'tituloTabla'    => $tituloTabla,
            'anterior'       => route('alumnos.index'),
        ]);
    }

    /**
     * listasAlumnos
     * Regresa la lista de alumnos según las opciones de búsqueda (AJAX).
     */
    public function listasAlumnos(Request $request, $opc = null, $id_escuela = null)
    {
        $buscaData  = $request->input('Busca', []);
        $id_escuela = $id_escuela ?? session('Usuario.id_escuela');

        $query = DB::table('historias')
            ->where('id_escuela', $id_escuela)
            ->where('captura', $this->captura_actual)
            ->where('status', '!=', 's');

        $contador = 0;
        foreach ($buscaData as $valor) {
            if (!$valor) {
                $contador++;
            }
        }

        if ($contador < 8) {
            foreach ($buscaData as $campo => $valor) {
                if (!empty($valor)) {
                    $query->where($campo, $valor);
                }
            }
        }

        $alumnos = $query->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get([
                'nid', 'apellido_paterno', 'apellido_materno', 'nombre',
                'curp', 'semestre', 'status', 'id_registro',
                'status_conflictos', 'modalidad'
            ]);

        $url = ($opc === 'i') 
            ? route('alumnos.reinscribirAlumnos') 
            : route('alumnos.actualizarAlumno');
        
        $titulo = ($opc === 'i') ? "Inscribir" : "Editar";

        $renglones = [];
        foreach ($alumnos as $alumno) {
            $boton = '';
            $parametros = "data[Historia][id_registro]=" . $alumno->id_registro;

            if ($alumno->status === 't' && $opc === 'i') {
                $evento = "confirmacion('$url', '$parametros');";
            } else {
                $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado);";
            }

            if (($alumno->modalidad === 'e' && $alumno->semestre < 9) || 
                ($alumno->modalidad === 'm' && $alumno->semestre < 13)) {
                $boton = '<input id="b_' . $alumno->id_registro . '" type="button" value="' . $titulo . '" onclick="' . $evento . '" class="boton__primario" style="padding: 4px 12px; font-size: 0.85rem; cursor: pointer;" />';
            }

            $accion = $boton;
            if ((($alumno->status === 'x' || $alumno->status === 't') && $opc === 'i') || 
                ($opc === 'e' && ($alumno->status === 'x' || $alumno->status === 'i'))) {
                if ($alumno->status_conflictos != 'n') {
                    $accion = $boton;
                } else {
                    $accion = '<div style="color: var(--error, #dc3545); font-weight: bold;">Conflicto</div>';
                }
            }

            switch ($alumno->status) {
                case 'x': $estilo = 'text-align:center; background-color: var(--error, #dc3545); color: #fff; font-weight: bold; padding: 4px; border-radius: 4px;'; $estado = 'No Insc.'; break;
                case 't': $estilo = 'text-align:center; background-color: #17a2b8; color: #fff; font-weight: bold; padding: 4px; border-radius: 4px;'; $estado = 'B. Temp.'; break;
                case 'i': $estilo = 'text-align:center; background-color: var(--exito, #28a745); color: #fff; font-weight: bold; padding: 4px; border-radius: 4px;'; $estado = 'Insc.'; break;
                case 'd': $estilo = 'text-align:center; background-color: #ffc107; color: #212529; font-weight: bold; padding: 4px; border-radius: 4px;'; $estado = 'B. Def.'; break;
                default:  $estilo = ''; $estado = ''; break;
            }

            $statusHtml = "<div style='{$estilo}'>{$estado}</div>";

            $renglones[$alumno->id_registro] = [
                'nid'              => $alumno->nid,
                'apellido_paterno' => $alumno->apellido_paterno,
                'apellido_materno' => $alumno->apellido_materno,
                'nombre'           => $alumno->nombre,
                'curp'             => $alumno->curp,
                'semestre'         => $alumno->semestre,
                'status'           => $statusHtml,
                'accion'           => $accion,
            ];
        }

        $cabeceras = ['No.', 'nid', 'Primer Apellido', 'Segundo Apellido', 'Nombre', 'CURP', 'Sem', 'Estado', 'Acción'];

        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }

    /**
     * reinscribirAlumnos
     * Muestra el formulario para reinscripción y procesa su guardado.
     */
    public function reinscribirAlumnos(Request $request)
    {
        $ctx           = $this->contexto();
        $id_escuela    = $ctx['id_escuela'] ?? null;
        $nombreEntidad = $ctx['id_renapo'] ?? null;

        $escuela       = Escuela::find($id_escuela);
        $sostenimiento = $escuela->sostenimiento ?? null;

        $id_registro = $request->input('data.Historia.id_registro', $request->input('Historia.id_registro', $request->input('id_registro')));

        if ($request->isMethod('post') && $request->has('Registro')) {
            $datosAlumno       = $request->input('Alumno', []);
            $datosRegistro     = $request->input('Registro', []);
            $datosBachillerato = $request->input('AlumnoBachillerato', []);

            $parametros = [
                'nid'             => $datosAlumno['numero_identificador'] ?? null,
                'id_registro'     => $datosRegistro['id'] ?? null,
                'id_licenciatura' => $datosRegistro['id_licenciatura'] ?? null,
                'id_especialidad' => $datosRegistro['id_especialidad'] ?? null,
                'matricula'       => $datosRegistro['matricula'] ?? null,
                'semestre'        => $datosRegistro['semestre'] ?? null,
                'id_escuela'      => $id_escuela,
                'captura'         => $this->captura_actual
            ];

            $errores = Registro::inscripcionAlumnoStatic($parametros);

            if (empty($errores)) {
                $datosRegistro['status'] = 'i';
                $datosRegistro['fecha_actualizacion'] = now();

                Baja::revisarBaja($datosAlumno['numero_identificador'] ?? null, $id_escuela);

                if (($datosAlumno['discapacidad'] ?? 'n') === 'n') {
                    $datosAlumno['tipo_discapacidad'] = 0;
                }

                if (($datosBachillerato['id_bachillerato'] ?? 0) != 0) {
                    $datosBachillerato['otro'] = 'no';
                }

                $regModel = Registro::find($datosRegistro['id'] ?? null);
                if ($regModel) {
                    $apoyos = Registro::uneApoyosStatic($datosRegistro);
                    $datosRegistro['apoyos'] = $apoyos;
                    $regModel->update($datosRegistro);
                }

                if (!empty($datosAlumno['numero_identificador'])) {
                    $alumModel = Alumno::find($datosAlumno['numero_identificador']);
                    if ($alumModel) {
                        $alumModel->update($datosAlumno);
                    }
                    $datosBachillerato['nid'] = $datosAlumno['numero_identificador'];
                    AlumnoBachillerato::updateOrCreate(
                        ['nid' => $datosAlumno['numero_identificador']],
                        $datosBachillerato
                    );
                }

                return response()->json(['status' => 'success']);
            } else {
                $mensajes = "";
                for ($i = 1; $i <= 4; $i++) {
                    if (!empty($errores[$i])) {
                        $mensajes .= $errores[$i] . "\n";
                    }
                }
                return response($mensajes, 400);
            }
        }

        $infoAlumno = DB::table('historias')
            ->where('id_escuela', $id_escuela)
            ->where('captura', $this->captura_actual)
            ->where('id_registro', $id_registro)
            ->first();

        if (!$infoAlumno) {
            return response('Registro no encontrado', 404);
        }

        $infoAlumno2      = Alumno::where('numero_identificador', $infoAlumno->nid)->first();
        $infoBachillerato = AlumnoBachillerato::where('nid', $infoAlumno->nid)->first();
        $infoBecado       = BecasPractica::where('nid', $infoAlumno->nid)->first();

        $municipio_nacimiento = Municipio::where('id_entidad', $infoAlumno->entidad_nacimiento ?? 0)->pluck('municipio', 'id');
        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id')->toArray();
        $entidades[33] = 'Extranjero';
        $municipios_bach = Municipio::where('id_entidad', $infoBachillerato->id_entidad ?? 0)->pluck('municipio', 'id');
        $bachilleratos = Bachillerato::where('id_municipio2', $infoBachillerato->id_municipio ?? 0)->pluck('nombre', 'id')->toArray();
        $bachilleratos[0] = 'otro';

        $especialAlumno = Historia::isEspecialStatic($infoAlumno->id_licenciatura, $infoAlumno->semestre, $infoAlumno->id_especialidad);

        $semestre = Historia::CalcularSemestreStatic($infoAlumno->semestre, $this->captura_actual, $nombreEntidad, $id_escuela);
        $modalidad = ['e' => 'Escolarizada', 'm' => 'Mixta'];
        $lista_turno = ['m' => 'Matutino', 'n' => 'Nocturno', 'v' => 'Vespertino', 'd' => 'Discontinuo'];
        $sexo = ['M' => 'Mujer', 'H' => 'Hombre'];
        $nacionalidad = ['m' => 'Mexicano', 'e' => 'Extranjero'];
        $sostenimiento_bach = ['p' => 'Público', 'q' => 'Privado'];
        $sino = ['s' => 'Sí', 'n' => 'No'];
        $discapacidad = Discapacidad::where('id', '>=', 5)->pluck('discapacidad', 'id');
        $documentos = Documento::pluck('documento', 'id');
        $entidad_nac = Entidad::pluck('entidad', 'id');
        $licenciatura = Licenciatura::pluck('licenciatura', 'id');
        $especialidad = Especialidad::pluck('especialidad', 'id');
        $paises = Pais::pluck('pais', 'id');
        $lenguas = Lengua::pluck('nombre', 'id');
        $necesidad = Necesidad::pluck('necesidad', 'id');

        $entidadesnal = EntidadNal::where('id_pais', $infoAlumno2->pais ?? 0)->pluck('nombre', 'id');
        $entidadesnal2 = EntidadNal::where('id_pais', $infoAlumno2->pais_residencia ?? 0)->pluck('nombre', 'id');
        $entidadesnal3 = EntidadNal::where('id_pais', $infoAlumno2->pais_procedencia ?? 0)->pluck('nombre', 'id');
        $municipios2 = Municipio::where('id_entidad', $infoAlumno2->entidad_domicilio ?? 0)->pluck('municipio', 'id');

        $vista = 'reinscribir_alumnos';
        if ($especialAlumno) {
            $vista = 'reinscribir_alumnos_especial';
            $escuelaEspecial = EscuelaLicenciatura::where('id_escuela', $id_escuela)->where('id_licenciatura', 7)->pluck('id_especialidad');
            $especialOpciones = [];
            foreach ($escuelaEspecial as $espId) {
                if (isset($especialidad[$espId])) {
                    $especialOpciones[$espId] = $especialidad[$espId];
                }
            }
            if ($infoAlumno->semestre < 4) {
                $especialOpciones[0] = 'n/a';
            }
            $especialidad = $especialOpciones;
        }

        $turno = [];
        if ($especialAlumno) {
            $especialTurno = EscuelaLicenciatura::where('id_escuela', $id_escuela)
                ->where('id_licenciatura', $infoAlumno->id_licenciatura)
                ->where('modalidad', $infoAlumno->modalidad)
                ->get(['turno']);
            foreach ($especialTurno as $t) {
                if (isset($lista_turno[$t->turno])) {
                    $turno[$t->turno] = $lista_turno[$t->turno];
                }
            }
        } else {
            $opcionesTurno = EscuelaLicenciatura::where('id_escuela', $id_escuela)
                ->where('id_licenciatura', $infoAlumno->id_licenciatura)
                ->where('id_especialidad', $infoAlumno->id_especialidad)
                ->where('modalidad', $infoAlumno->modalidad)
                ->get(['turno']);
            foreach ($opcionesTurno as $t) {
                if (isset($lista_turno[$t->turno])) {
                    $turno[$t->turno] = $lista_turno[$t->turno];
                }
            }
        }

        $becado = Historia::CriteriosBecadosStatic($sostenimiento, $infoAlumno->semestre, $infoAlumno->modalidad, $infoAlumno->id_licenciatura);

        return view('alumnos.' . $vista, compact(
            'infoAlumno', 'infoAlumno2', 'infoBachillerato', 'infoBecado',
            'municipio_nacimiento', 'entidades', 'municipios_bach', 'bachilleratos',
            'entidad_nac', 'licenciatura', 'nacionalidad', 'especialidad', 'discapacidad',
            'documentos', 'modalidad', 'semestre', 'turno', 'sexo', 'sino',
            'sostenimiento_bach', 'paises', 'lenguas', 'necesidad',
            'entidadesnal', 'entidadesnal2', 'entidadesnal3', 'municipios2', 'becado'
        ));
    }

    /**
     * actualizarAlumno
     * Muestra la información del alumno para editar según su nivel.
     */
    public function actualizarAlumno(Request $request)
    {
        $ctx           = $this->contexto();
        $id_escuela    = $ctx['id_escuela'] ?? null;
        $nivel         = $ctx['nivel'] ?? null;
        $nombreEntidad = $ctx['id_renapo'] ?? null;

        $escuela       = Escuela::find($id_escuela);
        $sostenimiento = $escuela->sostenimiento ?? null;

        $id_registro = $request->input('Historia.id_registro', $request->input('id_registro', $request->input('Registro.id')));

        if ($request->isMethod('post') && $request->has('Alumno')) {
            $datosAlumno   = $request->input('Alumno', []);
            $datosRegistro = $request->input('Registro', []);
            $datosBach     = $request->input('AlumnoBachillerato', []);

            $parametros = [
                'nid'             => $datosAlumno['numero_identificador'] ?? null,
                'id_registro'     => $datosRegistro['id'] ?? null,
                'id_licenciatura' => $datosRegistro['id_licenciatura'] ?? null,
                'id_especialidad' => $datosRegistro['id_especialidad'] ?? null,
                'matricula'       => $datosRegistro['matricula'] ?? null,
                'semestre'        => $datosRegistro['semestre'] ?? null,
                'id_escuela'      => $id_escuela,
                'curp'            => $datosAlumno['curp'] ?? null,
                'captura'         => $this->captura_actual
            ];

            $errores = Registro::inscripcionAlumnoStatic($parametros);

            if (!empty($datosAlumno) && !Alumno::validaNombreStatic($datosAlumno)) {
                return response("Error: Favor de quitar acentos o diéresis en el nombre: {$datosAlumno['nombre']} {$datosAlumno['apellido_paterno']} {$datosAlumno['apellido_materno']}", 400);
            }

            if (empty($errores)) {
                $datosCurp = Alumno::infoCurpStatic($datosAlumno['curp'] ?? '', $this->captura_actual);
                if (($datosCurp['dia'] ?? 0) > 31 || ($datosCurp['mes'] ?? 0) > 12) {
                    return response('Error en fecha CURP', 400);
                }
                $datosAlumno['fecha_nacimiento'] = ($datosCurp['anio'] ?? '') . '-' . ($datosCurp['mes'] ?? '') . '-' . ($datosCurp['dia'] ?? '');
                $datosAlumno['entidad_nacimiento'] = $datosCurp['entidad'] ?? '';

                $datosRegistro['apoyos'] = Registro::uneApoyosStatic($datosRegistro);

                if (($datosAlumno['discapacidad'] ?? 'n') === 'n') {
                    $datosAlumno['tipo_discapacidad'] = 0;
                }

                if (!empty($datosRegistro['id'])) {
                    Registro::where('id', $datosRegistro['id'])->update($datosRegistro);
                }

                if (!empty($datosAlumno['numero_identificador'])) {
                    Alumno::where('numero_identificador', $datosAlumno['numero_identificador'])->update($datosAlumno);
                    if (!empty($datosBach)) {
                        if (($datosBach['id_bachillerato'] ?? 0) != 0) {
                            $datosBach['otro'] = 'no';
                        }
                        $datosBach['nid'] = $datosAlumno['numero_identificador'];
                        AlumnoBachillerato::updateOrCreate(
                            ['nid' => $datosAlumno['numero_identificador']],
                            $datosBach
                        );
                    }
                }

                return response()->json(['status' => 'success']);
            } else {
                $mensajes = "";
                for ($i = 1; $i <= 7; $i++) {
                    if (!empty($errores[$i])) {
                        $mensajes .= $errores[$i] . "\n";
                    }
                }
                return response($mensajes, 400);
            }
        }

        $infoAlumno = Registro::with(['alumno', 'escuelaPractica', 'alumnoBachillerato'])
            ->where('id_escuela', $id_escuela)
            ->where('captura', $this->captura_actual)
            ->where('id', $id_registro)
            ->first();

        if (!$infoAlumno) {
            return response('Registro no encontrado', 404);
        }

        $infoBecado = BecasPractica::where('id_registro', $infoAlumno->id)->first();
        $infoBachillerato = AlumnoBachillerato::where('nid', $infoAlumno->nid)->first();

        $municipio_entidad = null;
        if ($infoBecado && $infoBecado->id_escuela_practica) {
            $municipio_entidad = EscuelaPractica::where('id', $infoBecado->id_escuela_practica)->first(['id_entidad', 'id_municipio']);
        }

        $becado = Historia::CriteriosBecadosStatic($sostenimiento, $infoAlumno->semestre, $infoAlumno->modalidad, $infoAlumno->id_licenciatura);

        if (!empty($infoAlumno->apoyos)) {
            $apoyos = Registro::separaApoyosStatic($infoAlumno->apoyos);
            foreach ($apoyos as $apoyo => $val) {
                $infoAlumno->setAttribute($apoyo, $val);
            }
        }

        $datosCurp = Alumno::infoCurpStatic($infoAlumno->alumno->curp ?? '', $this->captura_actual);
        if ($infoAlumno->alumno) {
            $infoAlumno->alumno->id_renapo = $datosCurp['renapo'] ?? '';
        }

        $lista_turno = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];
        $lista_modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'Intensiva'];
        $discapacidad = Discapacidad::where('id', '>=', 5)->pluck('discapacidad', 'id');
        $documentos = Documento::pluck('documento', 'id');
        $entidad_nacimiento = Entidad::pluck('entidad', 'id_renapo');
        $entidad_nac = Entidad::pluck('entidad', 'id');
        $entidades = Entidad::where('id', '<=', 33)->pluck('entidad', 'id')->toArray();
        $municipios_nacimiento = Municipio::where('id_entidad', $infoAlumno->alumno->entidad_nacimiento ?? 0)->pluck('municipio', 'id');
        $municipios_bach = Municipio::where('id_entidad', $infoBachillerato->id_entidad ?? 0)->pluck('municipio', 'id');
        $bachilleratos = Bachillerato::where('id_municipio2', $infoBachillerato->id_municipio ?? 0)->pluck('nombre', 'id')->toArray();
        $bachilleratos[0] = 'otro';

        $entidadesnal = EntidadNal::where('id_pais', $infoAlumno->alumno->pais ?? 0)->pluck('nombre', 'id');
        $entidadesnal2 = EntidadNal::where('id_pais', $infoAlumno->alumno->pais_residencia ?? 0)->pluck('nombre', 'id');
        $entidadesnal3 = EntidadNal::where('id_pais', $infoAlumno->alumno->pais_procedencia ?? 0)->pluck('nombre', 'id');
        $municipios2 = Municipio::where('id_entidad', $infoAlumno->alumno->entidad_domicilio ?? 0)->pluck('municipio', 'id');
        $paises = Pais::pluck('pais', 'id');
        $lenguas = Lengua::pluck('nombre', 'id');
        $necesidad = Necesidad::pluck('necesidad', 'id');

        $especialAlumno = Historia::isEspecialStatic($infoAlumno->id_licenciatura, $infoAlumno->semestre, $infoAlumno->id_especialidad);
        $turnos = [];
        if ($especialAlumno) {
            $especialTurno = EscuelaLicenciatura::where('id_escuela', $id_escuela)
                ->where('id_licenciatura', $infoAlumno->id_licenciatura)
                ->where('modalidad', $infoAlumno->modalidad)
                ->get(['turno']);
            foreach ($especialTurno as $t) {
                if (isset($lista_turno[$t->turno])) {
                    $turnos[$t->turno] = $lista_turno[$t->turno];
                }
            }
        } else {
            $opcionesTurno = EscuelaLicenciatura::where('id_escuela', $id_escuela)
                ->where('id_licenciatura', $infoAlumno->id_licenciatura)
                ->where('id_especialidad', $infoAlumno->id_especialidad)
                ->where('modalidad', $infoAlumno->modalidad)
                ->get(['turno']);
            foreach ($opcionesTurno as $t) {
                if (isset($lista_turno[$t->turno])) {
                    $turnos[$t->turno] = $lista_turno[$t->turno];
                }
            }
        }

        switch ($nivel) {
            case 2:
                $semestre = Historia::CalcSemSinLimitStatic($infoAlumno->semestre, $this->captura_actual);
                $opcLicenciaturas = EscuelaLicenciatura::with(['licenciatura', 'especialidad'])
                    ->where('id_escuela', $id_escuela)
                    ->get();
                $licenciatura = [];
                $especialidad = [];
                foreach ($opcLicenciaturas as $ol) {
                    if ($ol->licenciatura) $licenciatura[$ol->id_licenciatura] = $ol->licenciatura->licenciatura;
                    if ($ol->especialidad) $especialidad[$ol->id_especialidad] = $ol->especialidad->especialidad;
                }
                if ($especialAlumno && $infoAlumno->semestre < 4) {
                    $especialidad[0] = 'n/a';
                }
                $modalidad = [$infoAlumno->modalidad => $lista_modalidades[$infoAlumno->modalidad] ?? ''];
                break;
            case 3:
                $lics = Licenciatura::pluck('licenciatura', 'id');
                $esps = Especialidad::pluck('especialidad', 'id');
                $licenciatura = [$infoAlumno->id_licenciatura => $lics[$infoAlumno->id_licenciatura] ?? ''];
                $especialidad = [$infoAlumno->id_especialidad => $esps[$infoAlumno->id_especialidad] ?? ''];
                $modalidad = [$infoAlumno->modalidad => $lista_modalidades[$infoAlumno->modalidad] ?? ''];
                $semestre = Historia::CalcularSemestreStatic($infoAlumno->semestre, $this->captura_actual, $nombreEntidad, $id_escuela);
                break;
            case 4:
                $licenciatura = Licenciatura::pluck('licenciatura', 'id');
                $especialidad = Especialidad::pluck('especialidad', 'id');
                $modalidad = [$infoAlumno->modalidad => $lista_modalidades[$infoAlumno->modalidad] ?? ''];
                $semestre = Historia::CalcularSemestreStatic($infoAlumno->semestre, $this->captura_actual, $nombreEntidad, $id_escuela);
                break;
        }

        $permiso = Historia::permisosUsuariosStatic($nivel);

        return view('alumnos.actualizar_alumno', compact(
            'infoAlumno', 'infoBecado', 'infoBachillerato', 'entidad_nacimiento',
            'entidad_nac', 'entidades', 'municipios_nacimiento', 'municipios_bach',
            'bachilleratos', 'permiso', 'modalidad', 'licenciatura', 'especialidad',
            'discapacidad', 'documentos', 'semestre', 'turnos', 'becado',
            'paises', 'lenguas', 'necesidad', 'entidadesnal', 'entidadesnal2',
            'entidadesnal3', 'municipios2'
        ));
    }

    /**
     * opcionesAltaAlumno
     */
    public function opcionesAltaAlumno()
    {
        $ctx           = $this->contexto();
        $id_escuela    = $ctx['id_escuela'] ?? null;
        $nivel         = $ctx['nivel'] ?? null;

        if ($nivel != 4) {
            return redirect()->route('alumnos.index');
        }

        $opciones = EscuelaLicenciatura::with('licenciatura')
            ->where('id_escuela', $id_escuela)
            ->whereIn('id_licenciatura', [6, 37, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60])
            ->get();

        $licenciaturas = [];
        foreach ($opciones as $op) {
            if ($op->licenciatura) {
                $licenciaturas[$op->id_licenciatura] = $op->licenciatura->licenciatura;
            }
        }

        $i = 1;
        $semestre = [];
        if ($id_escuela == 545 || $id_escuela == 664) {
            $semestre[$i] = $i;
        } else {
            $paridad = ((int) $this->captura_actual) % 2;
            if ($i % 2 == $paridad) {
                $semestre[$i] = $i;
            }
        }

        $anterior = route('alumnos.index');

        return view('alumnos.opciones_alta_alumno', compact('semestre', 'licenciaturas', 'anterior'));
    }

    /**
     * altaAlumno
     */
    public function altaAlumno(Request $request)
    {
        $ctx        = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $opcionesCreacion = $request->input('Vuelta', []);

        if ($request->isMethod('post') && $request->has('Registro')) {
            $datosAlumno   = $request->input('Alumno', []);
            $datosRegistro = $request->input('Registro', []);
            $datosBach     = $request->input('AlumnoBachillerato', []);

            $datosRegistro['captura'] = $this->captura_actual;
            $datosRegistro['id_escuela'] = $id_escuela;
            $datosRegistro['status'] = 'i';

            if (!empty($datosAlumno) && !Alumno::validaNombreStatic($datosAlumno)) {
                return response("Error: Caracteres no permitidos en el nombre: {$datosAlumno['nombre']} {$datosAlumno['apellido_paterno']} {$datosAlumno['apellido_materno']}", 400);
            }

            if (!empty($datosAlumno) && !Alumno::validaEntidadStatic($datosAlumno)) {
                return response("Error: Favor de revisar la entidad de nacimiento: {$datosAlumno['id_renapo']} , no coincide con la entidad en curp", 400);
            }

            $parametros = [
                'curp'            => $datosAlumno['curp'] ?? null,
                'id_licenciatura' => $datosRegistro['id_licenciatura'] ?? null,
                'id_especialidad' => $datosRegistro['id_especialidad'] ?? null,
                'matricula'       => $datosRegistro['matricula'] ?? null,
                'semestre'        => $datosRegistro['semestre'] ?? null,
                'id_escuela'      => $id_escuela,
                'captura'         => $this->captura_actual
            ];

            [$errores, $nid] = Registro::altaAlumnoStatic($parametros);

            if (empty($errores)) {
                $datosCurp = Alumno::infoCurpStatic($datosAlumno['curp'] ?? '', $this->captura_actual);
                if (($datosCurp['dia'] ?? 0) > 31 || ($datosCurp['mes'] ?? 0) > 12) {
                    return response('Error fecha CURP', 400);
                }
                $datosAlumno['fecha_nacimiento'] = ($datosCurp['anio'] ?? '') . '-' . ($datosCurp['mes'] ?? '') . '-' . ($datosCurp['dia'] ?? '');
                $datosAlumno['entidad_nacimiento'] = $datosCurp['entidad'] ?? '';
                $datosRegistro['apoyos'] = Registro::uneApoyosStatic($datosRegistro);

                if (($datosAlumno['discapacidad'] ?? 'n') === 'n') {
                    $datosAlumno['tipo_discapacidad'] = 0;
                }

                if (!empty($nid)) {
                    if (count($nid) == 1) {
                        $alumId = $nid[0]->numero_identificador ?? $nid[0]['Alumno']['numero_identificador'] ?? null;
                        if ($alumId) {
                            Alumno::where('numero_identificador', $alumId)->update(array_intersect_key($datosAlumno, array_flip(['municipio_nacimiento', 'fecha_nacimiento', 'discapacidad', 'tipo_discapacidad'])));
                            $datosRegistro['nid'] = $alumId;
                            $datosBach['nid'] = $alumId;
                            if (($datosBach['id_bachillerato'] ?? 0) != 0) {
                                $datosBach['otro'] = 'no';
                            }
                            Registro::create($datosRegistro);
                            AlumnoBachillerato::create($datosBach);
                        }
                        return response()->json(['status' => 'success']);
                    } else {
                        return response('Error: Múltiples NIDs encontrados', 400);
                    }
                } else {
                    $alumnoNid = Alumno::generarNidStatic($this->captura_actual);
                    $datosAlumno['numero_identificador'] = $alumnoNid;
                    $datosRegistro['nid'] = $alumnoNid;
                    $datosBach['nid'] = $alumnoNid;

                    if (($datosBach['id_bachillerato'] ?? 0) != 0) {
                        $datosBach['otro'] = 'no';
                    }

                    try {
                        Alumno::create($datosAlumno);
                        Registro::create($datosRegistro);
                        AlumnoBachillerato::create($datosBach);
                    } catch (\Exception $e) {
                        return response('Error: Inténtelo de nuevo, por favor', 400);
                    }
                }
            } else {
                $mensajes = "";
                for ($i = 1; $i <= 4; $i++) {
                    if (!empty($errores[$i])) {
                        $mensajes .= $errores[$i] . "\n";
                    }
                }
                return response($mensajes, 400);
            }
        }

        $licenciatura = Licenciatura::where('id', $opcionesCreacion['id_licenciatura'] ?? null)->pluck('licenciatura', 'id');
        $especialidad = Especialidad::where('id', $opcionesCreacion['id_especialidad'] ?? null)->pluck('especialidad', 'id');
        $turnos = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno'];
        $modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'Intensiva'];
        $discapacidad = Discapacidad::where('id', '>=', 5)->pluck('discapacidad', 'id');
        $generos = ['H' => 'Hombre', 'M' => 'Mujer'];
        $nacionalidad = ['m' => 'Mexicano(a)', 'e' => 'Extranjero(a)'];
        $documentos = Documento::pluck('documento', 'id');
        $entidadNac = Entidad::pluck('entidad', 'id_renapo');
        $municipioNac = Municipio::pluck('municipio', 'id');
        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id')->toArray();
        $entidades[33] = 'Extranjero';
        $paises = Pais::pluck('pais', 'id');
        $lenguas = Lengua::pluck('nombre', 'id');
        $necesidad = Necesidad::pluck('necesidad', 'id');

        return view('alumnos.alta_alumno', compact(
            'licenciatura', 'especialidad', 'turnos', 'modalidades',
            'discapacidad', 'generos', 'nacionalidad', 'documentos',
            'entidadNac', 'municipioNac', 'entidades', 'opcionesCreacion',
            'paises', 'lenguas', 'necesidad'
        ));
    }

    /**
     * opcionCambioEscuela
     */
    public function opcionCambioEscuela()
    {
        $ctx        = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel      = $ctx['nivel'] ?? null;

        if ($nivel != 4) {
            return redirect()->route('alumnos.index');
        }

        $opciones = EscuelaLicenciatura::with('licenciatura')
            ->where('id_escuela', $id_escuela)
            ->get();

        $licenciaturas = [];
        foreach ($opciones as $op) {
            if ($op->licenciatura) {
                $licenciaturas[$op->id_licenciatura] = $op->licenciatura->licenciatura;
            }
        }

        $anterior = route('alumnos.index');

        return view('alumnos.opcion_cambio_escuela', compact('licenciaturas', 'anterior'));
    }

    /**
     * alumnoCambioEscuela
     */
    public function alumnoCambioEscuela(Request $request)
    {
        $ctx        = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura    = $this->captura_actual;

        $criterios = $request->input('Alumno', []);
        $nid = $criterios['numero_identificador'] ?? null;

        $infoNid = Historia::where('nid', $nid)
            ->where('captura', $captura)
            ->where('id_escuela', '!=', $id_escuela)
            ->whereNotIn('status', ['i', 'd'])
            ->get();

        $listaEscuela = Escuela2::where('unica', '<', 2)
            ->where('status', 'a')
            ->where('modulo', 'l')
            ->get(['id', 'escuela', 'id_entidad']);

        $entidad = Entidad::where('id', '<', 33)->pluck('entidad', 'id');
        $licenciatura = Licenciatura::pluck('licenciatura', 'id');
        $especialidad = Especialidad::pluck('especialidad', 'id');
        $lista_modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'Intensiva'];

        $escuelaActual = Escuela2::find($id_escuela);
        $soste1 = $escuelaActual->sostenimiento ?? null;

        $escuelasMap = [];
        foreach ($listaEscuela as $esc) {
            $escuelasMap[$esc->id] = [
                'escuela'    => $esc->escuela,
                'id_entidad' => $esc->id_entidad,
                'entidad'    => $entidad[$esc->id_entidad] ?? ''
            ];
        }

        $alumnos = [];
        foreach ($infoNid as $alumno) {
            $indx = $alumno->id_registro;
            $alum_lic = $alumno->id_licenciatura;
            $escInfo = $escuelasMap[$alumno->id_escuela] ?? [];

            $dataHist = $alumno->toArray();
            $dataHist['escuela']    = $escInfo['escuela'] ?? '';
            $dataHist['entidad']    = $escInfo['entidad'] ?? '';
            $dataHist['id_entidad'] = $escInfo['id_entidad'] ?? '';

            $sostEsc2 = Escuela2::find($alumno->id_escuela);
            $soste2 = $sostEsc2->sostenimiento ?? null;

            $elegirButton = true;
            $parametros = "data[Registro][id]=" . $alumno->id_registro;

            $url = route('alumnos.cambioEscuela');
            if ($elegirButton) {
                $evento = "enviaBusqueda('$url','$parametros', this.id);";
                $boton = '<input id="' . $alumno->id_registro . '" type="button" value="Cambio s/conflicto" onclick="' . $evento . '" class="boton__primario" style="padding: 4px 10px; font-size: 0.8rem; cursor: pointer;"/>';
            } else {
                $parametros .= "&data[Conflicto][id_escuela]=" . $alumno->id_escuela;
                $evento = "enviaBusqueda('$url','$parametros', this.id);";
                $boton = '<input id="' . $alumno->id_registro . '" type="button" value="Cambio c/conflicto" onclick="' . $evento . '" class="boton__secundario" style="background-color: var(--error, #dc3545); color: #fff; padding: 4px 10px; font-size: 0.8rem; cursor: pointer;" />';
            }

            if (($alumno->semestre < 9 && $alumno->modalidad == 'e') || ($alumno->semestre < 13 && $alumno->modalidad == 'm')) {
                if ($alumno->status_conflictos != 'n') {
                    $dataHist['boton'] = $boton;
                } else {
                    $dataHist['boton'] = 'Cambio inválido';
                }
            }

            $alumnos[$indx] = $dataHist;
        }

        $cabeceraEscuela = ['Primer Apellido', 'Segundo Apellido', 'Nombre', 'Licenciatura', 'Especialidad', 'Modalidad', 'Semestre', 'Entidad', 'Acción'];

        return view('alumnos.alumno_cambio_escuela', compact(
            'cabeceraEscuela', 'alumnos', 'lista_modalidades', 'licenciatura',
            'especialidad', 'soste1', 'soste2', 'alum_lic'
        ));
    }

    /**
     * resolverConflictos
     */
    public function resolverConflictos(Request $request, $id_registro = null)
    {
        if (!$id_registro) {
            return redirect()->route('alumnos.index');
        }

        $conflicto = Conflicto::where('id_registro', $id_registro)->first();
        if ($conflicto && $conflicto->status == 's') {
            return redirect()->route('conflictos.index');
        }

        $alumno = Registro::with('alumno')->where('id', $id_registro)->where('captura', $this->captura_actual)->first();

        if ($request->isMethod('post') && $request->has('Registro')) {
            $dataReg = $request->input('Registro', []);
            if (!empty($dataReg['id']) && $conflicto && !empty($conflicto->id)) {
                Registro::where('id', $dataReg['id'])->update($dataReg);
                Conflicto::where('id', $conflicto->id)->update(['status' => 's']);
            } else {
                return response('Ha ocurrido un error', 400);
            }
        }

        $id_escuela_origen = $conflicto->id_escuela ?? null;
        $id_escuela_destino = $alumno->id_escuela ?? null;

        $escuelas = Escuela::whereIn('id', [$id_escuela_origen, $id_escuela_destino])->pluck('escuela', 'id');
        $turnos = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno'];
        $modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'Intensiva'];
        $generos = ['M' => 'Mujer', 'H' => 'Hombre'];
        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');
        $especialidades = Especialidad::pluck('especialidad', 'id');
        $semestre = Historia::CalcSemSinLimitStatic($alumno->semestre ?? 1, $this->captura_actual);

        $anterior = route('conflictos.index');

        return view('alumnos.resolver_conflictos', compact(
            'alumno', 'escuelas', 'turnos', 'modalidades', 'generos',
            'licenciaturas', 'especialidades', 'semestre', 'id_registro',
            'conflicto', 'id_escuela_origen', 'id_escuela_destino', 'anterior'
        ));
    }

    /**
     * cambioEscuela
     */
    public function cambioEscuela(Request $request)
    {
        $ctx        = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $idRegistro = $request->input('Registro.id');
        if ($idRegistro) {
            Registro::where('id', $idRegistro)->update([
                'id_escuela' => $id_escuela,
                'status'     => 'x'
            ]);
        }

        $conflictoData = $request->input('Conflicto', []);
        if (!empty($conflictoData)) {
            $conflictoData['status'] = 'n';
            $conflictoData['id_registro'] = $idRegistro;
            Conflicto::create($conflictoData);
        }

        return view('elements.cambioEscuela');
    }

    /* =========================================================
     * MÉTODOS AJAX Y SELECTORES
     * ========================================================= */

    public function municipiosOpciones(Request $request)
    {
        $id_entidad = $request->input('EscuelaPractica.id_entidad');
        $opciones = Municipio::where('id_entidad', $id_entidad)->orderBy('municipio')->pluck('municipio', 'id');
        return view('elements.opciones2', compact('opciones'));
    }

    public function municipiosBachilleratos(Request $request)
    {
        $id_entidad = $request->input('AlumnoBachillerato.id_entidad');
        $opciones = Municipio::where('id_entidad', $id_entidad)->orderBy('municipio')->pluck('municipio', 'id');
        return view('elements.opciones2', compact('opciones'));
    }

    public function municipiosNacimiento(Request $request)
    {
        $entidadCurp = $request->input('Alumno.id_renapo');
        $entidad = Entidad::where('id_renapo', $entidadCurp)->first();
        $idEntidad = $entidad ? $entidad->id : null;

        $opciones = Municipio::where('id_entidad', $idEntidad)->orderBy('municipio')->pluck('municipio', 'id');
        return view('elements.opciones2', compact('opciones'));
    }

    public function municipiosNacimiento2(Request $request)
    {
        $entidad_nacimiento = $request->input('Alumno.id_renapo');
        $opciones = Municipio::where('id_entidad', $entidad_nacimiento)->orderBy('municipio')->pluck('municipio', 'id');
        return view('elements.opciones2', compact('opciones'));
    }

    public function municipiosNacimiento3(Request $request)
    {
        $pais = $request->input('Alumno.pais');
        $opciones = EntidadNal::where('id_pais', $pais)->orderBy('nombre')->pluck('nombre', 'id');
        return view('elements.opciones2', compact('opciones'));
    }

    public function municipiosNacimiento4(Request $request)
    {
        $pais = $request->input('Alumno.pais_residencia');
        $opciones = EntidadNal::where('id_pais', $pais)->orderBy('nombre')->pluck('nombre', 'id');
        return view('elements.opciones2', compact('opciones'));
    }

    public function municipiosNacimiento5(Request $request)
    {
        $pais = $request->input('Alumno.pais_procedencia');
        $opciones = EntidadNal::where('id_pais', $pais)->orderBy('nombre')->pluck('nombre', 'id');
        return view('elements.opciones2', compact('opciones'));
    }

    public function municipiosNacimiento6(Request $request)
    {
        $entidad = $request->input('Alumno.entidad_domicilio');
        $opciones = Municipio::where('id_entidad', $entidad)->orderBy('municipio')->pluck('municipio', 'id');
        return view('elements.opciones2', compact('opciones'));
    }

    public function escuelasPracticaOpciones(Request $request)
    {
        $id_municipio = $request->input('EscuelaPractica.id_municipio');
        $escuelas = EscuelaPractica::where('id_municipio', $id_municipio)->orderBy('escuela_practica')->get();
        $opciones = [];
        foreach ($escuelas as $esc) {
            $opciones[$esc->id] = $esc->escuela_practica . ' Clave: ' . $esc->clave_ct;
        }
        return view('elements.opciones2', compact('opciones'));
    }

    public function bachilleratoOpciones(Request $request)
    {
        $id_municipio = $request->input('AlumnoBachillerato.id_municipio');
        $bachilleratos = Bachillerato::where('id_municipio2', $id_municipio)->orderBy('nombre')->get();
        $opciones = [];
        foreach ($bachilleratos as $bach) {
            $opciones[$bach->id] = $bach->nombre . ' Clave: ' . $bach->clave;
        }
        $opciones[0] = 'Otro';
        return view('elements.opciones2', compact('opciones'));
    }

    public function EspecialLicOpciones()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $especialidadNombre = Especialidad::pluck('especialidad', 'id')->toArray();

        $ids = EscuelaLicenciatura::where('id_escuela', $id_escuela)->where('id_licenciatura', 7)->pluck('id_especialidad');
        $opciones = [];
        foreach ($ids as $espId) {
            if (isset($especialidadNombre[$espId])) {
                $opciones[$espId] = $especialidadNombre[$espId];
            }
        }
        return view('elements.opciones2', compact('opciones'));
    }

    public function especialidadOpciones(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $id_lic = $request->input('Registro.id_licenciatura');
        $semestre = $request->input('Registro.semestre', 1);

        $opciones = EscuelaLicenciatura::with('especialidad')
            ->where('id_escuela', $id_escuela)
            ->where('id_licenciatura', $id_lic)
            ->get()
            ->pluck('especialidad.especialidad', 'especialidad.id')
            ->toArray();

        if ($semestre < 4 && $id_lic == 7) {
            $opciones[0] = 'n/a';
        }

        return view('elements.opciones2', compact('opciones'));
    }

    public function modalidadOpciones(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $id_lic = $request->input('Registro.id_licenciatura');
        $id_esp = $request->input('Registro.id_especialidad');
        $semestre = $request->input('Registro.semestre', 1);

        $query = EscuelaLicenciatura::where('id_escuela', $id_escuela)->where('id_licenciatura', $id_lic);
        if ($id_esp > 0 || ($id_lic == 7 && $semestre >= 4)) {
            $query->where('id_especialidad', $id_esp);
        }

        $lista_modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'Intensiva'];
        $mods = $query->pluck('modalidad')->unique();
        $opciones = [];
        foreach ($mods as $m) {
            if (isset($lista_modalidades[$m])) {
                $opciones[$m] = $lista_modalidades[$m];
            }
        }

        return view('elements.opciones2', compact('opciones'));
    }

    public function turnoOpciones(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $id_lic = $request->input('Registro.id_licenciatura');
        $id_esp = $request->input('Registro.id_especialidad');
        $mod = $request->input('Registro.modalidad');
        $semestre = $request->input('Registro.semestre', 1);

        $query = EscuelaLicenciatura::where('id_escuela', $id_escuela)
            ->where('id_licenciatura', $id_lic)
            ->where('modalidad', $mod);

        if ($id_esp > 0 || ($id_lic == 7 && $semestre >= 4)) {
            $query->where('id_especialidad', $id_esp);
        }

        $lista_turno = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];
        $turns = $query->pluck('turno')->unique();
        $opciones = [];
        foreach ($turns as $t) {
            if (isset($lista_turno[$t])) {
                $opciones[$t] = $lista_turno[$t];
            }
        }

        return view('elements.opciones2', compact('opciones'));
    }

    public function semestreOpciones(Request $request)
    {
        $modalidad = $request->input('Registro.modalidad');
        $id_licenciatura = $request->input('Registro.id_licenciatura');
        $id_especialidad = $request->input('Registro.id_especialidad');
        $semestres = [];

        $paridad = ((int) $this->captura_actual) % 2;

        if ($modalidad === 'e') {
            if ($id_licenciatura == 7 && $id_especialidad == 0) {
                for ($i = 1; $i <= 3; $i++) {
                    if (($i % 2) === $paridad) {
                        $semestres[$i] = $i;
                    }
                }
            } else {
                for ($i = 1; $i < 9; $i++) {
                    if (($i % 2) === $paridad) {
                        $semestres[$i] = $i;
                    }
                }
            }
        } elseif ($modalidad === 'm') {
            for ($i = 1; $i < 13; $i++) {
                if (($i % 2) === $paridad) {
                    $semestres[$i] = $i;
                }
            }
        }

        $opciones = $semestres;
        return view('elements.opciones2', compact('opciones'));
    }

    public function actualizaRenglon(Request $request)
    {
        $info = $request->all();
        return view('elements.actualizarRenglon', compact('info'));
    }

    public function renglonEscuela()
    {
        $escuelaCambio = session('Usuario.escuela');
        return view('elements.actualizarRenglonEscuela', compact('escuelaCambio'));
    }

    public function renglonTexto()
    {
        $texto = session('Usuario.entidad');
        return view('elements.actualizarRenglonTexto', compact('texto'));
    }

    public function reglonesAlta(Request $request)
    {
        $renglon = [
            $request->input('Alumno.nombre'),
            $request->input('Alumno.apellido_paterno'),
            $request->input('Alumno.apellido_materno'),
            $request->input('Alumno.curp')
        ];
        return view('elements.renglon', compact('renglon'));
    }
}