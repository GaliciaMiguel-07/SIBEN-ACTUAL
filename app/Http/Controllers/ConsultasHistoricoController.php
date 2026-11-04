<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entidad;
use App\Models\Funcion;
use App\Models\Escuela;
use App\Models\Escuela2;
use App\Models\Historia;
use App\Models\Personal;
use App\Models\Licenciatura;
use App\Models\Especialidad;
use App\Models\NivelEstudio;
use App\Models\ContratacionDocente;
use App\Models\FormaContratacion;
use App\Models\Categorias;
use App\Models\Asignatura;
use App\Models\AsignaturaDocente;
use App\Models\EscuelaLicenciatura;
use App\Models\NivelSni;
use App\Models\AreaConocimiento;
use App\Models\Docente;

class ConsultasHistoricoController extends sibenController {

    public function buscarAlumno(){
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');

        return view('consultas_historico.buscar_alumno', compact('nivel', 'entidades'));
    }
    public function buscarDocente(){
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $idEntidad = $ctx['id_entidad'] ?? null;
        $idEscuela = $ctx['id_escuela'] ?? null;

        $status = [
            'a'=>'Activo',
            'x'=>'Inactivo',
            'j'=>'Jubilado',
            'r'=>'Renuncia',
            'b'=>'Baja administrativa',
            'd'=>'Defuncion'
        ];
        $funciones = Funcion::pluck('fumcion', 'id');

        $entidades = [];
        $esculas = [];

        if($nivel == 1){
            $entidades = Entidades::where('id', '<', 33)->pluck('entidad', 'id');
        }elseif($nivel == 2 || $nivel == 6){
            $escuelas = Escuela::where('id_entidad', $idEntidad)->pluck('escuela', 'id');
        }elseif(in_array($nivel, [3,4,5])){
            $escuelas = Escuela::where('id', $idEscuela)->pluck('escuela', 'id');
        }
        return view('consultas_historico.buscar_docente', compact('nivel', 'idEntidad', 'idEscuela', 'funciones', 'status', 'entidades', 'escuelas'));
    }
    public function alumnos(Request $request){
        $entradas = $request->input('Historia', []);
        $condiciones = array_filter($entradas, fn($val) => !empty($val));

        $renglones = [];
        if(!empty($condiciones)){
            $query = Historia::query();

            foreach($condiciones as $col => $val){
                if($col !== 'nid'){
                    $query->where($col, $val);
                }
            }
            // Parche original para registros con nid vacío
            if (!isset($condiciones['nid'])) {
                $query->whereNotNull('nid')->where('nid', '<>', '');
            } else {
                $query->where('nid', $condiciones['nid']);
            }

            $alumnos = $query->select('nid as id', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp')
                ->groupBy('nid')
                ->orderBy('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre')
                ->get();

            $url = route('consultasHistorico.detalleAlumno');

            foreach ($alumnos as $alumno) {
                $parametros = "data[Historia][nid]=" . $alumno->id;
                $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado)";
                $boton = '<input id="' . $alumno->id . '" type="button" value="Detalle" onclick="' . $evento . '" />';

                $renglones[] = [
                    'id'               => $alumno->id,
                    'apellido_paterno' => $alumno->apellido_paterno,
                    'apellido_materno' => $alumno->apellido_materno,
                    'nombre'           => $alumno->nombre,
                    'curp'             => $alumno->curp,
                    'accion'           => $boton
                ];
            }
        }

        $cabeceras = ['No.', 'NID', 'Primer Apellido', 'Segundo Apellido', 'Nombre', 'Curp', 'Acción'];

        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }

    /**
     * docentes (Petición AJAX)
     */
    public function docentes(Request $request)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $id_entidad = $ctx['id_entidad'] ?? null;
        $id_escuela = $ctx['id_escuela'] ?? null;

        $inputPersonal = $request->input('Personal', []);

        if (in_array($nivel, [2, 6])) {
            $inputPersonal['id_entidad'] = $id_entidad;
        } elseif (in_array($nivel, [3, 4, 5])) {
            $inputPersonal['id_entidad'] = $id_entidad;
            $inputPersonal['id_escuela'] = $id_escuela;
        }

        $condiciones = array_filter($inputPersonal, fn($val) => !empty($val));

        $renglones = [];
        if (!empty($condiciones)) {
            $docentes = Personal::where($condiciones)
                ->select('id', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp')
                ->orderBy('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre')
                ->get();

            $url = route('consultasHistorico.detalleDocente');

            foreach ($docentes as $docente) {
                $parametros = "data[Personal][id]=" . $docente->id;
                $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado)";
                $boton = '<input id="' . $docente->id . '" type="button" value="Detalle" onclick="' . $evento . '" />';

                $renglones[] = [
                    'id'               => $docente->id,
                    'apellido_paterno' => $docente->apellido_paterno,
                    'apellido_materno' => $docente->apellido_materno,
                    'nombre'           => $docente->nombre,
                    'curp'             => $docente->curp,
                    'accion'           => $boton
                ];
            }
        }

