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
     * Muestra la lista de escuelas de la entidad para enlaces.
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
     * Fija el id_escuela en la variable de sesión.
     */
    public function setIdEscuela(Request $request)
    {
        $id_escuela = $request->input('Escuela.id', $request->input('id_escuela'));
        session(['Usuario.id_escuela' => $id_escuela]);

        return response('');
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

        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $id_escuela ?? ($ctx['id_escuela'] ?? null);

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

        if ($opc !== 't') {
            $status = ['i' => 'Inscrito', 'x' => 'No Inscrito', 't' => 'Baja Temporal', 'd' => 'Baja Definitiva'];
            $url    = url("/alumnos/listasAlumnos/{$opc}/{$id_escuela}");
        } else {
            $status = ['i' => 'Inscrito', 'x' => 'No Inscrito'];
            $url    = url("/bajas/listasAlumnos/{$opc}/{$id_escuela}");
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
            'anterior'       => '/alumnos',
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

        $llenos = array_filter($buscaData, fn($v) => !is_null($v) && $v !== '');
        if (count($llenos) > 0) {
            foreach ($llenos as $campo => $valor) {
                $query->where($campo, $valor);
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
            ? url('/alumnos/reinscribirAlumnos') 
            : url('/alumnos/actualizarAlumno');
        
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
                $boton = '<input id="b_' . $alumno->id_registro . '" type="button" value="' . $titulo . '" onclick="' . $evento . '" />';
            }

            $accion = $boton;
            if ((($alumno->status === 'x' || $alumno->status === 't') && $opc === 'i') || 
                ($opc === 'e' && ($alumno->status === 'x' || $alumno->status === 'i'))) {
                if ($alumno->status_conflictos === 'n') {
                    $accion = '<div style="color:red;background-color:black;font-weight:900;">Conflicto</div>';
                }
            }

            switch ($alumno->status) {
                case 'x': $estilo = 'text-align:center;background-color:#cc0000;color:#ffcccc;font-weight:bold;'; $estado = 'No Insc.'; break;
                case 't': $estilo = 'text-align:center;background-color:#0066cc;color:#ccffff;font-weight:bold;'; $estado = 'B. Temp.'; break;
                case 'i': $estilo = 'text-align:center;background-color:#054205;color:#ccffcc;font-weight:bold;'; $estado = 'Insc.'; break;
                case 'd': $estilo = 'text-align:center;background-color:#ff9900;color:#663300;font-weight:bold;'; $estado = 'B. Def.'; break;
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
        $ctx           = $this->obtenerContextoUsuario();
        $id_escuela    = $ctx['id_escuela'] ?? null;
        $nombreEntidad = $ctx['id_renapo'] ?? null;

        $escuela       = Escuela::find($id_escuela);
        $sostenimiento = $escuela->sostenimiento ?? null;

        $id_registro = $request->input('data.Historia.id_registro', $request->input('Historia.id_registro', $request->input('id_registro')));

        if ($request->isMethod('post') && $request->has('Registro')) {
            $datosAlumno       = $request->input('Alumno', []);
            $datosRegistro     = $request->input('Registro', []);
            $datosBachillerato = $request->input('AlumnoBachillerato', []);

            $datosRegistro['status']              = 'i';
            $datosRegistro['fecha_actualizacion'] = now();

            $registroModel = Registro::find($datosRegistro['id'] ?? $id_registro);
            if ($registroModel) {
                $registroModel->update($datosRegistro);
            }

            if (!empty($datosAlumno['numero_identificador'])) {
                if (($datosAlumno['discapacidad'] ?? 'n') === 'n') {
                    $datosAlumno['tipo_discapacidad'] = 0;
                }
                Alumno::where('numero_identificador', $datosAlumno['numero_identificador'])->update($datosAlumno);

                if (!empty($datosBachillerato)) {
                    if (($datosBachillerato['id_bachillerato'] ?? 0) != 0) {
                        $datosBachillerato['otro'] = 'no';
                    }
                    $datosBachillerato['nid'] = $datosAlumno['numero_identificador'];
                    AlumnoBachillerato::updateOrCreate(
                        ['nid' => $datosAlumno['numero_identificador']],
                        $datosBachillerato
                    );
                }
            }

            return response()->json(['status' => 'success']);
        }

        $infoAlumno = DB::table('historias')
            ->where('id_escuela', $id_escuela)
            ->where('captura', $this->captura_actual)
            ->where('id_registro', $id_registro)
            ->first();

        if (!$infoAlumno) {
            return response('Registro no encontrado', 404);
        }

        $alumnoModel      = Alumno::where('numero_identificador', $infoAlumno->nid)->first();
        $infoBachillerato = AlumnoBachillerato::where('nid', $infoAlumno->nid)->first();
        $infoBecado       = BecasPractica::where('nid', $infoAlumno->nid)->first();

        $entidades      = Entidad::where('id', '<', 33)->pluck('entidad', 'id')->toArray();
        $entidades[33]  = 'Extranjero';
        $municipiosNac  = Municipio::where('id_entidad', $infoAlumno->entidad_nacimiento ?? 0)->pluck('municipio', 'id');
        $documentos     = Documento::pluck('documento', 'id');
        $licenciaturas  = Licenciatura::pluck('licenciatura', 'id');
        $especialidades = Especialidad::pluck('especialidad', 'id');
        $paises         = Pais::pluck('pais', 'id');
        $lenguas        = Lengua::pluck('nombre', 'id');
        $necesidades    = Necesidad::pluck('necesidad', 'id');
        $discapacidades = Discapacidad::where('id', '>=', 5)->pluck('discapacidad', 'id');

        $modalidades  = ['e' => 'Escolarizada', 'm' => 'Mixta'];
        $listaTurno   = ['m' => 'Matutino', 'n' => 'Nocturno', 'v' => 'Vespertino', 'd' => 'Discontinuo'];
        $sexos        = ['M' => 'Mujer', 'H' => 'Hombre'];
        $nacionalidad = ['m' => 'Mexicano', 'e' => 'Extranjero'];

        return view('alumnos.reinscribir_alumnos', compact(
            'infoAlumno', 'alumnoModel', 'infoBachillerato', 'infoBecado',
            'entidades', 'municipiosNac', 'documentos', 'licenciaturas',
            'especialidades', 'paises', 'lenguas', 'necesidades', 'discapacidades',
            'modalidades', 'listaTurno', 'sexos', 'nacionalidad'
        ));
    }

    /**
     * actualizarAlumno
     * Muestra la información del alumno para editar según su nivel.
     */
    public function actualizarAlumno(Request $request)
    {
        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel      = $ctx['nivel'] ?? null;

        if ($request->isMethod('post') && $request->has('Alumno.numero_identificador')) {
            $datosAlumno   = $request->input('Alumno', []);
            $datosRegistro = $request->input('Registro', []);

            if (!empty($datosAlumno['numero_identificador'])) {
                Alumno::where('numero_identificador', $datosAlumno['numero_identificador'])->update($datosAlumno);
            }
            if (!empty($datosRegistro['id'])) {
                Registro::where('id', $datosRegistro['id'])->update($datosRegistro);
            }

            return response()->json(['status' => 'success']);
        }

        $id_registro = $request->input('Historia.id_registro', $request->input('id_registro'));

        $infoAlumno = Registro::where('id_escuela', $id_escuela)
            ->where('captura', $this->captura_actual)
            ->where('id', $id_registro)
            ->first();

        $alumnoModel    = Alumno::where('numero_identificador', $infoAlumno->nid ?? null)->first();
        $entidades      = Entidad::where('id', '<', 33)->pluck('entidad', 'id')->toArray();
        $licenciaturas  = Licenciatura::pluck('licenciatura', 'id');
        $especialidades = Especialidad::pluck('especialidad', 'id');

        return view('alumnos.actualizar_alumno', compact(
            'infoAlumno', 'alumnoModel', 'entidades', 'licenciaturas', 'especialidades'
        ));
    }

    /**
     * opcionesAltaAlumno
     * Muestra opciones previas a la alta de alumnos.
     */
    public function opcionesAltaAlumno()
    {
        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel      = $ctx['nivel'] ?? null;

        if ($nivel != 4) {
            return redirect()->route('alumnos.index');
        }

        $opciones = EscuelaLicenciatura::with('licenciaturaModel')
            ->where('id_escuela', $id_escuela)
            ->whereIn('id_licenciatura', [6, 37, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60])
            ->get();

        $licenciaturas = [];
        foreach ($opciones as $op) {
            if ($op->licenciaturaModel) {
                $licenciaturas[$op->id_licenciatura] = $op->licenciaturaModel->licenciatura;
            }
        }

        $semestre = [];
        $i = 1;
        if ($id_escuela == 545 || $id_escuela == 664) {
            $semestre[$i] = $i;
        } else {
            $paridadCaptura = ((int) $this->captura_actual) % 2;
            if ($i % 2 == $paridadCaptura) {
                $semestre[$i] = $i;
            }
        }

        return view('alumnos.opciones_alta_alumno', compact('semestre', 'licenciaturas'));
    }

    /**
     * altaAlumno
     * Formulario y guardado de nuevo estudiante.
     */
    public function altaAlumno(Request $request)
    {
        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $opcionesCreacion = $request->input('Vuelta', []);

        if ($request->isMethod('post') && $request->has('Registro')) {
            $dataRegistro = $request->input('Registro', []);
            $dataAlumno   = $request->input('Alumno', []);
            $dataBach     = $request->input('AlumnoBachillerato', []);

            $dataRegistro['captura'] = $this->captura_actual;
            $dataRegistro['id_escuela'] = $id_escuela;
            $dataRegistro['status'] = 'i';

            if (($dataAlumno['discapacidad'] ?? 'n') === 'n') {
                $dataAlumno['tipo_discapacidad'] = 0;
            }

            $alumnoNid = Alumno::generateNid($this->captura_actual);
            $dataAlumno['numero_identificador'] = $alumnoNid;
            $dataRegistro['nid'] = $alumnoNid;
            $dataBach['nid'] = $alumnoNid;

            Alumno::create($dataAlumno);
            Registro::create($dataRegistro);
            if (($dataBach['id_bachillerato'] ?? 0) != 0) {
                $dataBach['otro'] = 'no';
            }
            AlumnoBachillerato::create($dataBach);

            return response()->json(['status' => 'success']);
        }

        $licenciatura = Licenciatura::where('id', $opcionesCreacion['id_licenciatura'] ?? null)->pluck('licenciatura', 'id');
        $especialidad = Especialidad::where('id', $opcionesCreacion['id_especialidad'] ?? null)->pluck('especialidad', 'id');
        $turnos       = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno'];
        $modalidades  = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'Intensiva'];
        $discapacidad = Discapacidad::where('id', '>=', 5)->pluck('discapacidad', 'id');
        $generos      = ['H' => 'Hombre', 'M' => 'Mujer'];
        $nacionalidad = ['m' => 'Mexicano(a)', 'e' => 'Extranjero(a)'];
        $documentos   = Documento::pluck('documento', 'id');
        $entidadNac   = Entidad::pluck('entidad', 'id_renapo');
        $municipioNac = Municipio::pluck('municipio', 'id');
        $entidades    = Entidad::where('id', '<', 33)->pluck('entidad', 'id')->toArray();
        $entidades[33] = 'Extranjero';
        $paises       = Pais::pluck('pais', 'id');

        return view('alumnos.alta_alumno', compact(
            'licenciatura', 'especialidad', 'turnos', 'modalidades',
            'discapacidad', 'generos', 'nacionalidad', 'documentos',
            'entidadNac', 'municipioNac', 'entidades', 'opcionesCreacion', 'paises'
        ));
    }

    /**
     * opcionCambioEscuela
     * Formulario para cambio de escuela.
     */
    public function opcionCambioEscuela()
    {
        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel      = $ctx['nivel'] ?? null;

        if ($nivel != 4) {
            return redirect()->route('alumnos.index');
        }

        $opciones = EscuelaLicenciatura::with('licenciaturaModel')
            ->where('id_escuela', $id_escuela)
            ->get();

        $licenciaturas = [];
        foreach ($opciones as $op) {
            if ($op->licenciaturaModel) {
                $licenciaturas[$op->id_licenciatura] = $op->licenciaturaModel->licenciatura;
            }
        }

        return view('alumnos.opcion_cambio_escuela', compact('licenciaturas'));
    }

    /**
     * alumnoCambioEscuela
     * Obtiene registros con el NID ingresado y compara criterios para cambio.
     */
    public function alumnoCambioEscuela(Request $request)
    {
        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura    = $this->captura_actual;

        $criterios = $request->input('Alumno', []);
        $nid       = $criterios['numero_identificador'] ?? null;

        $infoNid      = Historia::where('nid', $nid)
            ->where('captura', $captura)
            ->where('id_escuela', '!=', $id_escuela)
            ->whereNotIn('status', ['i', 'd'])
            ->get();

        $listaEscuela = Escuela2::where('unica', '<', 2)
            ->where('status', 'a')
            ->where('modulo', 'l')
            ->get(['id', 'escuela', 'id_entidad']);

        $entidad      = Entidad::where('id', '<', 33)->pluck('entidad', 'id');
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

            $elegirButton = true; // Lógica simplificada de validación
            $parametros   = "data[Registro][id]=" . $alumno->id_registro;

            if ($elegirButton) {
                $evento = "enviaBusqueda('" . url('/alumnos/cambioEscuela') . "','$parametros', this.id);";
                $boton  = '<input id="' . $alumno->id_registro . '" type="button" value="Cambio s/conflicto" onclick="' . $evento . '"/>';
            } else {
                $boton  = '<input id="' . $alumno->id_registro . '" style="background-color: #F08080;" type="button" value="Cambio c/conflicto"/>';
            }

            if (($alumno->semestre < 9 && $alumno->modalidad == 'e') || ($alumno->semestre < 13 && $alumno->modalidad == 'm')) {
                $dataHist['boton'] = ($alumno->status_conflictos != 'n') ? $boton : 'Cambio inválido';
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
     * Resuelve conflictos de cambio de escuela.
     */
    public function resolverConflictos(Request $request, $id_registro = null)
    {
        if (!$id_registro) {
            return redirect()->route('alumnos.index');
        }

        $conflicto = Conflicto::where('id_registro', $id_registro)->first();
        if ($conflicto && $conflicto->status == 's') {
            return redirect('/conflictos');
        }

        $alumno = Registro::with('alumno')->where('id', $id_registro)->where('captura', $this->captura_actual)->first();

        if ($request->isMethod('post') && $request->has('Registro')) {
            $dataReg = $request->input('Registro');
            if (!empty($dataReg['id'])) {
                Registro::where('id', $dataReg['id'])->update($dataReg);
                Conflicto::where('id_registro', $id_registro)->update(['status' => 's']);
            }
            return response()->json(['status' => 'success']);
        }

        $id_escuela_origen  = $conflicto->id_escuela ?? null;
        $id_escuela_destino = $alumno->id_escuela ?? null;

        $escuelas = Escuela::whereIn('id', [$id_escuela_origen, $id_escuela_destino])->pluck('escuela', 'id');
        $turnos   = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno'];
        $modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'Intensiva'];
        $generos  = ['M' => 'Mujer', 'H' => 'Hombre'];
        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');
        $especialidades = Especialidad::pluck('especialidad', 'id');

        return view('alumnos.resolver_conflictos', compact(
            'alumno', 'escuelas', 'turnos', 'modalidades', 'generos',
            'licenciaturas', 'especialidades', 'id_registro', 'conflicto',
            'id_escuela_origen', 'id_escuela_destino'
        ));
    }

    /**
     * cambioEscuela
     * Actualiza id_escuela en registros y crea conflicto si aplica.
     */
    public function cambioEscuela(Request $request)
    {
        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $idRegistro = $request->input('Registro.id');
        if ($idRegistro) {
            Registro::where('id', $idRegistro)->update([
                'id_escuela' => $id_escuela,
                'status'     => 'x'
            ]);
        }

        $conflictoData = $request->input('Conflicto');
        if (!empty($conflictoData)) {
            $conflictoData['status'] = 'n';
            $conflictoData['id_registro'] = $idRegistro;
            Conflicto::create($conflictoData);
        }

        return view('elements.cambioEscuela');
    }

    /* =========================================================
     * MÉTODOS AUXILIARES Y SELECTORES AJAX
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
        $ctx        = $this->obtenerContextoUsuario();
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
        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $id_lic     = $request->input('Registro.id_licenciatura');

        $opciones = EscuelaLicenciatura::with('especialidadModel')
            ->where('id_escuela', $id_escuela)
            ->where('id_licenciatura', $id_lic)
            ->get()
            ->pluck('especialidadModel.especialidad', 'especialidadModel.id')
            ->toArray();

        return view('elements.opciones2', compact('opciones'));
    }

    public function modalidadOpciones(Request $request)
    {
        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $id_lic     = $request->input('Registro.id_licenciatura');
        $id_esp     = $request->input('Registro.id_especialidad');

        $query = EscuelaLicenciatura::where('id_escuela', $id_escuela)->where('id_licenciatura', $id_lic);
        if ($id_esp > 0) {
            $query->where('id_especialidad', $id_esp);
        }

        $modalidadMap = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'Intensiva'];
        $mods = $query->pluck('modalidad')->unique();
        $opciones = [];
        foreach ($mods as $m) {
            if (isset($modalidadMap[$m])) {
                $opciones[$m] = $modalidadMap[$m];
            }
        }

        return view('elements.opciones2', compact('opciones'));
    }

    public function turnoOpciones(Request $request)
    {
        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $id_lic     = $request->input('Registro.id_licenciatura');
        $id_esp     = $request->input('Registro.id_especialidad');
        $mod        = $request->input('Registro.modalidad');

        $query = EscuelaLicenciatura::where('id_escuela', $id_escuela)
            ->where('id_licenciatura', $id_lic)
            ->where('modalidad', $mod);

        if ($id_esp > 0) {
            $query->where('id_especialidad', $id_esp);
        }

        $turnoMap = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];
        $turns = $query->pluck('turno')->unique();
        $opciones = [];
        foreach ($turns as $t) {
            if (isset($turnoMap[$t])) {
                $opciones[$t] = $turnoMap[$t];
            }
        }

        return view('elements.opciones2', compact('opciones'));
    }

    public function semestreOpciones(Request $request)
    {
        $modalidad       = $request->input('Registro.modalidad');
        $id_licenciatura = $request->input('Registro.id_licenciatura');
        $id_especialidad = $request->input('Registro.id_especialidad');
        $semestres       = [];

        $paridad = ((int) $this->captura_actual) % 2;

        if ($modalidad === 'e') {
            $limite = ($id_licenciatura == 7 && $id_especialidad == 0) ? 3 : 8;
            for ($i = 1; $i <= $limite; $i++) {
                if (($i % 2) === $paridad) {
                    $semestres[$i] = $i;
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

    public function renglonesAlta(Request $request)
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