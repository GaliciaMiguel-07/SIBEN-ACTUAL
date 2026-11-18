<?php

namespace App\Http\Controllers;

use App\Models\AreaConocimiento;
use App\Models\Docente;
use App\Models\Funcion;
use App\Models\Licenciatura;
use App\Models\LenguasIndigenas;
use App\Models\Especialidad;
use App\Models\NivelEstudio;
use App\Models\NivelSni;
use App\Models\Personal;
use App\Models\Asignatura;
use App\Models\AsignaturaDocente;
use App\Models\ContratacionDocente;
use App\Models\EscuelaLicenciatura;
use App\Models\Jubilado;
use App\Models\Categorias;
use App\Models\Eventos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonalController extends sibenController
{
    /**
     * Método privado helper para validar los permisos de acceso según bandera de captura.
     * Sustituye la lógica del antiguo beforeFilter.
     */
    private function verificarAccesoAccion(Request $request, string $accionActual)
    {
        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;
        $acciones = ['index', 'agrega', 'jubilacion'];

        if (!$captura && in_array($accionActual, $acciones)) {
            return redirect()->route('personal.menu');
        }

        return null;
    }

    /**
     * Retorna el arreglo de años permitidos para evitar duplicación.
     */
    private function obtenerAnios(): array
    {
        $anios = [];
        for ($i = 1950; $i <= 2025; $i++) {
            $anios[$i] = $i;
        }
        return $anios;
    }

    /**
     * Retorna las discapacidades como catálogo estático.
     */
    private function obtenerDiscapacidades(): array
    {
        return ['1' => 'Auditiva y de lenguaje', '2' => 'Intelectual', '3' => 'Motriz', '4' => 'Visual'];
    }

    /**
     * Retorna las formas de contratación.
     */
    private function obtenerFormasContratacion()
    {
        return DB::table('forma_contratacions')->where('id', '<', 20)->pluck('contratacion', 'id');
    }

    /**
     * Retorna los tiempos de contratación.
     */
    private function obtenerTiemposContratacion()
    {
        return DB::table('forma_contratacions')->where('id', '>', 20)->pluck('contratacion', 'id');
    }

    /**
     * index
     * Mostramos un formulario para buscar, omitiendo la lista de botones
     */
    public function index()
    {
        $request = request(); // Si necesitas el request aquí dentro
        if ($res = $this->verificarAccesoAccion($request, 'index')) {
            return $res;
        }

        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel = $ctx['nivel'] ?? null;
        $cuentaDirector = $ctx['cuenta_director'] ?? 0;

        if (!$id_escuela) {
            return redirect()->back();
        }

        $condiciones = [];
        if ($nivel == 3) {
            $condiciones = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        } elseif ($nivel == 4) {
            $condiciones = [2, 3, 4, 5, 6, 7, 8, 9, 10];
        }

        $funciones = Funcion::whereIn('id', $condiciones)->pluck('funcion', 'id');
        $status = ['a' => 'Activo', 'x' => 'No Activo'];

        $mensaje = '';
        if ($cuentaDirector == 0) {
            $mensaje = 'Por favor, agregue o active al Director de la institución';
        }

        return view('personal.index', compact('funciones', 'status', 'mensaje'))
            ->with('title', 'Búsqueda Personal')
            ->with('anterior', 'menu');
    }

    /**
     * agrega
     * Primera de dos funciones para la agregación de personal
     */
    public function agrega(Request $request)
    {
        if ($res = $this->verificarAccesoAccion($request, 'agrega')) {
            return $res;
        }

        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $cuentaDirector = $ctx['cuenta_director'] ?? 0;

        if (!$id_escuela) {
            return redirect()->back();
        }

        $funciones = Funcion::pluck('funcion', 'id');
        $status = ['a' => 'Activo', 'x' => 'No Activo', 'j' => 'Jubilado'];

        $mensaje = '';
        if ($cuentaDirector == 0) {
            $mensaje = 'Por favor, agregue o active al Director de la institución';
        }

        return view('personal.agrega', compact('funciones', 'status', 'mensaje'))
            ->with('title', 'Captura de Personal')
            ->with('anterior', 'menu');
    }

    /**
     * agrega2
     * Segunda función para la agregación de personal. Mostramos el formulario para capturar los datos.
     */
    public function agrega2(Request $request)
    {
        $id_funcion = $request->input('Personal.id_funcion');

        $vista = match (true) {
            in_array($id_funcion, [1, 2, 3, 4, 5, 6]) => 'agrega_docente',
            in_array($id_funcion, [7, 8, 9, 10]) => 'agrega_nodocente',
            default => 'agrega_nodocente',
        };

        $niveles_estudio = NivelEstudio::pluck('nivel_estudio', 'id');
        $funciones = Funcion::pluck('funcion', 'id');

        $sino = ['s' => 'Sí', 'n' => 'No'];
        $contador = range(1, 50);

        $areas_conocimiento = AreaConocimiento::select('id_campo', 'campo')->distinct()->get();
        $areasConocimiento = $areas_conocimiento->pluck('campo', 'id_campo');

        $disciplinas = AreaConocimiento::pluck('disciplina', 'id');
        $nivel_sni = NivelSni::pluck('nivel_sni', 'id');

        $anios = $this->obtenerAnios();
        $discapacidades = $this->obtenerDiscapacidades();

        return view($vista, compact(
            'niveles_estudio',
            'funciones',
            'sino',
            'contador',
            'areasConocimiento',
            'disciplinas',
            'nivel_sni',
            'discapacidades',
            'id_funcion',
            'anios'
        ))->with('title', 'Agregar Personal');
    }

    /**
     * edita
     * Genera el formulario para editar la información del docente.
     */
    public function edita(Request $request)
    {
        $id_personal = $request->input('Personal.id');
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        $personal = Personal::with(['docente'])->find($id_personal);
        if ($personal && $personal->id_funcion <= 6 && $personal->docente) {
            $disciplina = AreaConocimiento::find($personal->docente->id_area_conocimiento);
            if ($disciplina) {
                $personal->docente->area_conocimiento = $disciplina->id_campo;
                $personal->docente->disciplina = $disciplina->id;
            }
            $personal->docente->investigacion = empty($personal->docente->proyecto_investigacion) ? 'n' : 's';
            $personal->docente->lengua = empty($personal->docente->lengua_indigena) ? 'n' : 's';
        }

        $niveles_estudio = NivelEstudio::pluck('nivel_estudio', 'id');
        $funciones = Funcion::pluck('funcion', 'id');

        $sino = ['s' => 'Sí', 'n' => 'No'];
        $contador = range(1, 50);

        $nivel_sni = NivelSni::pluck('nivel_sni', 'id');

        $anios = $this->obtenerAnios();
        $areas_conocimiento = AreaConocimiento::select('id_campo', 'campo')->distinct()->get();
        $areasConocimiento = $areas_conocimiento->pluck('campo', 'id_campo');

        $disciplinas = AreaConocimiento::pluck('disciplina', 'id');
        $discapacidades = $this->obtenerDiscapacidades();
        $lenguas = LenguasIndigenas::pluck('lengua', 'id');

        $edicion = in_array($nivel, [1, 3, 4]);
        $directivoDocente = $personal && $personal->id_funcion <= 4;
        $activaDocente = $personal && $personal->status == 'x';

        return view('edita', compact(
            'personal',
            'niveles_estudio',
            'funciones',
            'sino',
            'contador',
            'areasConocimiento',
            'disciplinas',
            'id_personal',
            'nivel_sni',
            'discapacidades',
            'anios',
            'lenguas',
            'edicion',
            'directivoDocente',
            'activaDocente'
        ))->with('title', 'Editar Personal');
    }

    /**
     * actualizaRenglon
     * Se hace el render del html que sustituye el renglon de la tabla de personal
     */
    public function actualizaRenglon(Request $request)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $id_personal = $request->input('Personal.id');

        $personal = Personal::find($id_personal);
        if (!$personal) {
            return response('', 404);
        }

        $id_funcion = $personal->id_funcion;
        $titulo = 'Cambiar a no activo';
        $url = route('personal.desactiva');
        $parametros = "data[Personal][id]={$id_personal}";

        $estilo = '';
        $status = '';
        $evento = '';

        switch ($nivel) {
            case 3:
                $titulo = 'Editar';
                $status = strtoupper($personal->status);
                $estilo = 'background-color:' . ($status == 'A' ? 'green' : 'red');
                $url = route('personal.edita');
                $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado)";
                break;
            case 4:
                if ($id_funcion >= 6) {
                    $titulo = 'Desactivar';
                    $status = 'A';
                    $estilo = 'background-color:green';
                    $url = ($personal->status == 'x' ? route('personal.edita') : route('personal.desactiva'));
                    $evento = "actualizaCelda({$id_personal})";
                }

                if ($id_funcion <= 5 && $personal->status == 'x') {
                    $status = 'X';
                    $titulo = 'Activar';
                    $estilo = 'background-color:red';
                    $url = route('personal.edita');
                    $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado)";
                }

                if ($id_funcion <= 5 && $personal->status == 'a') {
                    $status = 'A';
                    $titulo = 'Desactivar';
                    $estilo = 'background-color:green';
                    $url = route('personal.desactiva');
                    $evento = "actualizaCelda({$id_personal})";
                }
                break;
        }

        $boton = "<input type=\"button\" id=\"b_{$id_personal}\" onclick=\"{$evento}\" value=\"{$titulo}\" class=\"btn btn-primary\" style=\"font-size:0.8em;\" />";
        $personal->status = "<div id=\"status_{$id_personal}\" style=\"text-align:center;$estilo\">$status</div>";
        $personal->accion = $boton;

        return view('actualiza_renglon', compact('personal'));
    }

    /**
     * desactiva
     * Cambia el status del personal a no activo.
     */
    public function desactiva(Request $request)
    {
        $id_personal = $request->input('Personal.id');
        $personal = Personal::find($id_personal);

        if ($personal) {
            $personal->status = 'x';
            $personal->save();
        }

        return response('X');
    }

    /**
     * lista
     * Muestra una lista del personal
     */
    public function lista(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel = $ctx['nivel'] ?? null;

        $busqueda = $request->input('busca', []);

        $personal = Personal::where('id_escuela', $id_escuela)
            ->whereIn('status', ['a', 'x'])
            ->whereIn('id_funcion', [2, 3, 4, 5, 6, 7, 8, 9, 10])
            ->when($busqueda, function ($query) use ($busqueda) {
                foreach ($busqueda as $campo => $valor) {
                    if (!empty($valor)) {
                        $query->where($campo, $valor);
                    }
                }
            })
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $cabeceras = ['No.', 'Nombre', 'Primer Apellido', 'Segundo Apellido', 'CURP', 'Edad', 'Género', 'Estado', 'Acción'];
        $renglones = [];

        foreach ($personal as $persona) {
            $parametros = "data[Personal][id]={$persona->id}";

            switch ($nivel) {
                case 3:
                    $titulo = 'Editar';
                    $url = route('personal.edita');
                    $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado)";
                    break;
                case 4:
                    $titulo = ($persona->status == 'x' ? 'Activar' : 'Desactivar');
                    $url = ($persona->status == 'x' ? route('personal.edita') : route('personal.desactiva'));
                    $evento = ($persona->status == 'x' ? "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado)" : "actualizaCelda({$persona->id})");
                    break;
            }

            $estilo = 'background-color:' . ($persona->status == 'a' ? 'green;' : 'red;');
            $boton = "<input type=\"button\" id=\"b_{$persona->id}\" onclick=\"{$evento}\" value=\"{$titulo}\" class=\"btn btn-primary\" style=\"font-size:0.8em;\" />";

            $pArray = $persona->toArray();
            $pArray['accion'] = $boton;
            $pArray['status'] = "<div id='status_{$persona->id}' style='text-align:center;$estilo'>" . strtoupper($persona->status) . "</div>";
            $pArray['genero'] = "<div style='text-align:center;'>" . strtoupper($persona->genero) . "</div>";

            unset($pArray['id']);
            $renglones[$persona->id] = $pArray;
        }

        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }

    /**
     * asignatura
     * Agrega una asignatura al docente en cuestión
     */
    public function asignatura(Request $request)
    {
        $id_personal = $request->input('Personal.id');
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $personal = Personal::find($id_personal);

        if (!$personal || $personal->id_funcion > 5) {
            $vista = 'elements.cerrar_overlay';
        } else {
            $asignaturasDocente = AsignaturaDocente::where('id_docente', $id_personal)->get();

            $escuelaLicenciaturas = EscuelaLicenciatura::with(['licenciatura', 'especialidad'])
                ->where('id_escuela', $id_escuela)
                ->orderBy('id_licenciatura')
                ->get();

            $licenciaturas = $escuelaLicenciaturas->pluck('licenciatura.licenciatura', 'licenciatura.id');
            $especialidades = $escuelaLicenciaturas->pluck('especialidad.especialidad', 'especialidad.id');
            $modalidades = $escuelaLicenciaturas->pluck('modalidad', 'modalidad');
            $turnos = $escuelaLicenciaturas->pluck('turno', 'turno');

            $modalidades2 = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'Intensiva'];
            $turnos2 = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];

            $modalidades = $modalidades->map(fn($m) => $modalidades2[$m] ?? $m);
            $turnos = $turnos->map(fn($t) => $turnos2[$t] ?? $t);

            $semestres = [];
            for ($s = 1; $s <= 4; $s++) {
                $semestre = (2 * $s);
                $semestres[$semestre] = $semestre;
            }

            $semestresSinPlan = [];
            for ($s = 5; $s <= 6; $s++) {
                $semestre = (2 * $s);
                $semestresSinPlan[$semestre] = $semestre;
            }
            $semestresSinPlan[100] = 'Varios';

            $hor = array_combine(range(1, 10), range(1, 10));

            $asignaturasNoCurriculares = Asignatura::where('curricular', 'n')->where('semestre', '0')->pluck('asignatura', 'id');

            $vista = 'asignatura';

            return view($vista, compact(
                'personal',
                'asignaturasDocente',
                'licenciaturas',
                'especialidades',
                'modalidades',
                'semestres',
                'semestresSinPlan',
                'hor',
                'turnos',
                'asignaturasNoCurriculares'
            ));
        }

        return view($vista);
    }

    /**
     * guardaAgregacion
     * Paralela a la función guardaEdicion, se encarga de validar y guardar los datos.
     */
    public function guardaAgregacion(Request $request)
    {
        $curp = $request->input('Personal.curp');
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $id_escuela = $ctx['id_escuela'] ?? null;

        $existe = Personal::where('curp', $curp)->where('id_escuela', $id_escuela)->exists();
        if ($existe) {
            return response("Error: ya existe un registro con la curp {$curp}", 400);
        }

        $dataPersonal = $request->input('Personal', []);
        $dataPersonal['id_escuela'] = $id_escuela;

        if (isset($dataPersonal['id_funcion']) && $dataPersonal['id_funcion'] == '1') {
            $dataPersonal['status'] = 'x';
        }

        if (isset($dataPersonal['id_funcion']) && $dataPersonal['id_funcion'] >= 6) {
            $dataPersonal['status'] = ($nivel == 4 ? 'a' : ($dataPersonal['status'] ?? 'x'));
        }

        $dataPersonal['fecha_actualizacion'] = now();
        $personal = Personal::create($dataPersonal);

        if (isset($dataPersonal['id_funcion']) && $dataPersonal['id_funcion'] <= 6) {
            $dataDocente = $request->input('Docente', []);
            if (($dataDocente['titulo'] ?? 'n') == 'n') {
                $dataDocente['fecha_titulacion'] = "0000-00-00";
            }
            $dataDocente['id_personal'] = $personal->id;
            $dataDocente['id_area_conocimiento'] = $dataDocente['disciplina'] ?? null;
            Docente::create($dataDocente);
        }

        session(['Personal.id' => $personal->id]);

        return view('elements.vacio');
    }

    /**
     * guardaAsignatura
     * Guarda la información de la asignatura que se recibe por post
     */
    public function guardaAsignatura(Request $request)
    {
        $asigData = $request->input('Asignatura', []);
        $asigDocenteData = $request->input('AsignaturaDocente', []);

        $asignatura = Asignatura::find($asigData['id_asignatura'] ?? null);

        if (($asigData['curricular'] ?? '') == 'n' && $asignatura) {
            $asigDocenteData['id_asignatura'] = $asignatura->id;
            $asigDocenteData['horas'] = $asigData['horas'] ?? 0;
            $asigDocenteData['curricular'] = 'n';
        }

        if (($asigData['curricular'] ?? '') == 's' && $asignatura) {
            $asigDocenteData['horas'] = $asignatura->horas;
            $asigDocenteData['id_especialidad'] = $asigData['id_especialidad'] ?? 0;
        }

        $asigDocenteData['id_licenciatura'] = $asigData['id_licenciatura'] ?? null;
        $asigDocenteData['semestre'] = $asigData['semestre'] ?? null;

        $asignaturaDocente = AsignaturaDocente::create($asigDocenteData);

        $nombreAsignatura = $asignatura ? $asignatura->asignatura : '';
        $licenciaturas = Licenciatura::where('id', $asigData['id_licenciatura'] ?? 0)->pluck('licenciatura', 'id');
        $especialidades = Especialidad::where('id', $asigData['id_especialidad'] ?? 0)->pluck('especialidad', 'id');

        $evento_elimina = "eliminaAsignatura({$asignaturaDocente->id})";
        $elimina = "<input type=\"button\" value=\"Elimina\" onclick=\"$evento_elimina\" class=\"btn btn-primary\" />";

        $renglon = [
            $licenciaturas[$asigDocenteData['id_licenciatura']] ?? '',
            $especialidades[$asigDocenteData['id_especialidad']] ?? '',
            $nombreAsignatura,
            $asigDocenteData['semestre'],
            $asigDocenteData['grupo'] ?? '',
            $asigDocenteData['horas'],
            $elimina
        ];

        $clase = 'c' . $asignaturaDocente->id;

        return view('elements.renglon_clase', compact('renglon', 'clase'));
    }

    /**
     * guardaContratacion: Para usarse junto con contrataciones (por ajax)
     */
    public function guardaContratacion(Request $request)
    {
        $contratacionData = $request->input('ContratacionDocente', []);

        $formas_contratacion = $this->obtenerFormasContratacion();
        $tiempos_contratacion = $this->obtenerTiemposContratacion();

        $categoria = Categorias::where('id', '<', 181)->pluck('nombre', 'id');

        $horas = $contratacionData['horas_grupo'] ?? 0;
        $contratacionData['horas_grupo'] = $horas > 0 ? $horas : 0;

        if ($contratacionData['horas_grupo'] <= 0 || $contratacionData['horas_grupo'] >= 50) {
            $renglon = [
                '<div style="color:red;font-weight:600;">ERROR:</div>',
                '<div style="color:red;font-weight:600;">Revise las horas contratadas, no pueden ser 0 ni mayores de 50!</div>',
                '', ''
            ];
            $clase = 'c_error';
            return view('elements.renglon_clase', compact('renglon', 'clase'));
        }

        $contDocente = ContratacionDocente::create($contratacionData);

        $evento_elimina = "eliminaContratacion({$contDocente->id})";
        $elimina = "<input type=\"button\" value=\"Elimina\" onclick=\"$evento_elimina\" class=\"btn btn-primary\" />";

        $renglon = [
            $formas_contratacion[$contratacionData['forma_contratacion'] ?? 0] ?? '',
            $tiempos_contratacion[$contratacionData['tiempo_contratacion'] ?? 0] ?? '',
            $contratacionData['clave_contratacion'] ?? '',
            $categoria[$contratacionData['categoria'] ?? 0] ?? '',
            $contratacionData['horas_grupo'],
            $elimina
        ];

        $clase = 'c' . $contDocente->id;

        return view('elements.renglon_clase', compact('renglon', 'clase'));
    }

    /**
     * guardaEdicion
     * Método pensado para guardar los cambios realizados al editar un registro
     */
    public function guardaEdicion(Request $request)
    {
        $data = $request->all();
        if (empty($data)) {
            return view('elements.vacio');
        }

        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $id_personal = $request->input('Personal.id');
        $curp = $request->input('Personal.curp');
        $id_escuela = $ctx['id_escuela'] ?? null;

        $existe = Personal::where('id', '<>', $id_personal)
            ->where('curp', $curp)
            ->where('id_escuela', $id_escuela)
            ->exists();

        if ($existe) {
            return response("Error: ya existe un registro con la curp {$curp}", 400);
        }

        $personal = Personal::find($id_personal);
        if ($personal) {
            $dataPersonal = $request->input('Personal', []);
            if (isset($dataPersonal['id_funcion']) && $dataPersonal['id_funcion'] >= 6) {
                $dataPersonal['status'] = ($nivel == 4 ? 'a' : $personal->status);
            }
            $dataPersonal['fecha_actualizacion'] = now();
            $personal->update($dataPersonal);

            if (isset($dataPersonal['id_funcion']) && $dataPersonal['id_funcion'] <= 6) {
                $dataDocente = $request->input('Docente', []);
                if (($dataDocente['titulo'] ?? 'n') == 'n') {
                    $dataDocente['fecha_titulacion'] = '0000-00-00';
                }
                $dataDocente['id_area_conocimiento'] = $dataDocente['disciplina'] ?? null;

                Docente::updateOrCreate(
                    ['id' => $dataDocente['id'] ?? null],
                    array_merge($dataDocente, ['id_personal' => $id_personal])
                );
            }
        }

        return view('elements.vacio');
    }

    /**
     * guardaJubilacion: Complemento para el formulario con la fecha de jubilacion
     */
    public function guardaJubilacion(Request $request)
    {
        $id_personal = $request->input('Personal.id');
        $tipo = $request->input('Jubilado.tipo');

        $personal = Personal::find($id_personal);
        if ($personal) {
            $personal->status = $tipo;
            $personal->save();
        }

        Jubilado::updateOrCreate(
            ['id_personal' => $id_personal],
            ['fecha_jubilacion' => $request->input('Jubilado.fecha_jubilacion')]
        );

        return view('elements.vacio');
    }

    /**
     * muestra
     * Muestra la información del personal dado por su id.
     */
    public function muestra($id_personal = null)
    {
        if (!$id_personal) {
            return redirect()->route('personal.lista');
        }

        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        $personal = Personal::with(['docente'])->find($id_personal);
        if ($personal && $personal->id_funcion <= 6 && $personal->docente) {
            $disciplina = AreaConocimiento::find($personal->docente->id_area_conocimiento);
            if ($disciplina) {
                $personal->docente->area_conocimiento = $disciplina->id_campo;
                $personal->docente->disciplina = $disciplina->id;
            }
            $personal->docente->investigacion = empty($personal->docente->proyecto_investigacion) ? 'n' : 's';
            $personal->docente->lengua = empty($personal->docente->lengua_indigena) ? 'n' : 's';
        }

        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');
        $especialidades = Especialidad::pluck('especialidad', 'id') + [0 => 'n/a'];
        $niveles_estudio = NivelEstudio::pluck('nivel_estudio', 'id');

        $formas_contratacion = $this->obtenerFormasContratacion();
        $tiempos_contratacion = $this->obtenerTiemposContratacion();

        $contrataciones = ContratacionDocente::where('id_personal', $id_personal)->get();
        $funciones = Funcion::pluck('funcion', 'id');

        $sino = ['s' => 'Sí', 'n' => 'No'];

        $areas_conocimiento = AreaConocimiento::select('id_campo', 'campo')->distinct()->get();
        $areasConocimiento = $areas_conocimiento->pluck('campo', 'id_campo');

        $disciplinas = AreaConocimiento::pluck('disciplina', 'id');

        $asignaturasDocente = AsignaturaDocente::where('id_docente', $personal->docente->id ?? 0)->get();

        $titulo = ['s' => 'Titulado', 'n' => 'No titulado'];
        $generos = ['H' => 'Hombre', 'M' => 'Mujer'];

        $boton_agregar_asignatura = $nivel >= 3 && $personal && $personal->status == 'a' && $personal->id_funcion <= 5;

        $anterior = match (true) {
            in_array($personal->id_funcion, [1, 2, 3, 4]) => '/personal/lista/1/4',
            in_array($personal->id_funcion, [5, 6]) => '/personal/lista/' . $personal->id_funcion,
            in_array($personal->id_funcion, [7, 8, 9, 10]) => '/personal/lista/7/10',
            default => '/personal/lista'
        };

        return view('muestra', compact(
            'personal',
            'licenciaturas',
            'especialidades',
            'niveles_estudio',
            'formas_contratacion',
            'tiempos_contratacion',
            'contrataciones',
            'funciones',
            'sino',
            'areasConocimiento',
            'disciplinas',
            'id_personal',
            'asignaturasDocente',
            'titulo',
            'generos',
            'boton_agregar_asignatura',
            'anterior'
        ));
    }

    /**
     * especialidades
     */
    public function especialidades(Request $request)
    {
        $id_licenciatura = $request->input('Asignatura.id_licenciatura');
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $especialidades = EscuelaLicenciatura::with('especialidad')
            ->where('id_escuela', $id_escuela)
            ->where('id_especialidad', '<>', 0)
            ->when($id_licenciatura, fn($q) => $q->where('id_licenciatura', $id_licenciatura))
            ->get()
            ->pluck('especialidad.especialidad', 'id_especialidad');

        return view('elements.opciones', ['opciones' => $especialidades]);
    }

    /**
     * asignaturas
     */
    public function asignaturas(Request $request)
    {
        $id_licenciatura = $request->input('Asignatura.id_licenciatura');
        $id_especialidad = $request->input('Asignatura.id_especialidad');
        $semestre = $request->input('Asignatura.semestre');
        $modalidad = $request->input('Asignatura.modalidad');

        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $evento = Eventos::where('id_escuela', $id_escuela)->first();
        $condiciones = ['curricular' => 's'];

        if ($evento && $semestre == 0 && in_array($id_licenciatura, [16, 17, 18, 19])) {
            $condiciones['evento'] = $evento->id_evento;
        } else {
            $condiciones['evento'] = 1;
        }

        $asignaturas = Asignatura::where($condiciones)
            ->when($id_licenciatura, fn($q) => $q->where('id_licenciatura', $id_licenciatura))
            ->when($id_especialidad, fn($q) => $q->whereIn('id_especialidad', [0, $id_especialidad]))
            ->when($semestre, fn($q) => $q->where('semestre', $semestre))
            ->when($modalidad, fn($q) => $q->where('modalidad', $modalidad))
            ->orderBy('asignatura')
            ->pluck('asignatura', 'id');

        return view('elements.opciones', ['opciones' => $asignaturas]);
    }

    /**
     * semestres
     */
    public function semestres(Request $request)
    {
        $semestres = [];
        for ($s = 1; $s <= 4; $s++) {
            $semestre = (2 * $s);
            $semestres[$semestre] = $semestre;
        }

        if ($request->input('Asignatura.modalidad') == 'm') {
            for ($s = 1; $s <= 6; $s++) {
                $semestre = (2 * $s);
                $semestres[$semestre] = $semestre;
            }
        }

        return view('elements.opciones', ['opciones' => $semestres]);
    }

    /**
     * disciplinas
     */
    public function disciplinas(Request $request)
    {
        $area_conocimiento = $request->input('Docente.area_conocimiento');

        $disciplinas = AreaConocimiento::where('id_campo', $area_conocimiento)->pluck('disciplina', 'id');

        return view('elements.opciones', ['opciones' => $disciplinas]);
    }

    /**
     * contrataciones
     */
    public function contrataciones(Request $request)
    {
        $id_personal = $request->input('Personal.id', session('Personal.id'));
        if (session()->has('Personal.id')) {
            session()->forget('Personal.id');
        }

        $id_funcion = $request->input('Personal.id_funcion');

        if ($id_funcion > 6) {
            $vista = 'elements.cerrar_overlay';
        } else {
            $formas_contratacion = $this->obtenerFormasContratacion();
            $tiempos_contratacion = $this->obtenerTiemposContratacion();

            $contrataciones = ContratacionDocente::where('id_personal', $id_personal)->get();
            $horas = range(1, 30);

            $categoria = Categorias::pluck('nombre', 'id');

            $vista = 'contrataciones';

            return view($vista, compact(
                'id_personal',
                'tiempos_contratacion',
                'formas_contratacion',
                'contrataciones',
                'categoria',
                'horas'
            ));
        }

        return view($vista);
    }

    /**
     * eliminaAsignatura
     */
    public function eliminaAsignatura(Request $request)
    {
        $id = $request->input('AsignaturaDocente.id');
        AsignaturaDocente::destroy($id);

        return view('elements.vacio');
    }

    /**
     * eliminaContratacion
     */
    public function eliminaContratacion(Request $request)
    {
        $id = $request->input('ContratacionDocente.id');
        ContratacionDocente::destroy($id);

        return view('elements.vacio');
    }

    /**
     * menu
     */
    public function menu(Request $request)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $captura = $ctx['captura'] ?? null;
        $mensaje = '';

        if (!$captura) {
            $mensaje = 'La captura está finalizada';
        } else {
            $cuentaDirector = $ctx['cuenta_director'] ?? 0;
            $mensaje = ($cuentaDirector == 0 ? 'Por favor agregue o active al director de la escuela' : '');
        }

        $opcionesMenu = [];
        switch ($nivel) {
            case 3:
                $opcionesMenu[] = ['personal', 'Editar', '', 0];
                $opcionesMenu[] = ['consultas_personal', 'Consulta personal', 'formulario', 0];
                $opcionesMenu[] = ['personal', 'Lista Bajas', 'jubilados', 0];
                break;
            case 4:
                $opcionesMenu[] = ['personal', 'Agregar', 'agrega', 0];
                $opcionesMenu[] = ['personal', 'Activar', '', 0];
                $opcionesMenu[] = ['consultas_personal', 'Consulta personal', 'formulario', 0];
                $opcionesMenu[] = ['personal', 'Baja Personal', 'jubilacion', 0];
                $opcionesMenu[] = ['personal', 'Lista Bajas', 'jubilados', 0];
                break;
            default:
                return redirect('/');
        }

        return view('personal.menu', compact('opcionesMenu', 'mensaje'));
    }

    /**
     * jubilacion
     */
    public function jubilacion(Request $request)
    {
        if ($res = $this->verificarAccesoAccion($request, 'jubilacion')) {
            return $res;
        }

        $funciones = Funcion::pluck('funcion', 'id');
        $anterior = 'menu';

        return view('personal.jubilacion', compact('funciones', 'anterior'));
    }

    /**
     * listaJubilacion
     */
    public function listaJubilacion(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $busqueda = $request->input('busca', []);

        $personal = Personal::where('id_escuela', $id_escuela)
            ->where('status', 'x')
            ->when($busqueda, function ($query) use ($busqueda) {
                foreach ($busqueda as $campo => $valor) {
                    if (!empty($valor)) {
                        $query->where($campo, $valor);
                    }
                }
            })
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $cabeceras = ['No.', 'Nombre', 'Primer Apellido', 'Segundo Apellido', 'CURP', 'Edad', 'Género', 'Estado', 'Acción'];
        $renglones = [];

        foreach ($personal as $persona) {
            $parametros = "data[Personal][id]={$persona->id}";
            $titulo = 'Baja';
            $url = route('personal.jubilar');
            $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado)";
            $estilo = 'background-color:red;';
            $boton = "<input type=\"button\" id=\"b_{$persona->id}\" onclick=\"{$evento}\" value=\"{$titulo}\" class=\"btn btn-primary\" />";

            $pArray = $persona->toArray();
            $pArray['accion'] = $boton;
            $pArray['status'] = "<div id='status_{$persona->id}' style='text-align:center;$estilo'>" . strtoupper($persona->status) . "</div>";
            $pArray['genero'] = "<div style='text-align:center;'>" . strtoupper($persona->genero) . "</div>";

            unset($pArray['id']);
            $renglones[$persona->id] = $pArray;
        }

        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }

    /**
     * jubilar
     */
    public function jubilar(Request $request)
    {
        $id_personal = $request->input('Personal.id');
        $tipo = ['j' => 'Jubilación', 'b' => 'Baja Administrativa', 'r' => 'Renuncia', 'd' => 'Defunción'];

        return view('jubilar', compact('id_personal', 'tipo'));
    }

    /**
     * jubilados
     */
    public function jubilados(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $tipo = ['j' => 'Jubilado', 'b' => 'Baja Administrativa', 'r' => 'Renuncia', 'd' => 'Defunción'];

        $personal = Personal::where('id_escuela', $id_escuela)
            ->whereIn('status', ['j', 'b', 'r'])
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $ids_personal = $personal->pluck('id');
        $fechas_jubilacion = Jubilado::whereIn('id_personal', $ids_personal)->pluck('fecha_jubilacion', 'id_personal');

        $funciones = Funcion::pluck('funcion', 'id');

        $renglones = [];
        foreach ($personal as $persona) {
            $pArray = $persona->toArray();
            $pArray['funcion'] = $funciones[$persona->id_funcion] ?? '';
            $pArray['fecha_jubilacion'] = $fechas_jubilacion[$persona->id] ?? '';

            unset($pArray['id_funcion'], $pArray['id']);
            $renglones[] = $pArray;
        }

        $anterior = 'menu';

        return view('jubilados', compact('tipo', 'funciones', 'fechas_jubilacion', 'personal', 'renglones', 'anterior'));
    }

    /**
     * activarJubilado
     */
    public function activarJubilado(Request $request, $persona_id = null)
    {
        $persona_id = $persona_id ?? $request->input('Personal.id');

        if ($persona_id) {
            $personal = Personal::find($persona_id);
            $jubilado = Jubilado::where('id_personal', $persona_id)->first();

            if ($jubilado) {
                $jubilado->delete();
            }

            if ($personal) {
                $personal->status = 'x';
                $personal->fecha_actualizacion = now();
                $personal->save();
            }
        }

        return redirect('/personal/jubilados/');
    }

    /**
     * resumen
     */
    public function resumen(Request $request)
    {
        $id_personal = $request->input('Personal.id');
        $personal = Personal::find($id_personal);

        $funcion = Funcion::find($personal->id_funcion ?? 0);

        $contrataciones = ContratacionDocente::where('id_personal', $id_personal)->get();
        $asignaturas = AsignaturaDocente::where('id_docente', $id_personal)->get();
        $horas_contrato = ContratacionDocente::where('id_personal', $id_personal)->select('horas_grupo')->get();

        return view('resumen', compact('personal', 'contrataciones', 'asignaturas', 'horas_contrato', 'funcion', 'id_personal'));
    }

    /**
     * activa
     */
    public function activa(Request $request)
    {
        $id_personal = $request->input('Personal.id');
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        $personal = Personal::find($id_personal);
        if (!$personal) {
            return response("Personal no encontrado", 404);
        }

        $contrataciones = ContratacionDocente::where('id_personal', $id_personal)->get();
        $asignaturas = AsignaturaDocente::where('id_docente', $id_personal)->get();

        $total_horas_c = $contrataciones->sum('horas_grupo');
        $total_asignaturas = $asignaturas->sum('horas');

        if ($personal->id_funcion == 5 && $total_horas_c > 0 && $total_asignaturas > 0) {
            $personal->status = ($nivel == 4 ? 'a' : $personal->status);
            $personal->fecha_actualizacion = now();
            $personal->save();
            return view('elements.vacio');
        }

        if ($personal->id_funcion == 5 && $total_horas_c == 0) {
            return response("No se activara al docente ya que no ha capturado sus contrataciones. De clic en el botón anterior para revisar los datos", 400);
        }

        if ($personal->id_funcion == 5 && $total_asignaturas == 0) {
            return response("No se activara al docente ya que no ha capturado sus asignaturas. De clic en el botón anterior para revisar los datos", 400);
        }

        if ($personal->id_funcion <= 4 && $total_horas_c > 0) {
            $personal->status = ($nivel == 4 ? 'a' : $personal->status);
            $personal->fecha_actualizacion = now();
            $personal->save();
            return view('elements.vacio');
        }

        if ($personal->id_funcion <= 4 && $total_horas_c == 0) {
            return response("No se activo a la persona ya que no ha capturado sus contrataciones. De clic en el botón anterior para revisar los datos ", 400);
        }

        return view('elements.vacio');
    }

    /**
     * activaJubilado
     */
    public function activaJubilado(Request $request, $id_persona = null)
    {
        $id_persona = $id_persona ?? $request->input('Personal.id');

        if ($id_persona) {
            $personal = Personal::find($id_persona);
            if ($personal) {
                $personal->status = 'x';
                $personal->fecha_actualizacion = date('Y-m-d');
                $personal->save();
            }
        }

        return redirect('/personal/jubilados/');
    }
}