        $cabeceras = ['No.', 'ID', 'Primer Apellido', 'Segundo Apellido', 'Nombre', 'Curp', 'Acción'];

        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }

    /**
     * detalleAlumno (AJAX)
     */
    public function detalleAlumno(Request $request, $nid = null)
    {
        if (!$nid) {
            $nid = $request->input('Historia.nid');
        }

        // Asumiendo que Historia tiene definidas las relaciones: licenciatura, especialidad, escuela
        $historia = Historia::with(['licenciatura', 'especialidad', 'escuela'])
            ->where('nid', $nid)
            ->select('nid', 'nombre', 'apellido_paterno', 'apellido_materno', 'curp', 'id_escuela', 'modalidad', 'semestre', 'status', 'captura', 'id_licenciatura', 'id_especialidad', 'matricula')
            ->get();

        $licenciaturas = [];
        $especialidades = [];
        $escuela_entidad = [];
        $nombres_escuelas = [];

        foreach ($historia as $registro) {
            if ($registro->licenciatura) {
                $licenciaturas[$registro->licenciatura->id] = $registro->licenciatura->licenciatura;
            }
            if ($registro->especialidad) {
                $especialidades[$registro->especialidad->id] = $registro->especialidad->especialidad;
            }
            if ($registro->escuela) {
                $escuela_entidad[$registro->escuela->id] = $registro->escuela->id_entidad;
                $nombres_escuelas[$registro->escuela->id] = $registro->escuela->escuela;
            }
        }

        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');

        return view('consultas_historico.detalle_alumno', compact(
            'historia', 'licenciaturas', 'especialidades', 'entidades', 'nombres_escuelas', 'escuela_entidad'
        ));
    }
    /**
     * detalleDocente (AJAX)
     */
    public function detalleDocente(Request $request, $id = null)
    {
        $id_personal = $request->input('Personal.id') ?? session('Personal.id');
        session()->forget('Personal.id');

        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;
        $nivel = $ctx['nivel'] ?? null;
        
        $title = 'Editar Personal';

        // Evitar uso de relaciones complejas, buscar manual para asimilar el unbindModel de Cake
        $personalObj = Personal::find($id_personal);
        $personal = $personalObj ? $personalObj->toArray() : [];
        $docente = Docente::where('id_personal', $id_personal)->first();
        if ($docente) $personal['Docente'] = $docente->toArray();

        $id_escuela = $personal['id_escuela'] ?? null;
        $escuela = Escuela::find($id_escuela);
        $id_entidad = $escuela->id_entidad ?? null;
        $entidad = Entidad::find($id_entidad);

        if (isset($personal['id_funcion']) && $personal['id_funcion'] <= 6 && isset($personal['Docente'])) {
            $disciplina = AreaConocimiento::find($personal['Docente']['id_area_conocimiento']);
            if ($disciplina) {
                $personal['Docente']['area_conocimiento'] = $disciplina->id_campo;
                $personal['Docente']['disciplina'] = $disciplina->id;
            }
            $personal['Docente']['investigacion'] = empty($personal['Docente']['proyecto_investigacion']) ? 'n' : 's';
            $personal['Docente']['lengua'] = empty($personal['Docente']['lengua_indigena']) ? 'n' : 's';
        }

        // Catálogos
        $niveles_estudio = NivelEstudio::pluck('nivel_estudio', 'id');
        $funciones = Funcion::pluck('funcion', 'id');
        $nivel_sni = NivelSni::pluck('nivel_sni', 'id');
        $sino = ['s' => 'Sí', 'n' => 'No'];
        $contador = $this->contador(50, false);
        $horas = $this->contador(30, false);

        $areasConocimiento = AreaConocimiento::groupBy('id_campo')->pluck('campo', 'id_campo');
        $disciplinas = AreaConocimiento::pluck('disciplina', 'id');
        $discapacidades = ['1' => 'Auditiva y de lenguaje', '2' => 'Intelectual', '3' => 'Motriz', '4' => 'Visual'];

        $edicion = in_array($nivel, [1, 3, 4]);
        $directivoDocente = isset($personal['id_funcion']) && $personal['id_funcion'] <= 4;
        $activaDocente = isset($personal['status']) && $personal['status'] == 'x';

        $mapaStatus = [
            'x' => 'Inactivo',
            'a' => 'Activo',
            'j' => 'Jubilado',
            'r' => 'Renuncia',
            'b' => 'Baja administrativa',
            'd' => 'Defuncion'
        ];
        $statusStr = $mapaStatus[$personal['status'] ?? 'a'] ?? 'Activo';

        $formas_contratacion = FormaContratacion::where('id', '<', 20)->pluck('contratacion', 'id');
        $tiempos_contratacion = FormaContratacion::where('id', '>', 20)->pluck('contratacion', 'id');

        $contrataciones = ContratacionDocente::where('id_personal', $id_personal)->get();
        $categoria = Categorias::pluck('nombre', 'id');
        $asignaturasDocente = AsignaturaDocente::where('id_docente', $id_personal)->get();
        
        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');
        $especialidades = Especialidad::pluck('especialidad', 'id');
        $asignaturasNoCurriculares = Asignatura::where('curricular', 'n')->where('semestre', 0)->pluck('asignatura', 'id');

        // Para inyectar datos principales como array nativo de la vista
        $data = $personal;

        return view('consultas_historico.detalle_docente', compact(
            'title', 'data', 'niveles_estudio', 'funciones', 'nivel_sni', 'sino', 'contador',
            'areasConocimiento', 'disciplinas', 'discapacidades', 'edicion', 'directivoDocente',
            'activaDocente', 'statusStr', 'id_personal', 'formas_contratacion', 'tiempos_contratacion',
            'contrataciones', 'horas', 'categoria', 'escuela', 'entidad', 'asignaturasDocente',
            'licenciaturas', 'especialidades', 'asignaturasNoCurriculares', 'nivel', 'personal'
        ));
    }

    /**
     * busquedaGlobal
     */
    public function busquedaGlobal(Request $request, $opc = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $id_escuela = $nivel > 2 ? ($ctx['id_escuela'] ?? null) : null;
        $id_entidad = $ctx['id_entidad'] ?? null;

        if (!$opc) {
            return redirect('/Alumnos');
        }

        $captura_actual = $this->captura_actual ?? 1; // Ajustar si es propiedad heredada

        $vista = "";
        $datosVista = [
            'anterior' => '/Alumnos',
            'opc' => $opc,
            'nivel' => $nivel
        ];

        if ($opc == 'g') {
            $vista = "consultas_historico.busqueda_global";
            
            $semestres = [];
            for ($i = 1; $i <= 13; $i++) {
                if ($i % 2 == $captura_actual % 2) {
                    $semestres[$i] = $i;
                }
            }
            
            $status = ['i' => 'Inscrito', 'x' => 'No Inscrito', 't' => 'Baja Temporal', 'd' => 'Baja Definitiva'];
            $lic = [];
            $esp = [];
            $mod = [];
            $tur = [];

            if ($nivel > 2) {
                // Relaciones manuales o eager loading
                $licenciaturasData = EscuelaLicenciatura::with(['licenciatura', 'especialidad'])
                    ->where('id_escuela', $id_escuela)->get();
                
                $modalidadList = ['e' => 'Escolarizada', 'm' => 'Mixta'];
                $turnoList = ['m' => 'Matutino', 'n' => 'Nocturno', 'v' => 'Vespertino'];

                foreach ($licenciaturasData as $registro) {
                    if ($registro->licenciatura) $lic[$registro->id_licenciatura] = $registro->licenciatura->licenciatura;
                    if ($registro->especialidad) $esp[$registro->id_especialidad] = $registro->especialidad->especialidad;
                    $mod[$registro->modalidad] = $modalidadList[$registro->modalidad] ?? '';
                    $tur[$registro->turno] = $turnoList[$registro->turno] ?? '';
                }
            } else {
                $escuelas = Escuela2::where('id_entidad', $id_entidad)
                    ->where('status', 'a')
                    ->where('unica', '<', 2)
                    ->where('modulo', 'l')
                    ->pluck('escuela', 'id');
                $datosVista['escuelas'] = $escuelas;
            }

            $datosVista['licenciaturas'] = $lic;
            $datosVista['especialidades'] = $esp;
            $datosVista['modalidades'] = $mod;
            $datosVista['turnos'] = $tur;
            $datosVista['semestres'] = $semestres;
            $datosVista['status'] = $status;
        } elseif ($opc == 'p') {
            $vista = "consultas_historico.busqueda_individual";
            $datosVista['url'] = route('consultasHistorico.alumnos');
        }

        return view($vista, $datosVista);
    }

    /**
     * listaGlobal
     */
    public function listaGlobal(Request $request)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        
        $historiaInput = $request->input('Historia', []);
        
        $id_escuela = $nivel > 2 ? ($ctx['id_escuela'] ?? null) : ($historiaInput['id_escuela'] ?? null);
        unset($historiaInput['id_escuela']);

        $captura_actual = $this->captura_actual ?? 1;

        $condicionesFind = [
            'id_escuela' => $id_escuela,
            'captura'    => $captura_actual
        ];

        $vacios = 0;
        foreach ($historiaInput as $val) {
            if (!$val) $vacios++;
        }

        $datoAlumno = [];
        
        if ($vacios < 8) {
            foreach ($historiaInput as $indx => $valor) {
                if (!empty($valor)) {
                    $condicionesFind[$indx] = $valor;
                }
            }

            $alumnos = Historia::where($condicionesFind)
                ->select('nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp')
                ->orderBy('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre')
                ->get();

            $url = route('consultasHistorico.detalleAlumno');

            foreach ($alumnos as $alumno) {
                $boton = "";
                if (!empty($alumno->nid)) {
                    $parametros = "data[Historia][nid]=" . $alumno->nid;
                    $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado)";
                    $boton = '<input id="' . $alumno->nid . '" type="button" value="Detalle" onclick="' . $evento . '" />';
                }

                $datoAlumno[] = [
                    'nid' => $alumno->nid,
                    'nombre' => $alumno->nombre,
                    'apellido_paterno' => $alumno->apellido_paterno,
                    'apellido_materno' => $alumno->apellido_materno,
                    'curp' => $alumno->curp,
                    'accion' => $boton
                ];
            }
        }

        $cabeceras = ['No.', 'NID', 'Nombre', 'Primer Apellido', 'Segundo Apellido', 'Curp', 'Acción'];

        return view('elements.tablaAjax', ['renglones' => $datoAlumno, 'cabeceras' => $cabeceras]);
    }
    /**
     * AJAX Cascade Lookups
     */
    public function escuelas(Request $request)
    {
        $id_entidad = $request->input('Escuela.id_entidad');
        $opciones = Escuela2::where('id_entidad', $id_entidad)
            ->where('status', 'a')
            ->where('unica', '<', 2)
            ->where('modulo', 'l')
            ->pluck('escuela', 'id');

        return view('elements.opciones', ['opciones' => $opciones, 'noelegido' => false]);
    }

    public function licenciaturaOpciones(Request $request)
    {
        $id_escuela = $request->input('Historia.id_escuela');
        
        $opciones = EscuelaLicenciatura::with('licenciatura')
            ->where('id_escuela', $id_escuela)
            ->get()
            ->pluck('licenciatura.licenciatura', 'id_licenciatura');

        return view('elements.opciones2', ['opciones' => $opciones]);
    }

    public function especialidadOpciones(Request $request)
    {
        $id_escuela = $request->input('Historia.id_escuela');
        $id_licenciatura = $request->input('Historia.id_licenciatura');
        
        $opciones = EscuelaLicenciatura::with('especialidad')
            ->where('id_escuela', $id_escuela)
            ->where('id_licenciatura', $id_licenciatura)
            ->get()
            ->pluck('especialidad.especialidad', 'id_especialidad');

        return view('elements.opciones2', ['opciones' => $opciones]);
    }

    public function modalidadOpciones(Request $request)
    {
        $id_escuela = $request->input('Historia.id_escuela');
        $id_licenciatura = $request->input('Historia.id_licenciatura');
        $id_especialidad = $request->input('Historia.id_especialidad');
        
        $registros = EscuelaLicenciatura::where('id_escuela', $id_escuela)
            ->where('id_licenciatura', $id_licenciatura)
            ->where('id_especialidad', $id_especialidad)
            ->get();

        $lista_modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'Intensiva'];
        $modalidades = [];

        foreach ($registros as $reg) {
            $modalidades[$reg->modalidad] = $lista_modalidades[$reg->modalidad] ?? '';
        }

        return view('elements.opciones2', ['opciones' => $modalidades]);
    }

    public function turnoOpciones(Request $request)
    {
        $id_escuela = $request->input('Historia.id_escuela');
        $id_licenciatura = $request->input('Historia.id_licenciatura');
        $id_especialidad = $request->input('Historia.id_especialidad');
        $modalidad = $request->input('Historia.modalidad');
        
        $registros = EscuelaLicenciatura::where('id_escuela', $id_escuela)
            ->where('id_licenciatura', $id_licenciatura)
            ->where('id_especialidad', $id_especialidad)
            ->where('modalidad', $modalidad)
            ->get();

        $lista_turno = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno'];
        $turnos = [];

        foreach ($registros as $reg) {
            $turnos[$reg->turno] = $lista_turno[$reg->turno] ?? '';
        }

        return view('elements.opciones2', ['opciones' => $turnos]);
    }

}

?>