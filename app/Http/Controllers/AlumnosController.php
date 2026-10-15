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

            $modalidadMap = ['e' => 'Escolarizada', 'm' => 'Mixta'];
            $turnoMap     = ['m' => 'Matutino', 'n' => 'Nocturno', 'v' => 'Vespertino','d' => 'Discontinuo'];

            $lic = [];
            $esp = [];
            $mod = [];
            $tur = [];

            foreach($licenciaturas as $l){
                if($l->licenciaturaModel){
                    $lic[$l->licenciaturaModel->id] = $l->licenciaturaModel->licenciatura;
                }
                if($l->especialidadModel){
                    $esp[$l->especialidadModel->id] = $l->especialidadModel->especialidad;
                }
                if(isset($modalidadMap[$l->modalidad])){
                    $mod[$l->modalidad] = $modalidadMap[$l->modalidad];
                }
                if(isset($turnoMap[$l->turno])){
                    $tur[$l->turno] = $turnoMap[$l->turno];
                }
            }

            $semestre = [];
            $paridadCaptura = ((int) $this->captura_actual) % 2;
            for($i = 1; $i <= 13; $i++){
                if(($i % 2) === $paridadCaptura){
                    $semestre[$i] = $i;
                }
            }
            if($opc !== 't'){
                $status = ['i' => 'Inscrito', 'x' => 'No Inscrito', 't' => 'Baja Temporal', 'd' => 'Baja Definitiva'];
                $url    = url("/alumnos/listasAlumnos/{$opc}/{$id_escuela}");
            }else {
                $status = ['i' => 'Inscrito', 'x' => 'No Inscrito'];
                $url    = url("/bajas/listasAlumnos/{$opc}/{$id_escuela}");
            }

            $tituloTabla = [
                'i' => 'Para la Reinscripción', 
                'e' => ' Para la Edición', 
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
                'anterior'       => '/alumnos'
            ]);
    }
    /** Retorna el componente AJAX con los resultados de la busqueda */
    public function listaAlumnos(Request $request, $opc = null,$id_escuela=null){
        $buscaData  = $request->input('busca', []);
        $id_escuela = $id_escuela ?? session('Usuario.id_escuela');

        $query = DB::table('historias')
            ->where('id_escuela', $id_escuela)
            ->where('captura', $this->captura_actual)
            ->where('status', '!=', 's');

        $llenos = array_filter($buscaData, fn($v) => !is_null($v) && $v !== '');
        if(count($llenos) > 0){
            foreach($llenos as $campo => $valor){
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
        $url = ($opc === '1')
            ? url('/alumnos/reinscribirAlumnos')
            : url('/alumnos/actualizarAlumno');
        $titulo = ($opc === 'i') ?  "Inscribir" : "Editar";

        $renglones = [];
        foreach($alumnos as $alumno){
            $boton = '';
            $parametros = "data[Historia][id_registro]=" . $alumno->id_registro;
            if ($alumno->status === 't' && $opc === 'i'){
                $evento = "confirmacion('$url', '$parametros');";
            } else {
                $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado);";
            }
            if(($alumno->modalidad === 'e' && $alumno->semestre < 9) || ($alumno->modalidad === 'm' && $alumno->semestre < 13)){
                 $boton = '<input id="b_'.$alumno->id_registro . '" type="button" value=' . $titulo . ' onclick="' . $evento . '" />';
            }
            $accion = $boton;
            if ((($alumno->status === 'x' || $alumno->status === 't') && $opc == 'i') || ($opc === 'e' && ($alumno->status === 'x' || $alumno->status === 'i'))) {
                if ($alumno->status_conflictos === 'n') {
                    $alumno['Historia']['accion'] = $boton;
                    $accion = '<div style="color:red;background-color:black;font-weight:900;">Conflicto</div>';
                }
            }
            switch($alumno->status){
                case 'x':
                    $estilo = 'text-align:center;background-color:#cc0000;color:#ffcccc;font-weight:bold;';
                    $estado = 'No Insc.';
                    break;
                case 't':
                    $estilo = 'text-align:center;background-color:#0066cc;color:#ccffff;font-weight:bold;';
                    $estado = 'B. Temp.';
                    break;
                case 'i':
                    $estilo = 'text-align:center;background-color:#054205;color:#ccffcc;font-weight:bold;';
                    $estado = 'Insc.';
                    break;
                case 'd':
                    $estilo = 'text-align:center;background-color:#ff9900;color:#663300;font-weight:bold;';
                    $estado = 'B. Def.';
                    break;
                default:
                    $estilo = '';
                    $estado = '';
                    break;
            }
            $statusHtml = "div style='{$estilo}'>{$estado}</div>";
            $renglones[$alumno->id_registro] = [
                'nid'              => $alumno->nid,
                'apellido_paterno' => $alumno->apellido_paterno,
                'apellido_materno' => $alumno->apellido_materno,
                'nombre'           => $alumno->nombre,
                'curp'             => $alumno->curp,
                'semestre'         => $alumno->semestre,
                'status'           => $statusHtml,
                'accion'           => $accion
            ];
        }
        $cabeceras = ['No.', 'nid', 'Primer Apellido', 'Segundo Apellido', 'Nombre', 'CURP', 'Sem', 'Estado', 'Acción'];
        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }
    /**muestra y procesa la reinscripcion de alumnos */

    public function reinscribirAlumnos(Request $request){
        $ctx           = $this->obtenerContextoUsuario();
        $id_escuela    = $ctx['id_escuela'] ?? null;
        $nombreEntidad = $ctx['id_renapo'] ?? null;

        $escuela       = Escuela::find($id_escuela);
        $sostenimiento = $escuela->sostenimiento ?? null;

        if($request->isMethod('post') && $request->has('Historia.id_registro')){
            $id_registro = $request->input('Historia.id_registro');

            $datosAlumno      = $request->input('Alumno', []);
            $datosRegistro     = $request->input('Registro', []);
            $datosBachillerato = $request->input('AlumnoBachillerato', []);
            $datosBecas        = $request->input('BecasPractica', []);

            $datosRegistro['status']              = 'i';
            $datosRegistro['fecha_actualizacion'] = now();

            $registroModel = Registro::find($datosRegistro['id'] ?? $id_registro);
            if($registroModel){
                $registroModel->update($datosRegistro);
            }
            if(!empty($datosAlumno['numero_identificador'])){
                if(($datosAlumno['discapacidad'] ?? 'n') === 'n'){
                    $datosAlumno['tipo_discapacidad'] = 0;
                }
                Alumno::where('numero_identificador', $datosAlumno['numero_identificador'])->update($datosAlumnos);

                if(!empty($datosBachillerato)){
                    if(($datosBachillerato['id_bachillerato'] ?? 0) != 0){
                        $datosBachillerato['otro'] = 'no';
                    }
                    $datosBachillerato['nid'] = $datosAlumno['numero_identificador'];
                    AlumnosBachillerato::updateOrCreate(
                        ['nid' => $datosAlumno[numero_identificador]],
                        $datosBachillerato
                    );
                }
            }
            return response()->json(['status' => 'success', 'message' => 'Alumno reinscrito correctamente']);
        }
        $id_registro = $request->input('data.Historia.id_registro', $request->input('id_registro'));

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
    /**muestra y actualiza los datos generales de un alumno existente */
    public function actualizarAlumno(Request $request){
        $ctx        = $this->obtenerContextoUsuario();
        $id_escuela = $ctx['id_escuela'] ?? null;

        if($request->isMethod('post') && $request->has('Alumno.numero_identificador')){
            $datosAlumno   = $request->input('Alumno', []);
            $datosRegistro = $request->input('Registro', []);

            if(!empty($datosAlumno['numero_identificador'])){
                Alumno::where('id', $datosAlumno['numero_identificador'])->update($datosAlumno);
            }
            if(!empty($datosRegistro['id'])){
                Registro::where('id', $datosRegistro['id'])->update($datosRegistro);
            }
            return response()->json(['status' => 'success', 'message' => 'Informacion actualizada correctamente']);
        }
        $id_registro = $request->input('Historia.id_registro', $request ->input('id_registro'));

        $infoAlumno = Registro::where('id_escuela', $id_escuela)
            ->where('captura', $this->captura_actual)
            ->where('id', $id_registro)
            ->first();
        
        $alumnoModel =Alumno::where('numero_identificador', $infoAlumno->nid ?? null)->first();
        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id')->toArray();
        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');
        $especialidades = Especialidades::pluck('especialidad', 'id');

        return view('alumnos.actualizar_alumno', compact(
            'infoAlumno',
            'alumnoModel',
            'entidades',
            'licenciaturas',
            'especialidades'
        ));
    }
}            