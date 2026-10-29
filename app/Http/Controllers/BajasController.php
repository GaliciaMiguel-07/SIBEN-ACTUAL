<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Registro;
use App\Models\Baja;
use App\Models\Licenciatura;
use App\Models\Especialidad;
use App\Models\MotivosBaja;
use App\Models\Escuela2;
use App\Models\Historia;
use App\Models\EscuelaLicenciatura;
use App\Models\Modalidad;
use App\Models\Turno;
use Illuminate\Http\Request;

class BajasController extends sibenController
{
    /**
     * beforeFilter / Filtro de seguridad y control de acceso basado en contexto
     */
    public function beforeFilter(Request $request)
    {
        parent::beforeFilter();

        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;
        $nivel = $ctx['nivel'] ?? null;

        if (!$nivel) {
            return redirect()->route('login')->with('error', 'Sesión no iniciada.');
        }

        $currentAction = $request->route()->getActionMethod();

        $accionesPermitidas = [
            'listadosBajas'
        ];

        $accionesPermitidasObservador = [
            'busquedaAlumnos2',
            'listasAlumnos2'
        ];

        if (!$captura && in_array($nivel, [3, 4, 5])) {
            if (!in_array($currentAction, $accionesPermitidas)) {
                return redirect()->route('alumnos.index');
            }
        } elseif (!$captura && $nivel == 6) {
            if (!in_array($currentAction, $accionesPermitidasObservador)) {
                return redirect()->route('alumnos.index');
            }
        }
    }

    /**
     * index
     * Vista principal del módulo de bajas
     */
    public function index()
    {
        $request = request(); // Obtenemos el request aquí si lo necesitas
        $ctx = $this->contexto();
        if (!isset($ctx['nivel'])) {
            return redirect()->route('login');
        }
        return view('bajas.index');
    }

    /**
     * bajasAlumnos
     * Método seguro para actualizar el estatus de un alumno (t o d).
     */
    public function bajasAlumnos(Request $request, $opc = null, $id_escuela = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $captura_actual = $ctx['captura_actual'] ?? session('captura_actual');

        if (!$nivel) {
            return redirect()->route('login');
        }

        if (!$id_escuela) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        }

        if (!$id_escuela) {
            return redirect()->route('alumnos.index')->with('error', 'Escuela no especificada.');
        }

        switch ($nivel) {
            case 3:
                return redirect()->route('alumnos.index');
            case 4:
                break;
        }

        $id_registro = $request->input('Historia.id_registro');
        $tipo_baja = null;
        $nombre_baja = [];

        if ($id_registro) {
            if ($nivel == 2) {
                $tipo_baja = $request->input('Historia.tipo_baja');
                $nombre_baja = ['t' => 'BAJA TEMPORAL', 'd' => 'BAJA DEFINITIVA'];
            }
        }

        $alumno = null;
        if ($id_registro) {
            $alumno = Registro::with(['alumno', 'licenciatura', 'especialidad'])
                ->where('captura', $captura_actual)
                ->where('id', $id_registro)
                ->where('id_escuela', $id_escuela)
                ->first();
        }

        Licenciatura::$displayField = 'licenciatura';
        $licenciatura = Licenciatura::pluck('licenciatura', 'id');

        Especialidad::$displayField = 'especialidad';
        $especialidad = Especialidad::pluck('especialidad', 'id');

        $modalidad = ['e' => 'Escolarizada', 'm' => 'Mixta', 'i' => 'intensiva'];
        $turno = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno'];

        $motivos = [];
        if ($nivel == 4) {
            $motivos = MotivosBaja::where('tipo_baja', 't')->pluck('motivo', 'id');
        } elseif ($nivel == 2 && $tipo_baja) {
            $motivos = MotivosBaja::where('tipo_baja', $tipo_baja)->pluck('motivo', 'id');
        }

        $bajaData = $request->input('Baja');
        $registroData = $request->input('Registro');
        $alumnoData = $request->input('Alumno');

        if (!empty($bajaData) && !empty($registroData['id'])) {
            $registro = Registro::where('id', $registroData['id'])
                ->where('id_escuela', $id_escuela)
                ->first();

            if ($registro) {
                $registro->update(['status' => $registroData['status'] ?? $registro->status]);

                $nid = $alumnoData['numero_identificador'] ?? null;
                if ($nid) {
                    $bajaExistente = Baja::where('nid', $nid)->where('id_escuela', $id_escuela)->first();

                    $bajaPayload = [
                        'nid' => $nid,
                        'captura' => $captura_actual,
                        'id_escuela' => $id_escuela,
                    ];
                    $bajaPayload = array_merge($bajaPayload, $bajaData);

                    if ($bajaExistente) {
                        $bajaExistente->update($bajaPayload);
                    } else {
                        Baja::create($bajaPayload);
                    }
                }
            }
        }

        return view('bajas.bajas_alumnos', compact(
            'alumno',
            'licenciatura',
            'especialidad',
            'modalidad',
            'turno',
            'nivel',
            'motivos',
            'id_registro',
            'tipo_baja',
            'nombre_baja'
        ));
    }

    /**
     * listadosBajas
     * Muestra listados de bajas validados por escuela y contexto
     */
    public function listadosBajas(Request $request, $opc = null)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura_actual = $ctx['captura_actual'] ?? session('captura_actual');

        if (!$opc || !$id_escuela) {
            return redirect()->route('alumnos.index');
        }

        $listadoBajas = Baja::select([
                'alumnos.numero_identificador', 
                'alumnos.nombre', 
                'alumnos.apellido_paterno',
                'alumnos.apellido_materno', 
                'alumnos.curp', 
                'bajas.id_motivo_baja', 
                'registros.id_licenciatura',
                'registros.id_especialidad', 
                'registros.semestre'
            ])
            ->join('registros', function ($join) use ($id_escuela) {
                $join->on('bajas.id_escuela', '=', 'registros.id_escuela')
                     ->where('registros.id_escuela', '=', $id_escuela);
            })
            ->join('alumnos', 'alumnos.numero_identificador', '=', 'bajas.nid')
            ->where('alumnos.numero_identificador', '!=', '')
            ->where('registros.status', $opc)
            ->where('registros.captura', $captura_actual)
            ->where('bajas.id_escuela', $id_escuela)
            ->orderBy('alumnos.nombre')
            ->orderBy('alumnos.apellido_paterno')
            ->orderBy('alumnos.apellido_materno')
            ->groupBy('alumnos.numero_identificador')
            ->get();

        $motivos = MotivosBaja::pluck('motivo', 'id');
        
        Especialidad::$displayField = 'especialidad';
        $especialidades = Especialidad::pluck('especialidad', 'id');

        Licenciatura::$displayField = 'licenciatura';
        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');

        $cabeceras = ['Nid', 'Primer Apellido&nbsp;', 'Segundo Apellido&nbsp;', 'Nombe(s)&nbsp;', 'Curp&nbsp;', 'Lic.&nbsp;', 'Esp.&nbsp;', 'Sem.&nbsp;', 'Motivo&nbsp;'];
        $listaBajas = ['t' => 'BAJA TEMPORAL', 'd' => 'BAJA DEFINITIVA'];

        return view('bajas.listados_bajas', compact(
            'cabeceras',
            'listadoBajas',
            'motivos',
            'especialidades',
            'licenciaturas'
        ))->with('tipo_baja', $listaBajas[$opc] ?? '');
    }

    /**
     * actualizaRenglon
     * Render seguro del renglón actualizado mediante rutas nombradas
     */
    public function actualizaRenglon(Request $request)
    {
        $status = $request->input('Registro.status');
        $idRegistro = $request->input('Registro.id');
        $accion = $request->input('Renglon.accion');

        $estilo = '';
        $estado = '';

        switch ($status) {
            case 'x':
                $estilo = 'style="text-align:center; background-color:#CC0000; color:#FFCCCC; font-weight:bold;"';
                $estado = 'No Insc.';
                break;
            case 't':
                $estilo = 'style="text-align:center; background-color:#0066CC; color:#CCFFFF; font-weight:bold;"';
                $estado = 'B. Temp.';
                break;
            case 'i':
                $estilo = 'style="text-align:center; background-color:#054205; color:#CCFFCC; font-weight:bold;"';
                $estado = 'Insc.';
                break;
            case 'd':
                $estilo = 'style="text-align:center; background-color:#FF9900; color:#663300; font-weight:bold;"';
                $estado = 'B. Def.';
                break;
            default:
                $estilo = 'style="text-align:center; background-color:#054205; color:#CCFFCC; font-weight:bold;"';
                $estado = 'Insc.';
                break;
        }

        if (!empty($status)) {
            if (empty($accion)) {
                $url = route('alumnos.actualizarAlumno');
                $parametros = "data[Historia][id_registro]={$idRegistro}";
                $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado);";
                $accion = '<input id="b_' . $idRegistro . '" type="button" value="Editar" onclick="' . $evento . '" class="btn btn-primary" />';
            }
        }

        $statusHtml = "<div {$estilo}>{$estado}</div>";

        $info = $request->all();
        if (isset($info['Registro'])) {
            $info['Registro']['status'] = $statusHtml;
        }
        if (isset($info['Renglon'])) {
            $info['Renglon']['accion'] = $accion;
        }

        return view('elements.actualiza_renglon', compact('info'));
    }

    /**
     * busquedaAlumnos
     * Formulario seguro de búsqueda con rutas nombradas de Laravel
     */
    public function busquedaAlumnos(Request $request, $opc = null)
    {
        if (!$opc) {
            return redirect()->route('alumnos.index');
        }

        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $id_entidad = $ctx['id_entidad'] ?? null;
        $captura_actual = $ctx['captura_actual'] ?? session('captura_actual', 1);

        if (!$nivel) {
            return redirect()->route('login');
        }

        Escuela2::$displayField = 'escuela';
        $escuelasEstado = Escuela2::where('status', 'a')
            ->where('unica', '<', 2)
            ->where('id_entidad', $id_entidad)
            ->where('captura', 1)
            ->pluck('escuela', 'id');

        $semestre = [];

        if ($nivel == 2) {
            if ($opc == 't') {
                $semestre = ['8' => '8° Escolarizada', '12' => '12° Mixta'];
            } elseif ($opc == 'd') {
                for ($i = 1; $i <= 13; $i++) {
                    if (($i % 2) == ($captura_actual % 2)) {
                        $semestre[$i] = $i;
                    }
                }
            }
        } elseif ($nivel == 4) {
            for ($i = 1; $i <= 13; $i++) {
                if (($i % 2) == ($captura_actual % 2)) {
                    $semestre[$i] = $i;
                }
            }
        }

        $status = [];
        $url = '';

        if ($opc == 't') {
            $status = ['i' => 'Inscrito', 'x' => 'No Inscrito', 'd' => 'Baja Definitiva'];
            $url = route('bajas.listasAlumnos', ['opc' => $opc]);
        } elseif ($opc == 'd') {
            $status = ['i' => 'Inscrito', 'x' => 'No Inscrito'];
            $url = route('bajas.listasAlumnos', ['opc' => $opc]);
        }

        $tituloTabla = ['d' => ' Para la bajas definitivas', 't' => 'Para las Bajas Temporales'];
        $tituloPag = ['d' => ' DEFINITIVAS', 't' => ' TEMPORALES'];
        $anterior = route('alumnos.index');

        return view('bajas.busqueda_alumnos', compact(
            'semestre',
            'status',
            'opc',
            'url',
            'tituloTabla',
            'tituloPag',
            'escuelasEstado',
            'anterior'
        ));
    }

    /**
     * listasAlumnos
     * Regresa la lista de alumnos según los criterios de búsqueda (AJAX)
     */
    public function listasAlumnos(Request $request, $opc = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $captura_actual = $ctx['captura_actual'] ?? session('captura_actual', 1);

        if (!$nivel) {
            return redirect()->route('login');
        }

        $id_escuela = null;
        if ($nivel == 2) {
            $id_escuela = $request->input('Busca.id_escuela');
        } elseif ($nivel == 4) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        }

        $condiciones = [
            'id_escuela' => $id_escuela,
            'captura' => $captura_actual
        ];
        
        $campos = ['nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp', 'semestre', 'status', 'id_registro', 'status_conflictos', 'modalidad'];

        $buscaData = $request->input('Busca', []);
        $contador = 0;
        foreach ($buscaData as $valor) {
            if (empty($valor)) {
                $contador++;
            }
        }

        $alumnos = collect();
        if ($contador < count($buscaData) || count($buscaData) == 0) {
            foreach ($buscaData as $i => $valor) {
                if (!empty($valor)) {
                    $condiciones[$i] = $valor;
                }
            }

            $query = Historia::where('id_escuela', $id_escuela)
                ->where('captura', $captura_actual);

            foreach ($buscaData as $i => $valor) {
                if (!empty($valor)) {
                    $query->where($i, $valor);
                }
            }

            $alumnos = $query->select($campos)
                ->orderBy('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre')
                ->get();
        }

        $url = '';
        if ($opc == 't' || $opc == 'd') {
            $url = route('bajas.bajasAlumnos', ['opc' => $opc, 'id_escuela' => $id_escuela]);
        }

        $renglones = [];
        foreach ($alumnos as $alumno) {
            $idRegistro = $alumno->id_registro;
            $modalidad = $alumno->modalidad;
            $semestre = $alumno->semestre;
            $statusAlumno = $alumno->status;
            $statusConflictos = $alumno->status_conflictos;

            $parametros = "data[Historia][id_registro]={$idRegistro}&data[Historia][tipo_baja]={$opc}";
            $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado);";
            $boton = '';

            if ($nivel == 2) {
                if ($opc == 't') {
                    if ($modalidad == 'e' && $semestre == 8 && $statusAlumno != 't') {
                        $boton = '<input id="b_' . $idRegistro . '" type="button" value="Baja" onclick="' . $evento . '" class="btn btn-primary" />';
                    } elseif ($modalidad == 'm' && $semestre == 12 && $statusAlumno != 't') {
                        $boton = '<input id="b_' . $idRegistro . '" type="button" value="Baja" onclick="' . $evento . '" class="btn btn-primary" />';
                    }
                } elseif ($opc == 'd') {
                    $boton = '<input id="b_' . $idRegistro . '" type="button" value="Baja" onclick="' . $evento . '" class="btn btn-primary" />';
                }
            } elseif ($nivel == 4) {
                if ($opc == 't') {
                    if ($modalidad == 'e' && $semestre <= 8 && $statusAlumno != 't') {
                        $boton = '<input id="b_' . $idRegistro . '" type="button" value="Baja" onclick="' . $evento . '" class="btn btn-primary" />';
                    } elseif ($modalidad == 'm' && $semestre <= 12 && $statusAlumno != 't') {
                        $boton = '<input id="b_' . $idRegistro . '" type="button" value="Baja" onclick="' . $evento . '" class="btn btn-primary" />';
                    }
                }
            }

            $optativaTipoBaja = $request->input('Optativa.tipo_baja');
            if ($statusConflictos != 'n' && $statusAlumno != 'd' || ($statusAlumno == 't' && $optativaTipoBaja == 'd')) {
                $alumno->accion = $boton;
            }

            $estilo = '';
            $estado = '';
            switch ($statusAlumno) {
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
            }

            $alumno->status = "<div style='{$estilo}'>{$estado}</div>";

            $indx = $idRegistro;
            $alumnoArray = $alumno->toArray();
            
            unset($alumnoArray['id_registro'], $alumnoArray['status_conflictos'], $alumnoArray['modalidad']);

            $renglones[$indx] = $alumnoArray;
        }

        $cabeceras = ['No.', 'nid', 'Primer Apellido', 'Segundo Apellido', 'Nombre', 'CURP', 'Sem', 'Estado', 'Acción'];

        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }

    /**
     * listaLicenciaturas
     * Regresamos la lista de licenciaturas de la escuela (AJAX)
     */
    public function listaLicenciaturas(Request $request)
    {
        $id_escuela = $request->input('Busca.id_escuela');

        $licenciaturas = EscuelaLicenciatura::with('licenciatura')
            ->where('id_escuela', $id_escuela)
            ->get()
            ->pluck('licenciatura.licenciatura', 'licenciatura.id');

        return view('elements.opciones2', ['opciones' => $licenciaturas]);
    }

    /**
     * listaEspecialidades
     * Regresamos la lista de especialidades de la escuela (AJAX)
     */
    public function listaEspecialidades(Request $request)
    {
        $id_escuela = $request->input('Busca.id_escuela');
        $id_licenciatura = $request->input('Busca.id_licenciatura');

        $especialidades = EscuelaLicenciatura::with('especialidad')
            ->where('id_escuela', $id_escuela)
            ->where('id_licenciatura', $id_licenciatura)
            ->get()
            ->pluck('especialidad.especialidad', 'especialidad.id');

        return view('elements.opciones2', ['opciones' => $especialidades]);
    }

    /**
     * listaModalidades
     * Regresamos la lista de modalidades de la escuela (AJAX)
     */
    public function listaModalidades(Request $request)
    {
        $id_escuela = $request->input('Busca.id_escuela');
        $id_licenciatura = $request->input('Busca.id_licenciatura');

        $id_modalidades = EscuelaLicenciatura::where('id_escuela', $id_escuela)
            ->where('id_licenciatura', $id_licenciatura)
            ->pluck('modalidad', 'modalidad');

        $modalidades = Modalidad::whereIn('id', $id_modalidades)->pluck('modalidad', 'id');

        return view('elements.opciones2', ['opciones' => $modalidades]);
    }

    /**
     * listaTurnos
     * Regresamos la lista de turnos de la escuela (AJAX)
     */
    public function listaTurnos(Request $request)
    {
        $id_escuela = $request->input('Busca.id_escuela');
        $id_licenciatura = $request->input('Busca.id_licenciatura');

        $id_turnos = EscuelaLicenciatura::where('id_escuela', $id_escuela)
            ->where('id_licenciatura', $id_licenciatura)
            ->pluck('turno', 'turno');

        $turnos = Turno::whereIn('id', $id_turnos)->pluck('turno', 'id');

        return view('elements.opciones2', ['opciones' => $turnos]);
    }

    /**
     * busquedaAlumnos2
     * Formulario alternativo de búsqueda de alumnos
     */
    public function busquedaAlumnos2(Request $request, $opc = null)
    {
        if (!$opc) {
            return redirect()->route('alumnos.index');
        }

        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $id_entidad = $ctx['id_entidad'] ?? null;
        $captura_actual = $ctx['captura_actual'] ?? session('captura_actual', 1);

        if (!$nivel) {
            return redirect()->route('login');
        }

        Escuela2::$displayField = 'escuela';
        $escuelasEstado = Escuela2::where('status', 'a')
            ->where('unica', '<', 2)
            ->where('id_entidad', $id_entidad)
            ->pluck('escuela', 'id');

        $semestre = [];

        if ($nivel == 2) {
            if ($opc == 't') {
                $semestre = ['8' => '8° Escolarizada', '12' => '12° Mixta'];
            } elseif ($opc == 'd') {
                for ($i = 1; $i <= 13; $i++) {
                    if (($i % 2) == ($captura_actual % 2)) {
                        $semestre[$i] = $i;
                    }
                }
            }
        } elseif ($nivel == 4) {
            for ($i = 1; $i <= 13; $i++) {
                if (($i % 2) == ($captura_actual % 2)) {
                    $semestre[$i] = $i;
                }
            }
        }

        $status = [];
        $url = '';

        if ($opc == 't') {
            $status = ['i' => 'Inscrito', 'x' => 'No Inscrito', 'd' => 'Baja Definitiva'];
            $url = route('bajas.listasAlumnos2', ['opc' => $opc]);
        } elseif ($opc == 'd') {
            $status = ['i' => 'Inscrito', 'x' => 'No Inscrito'];
            $url = route('bajas.listasAlumnos2', ['opc' => $opc]);
        }

        $tituloTabla = ['d' => ' Para la bajas definitivas', 't' => 'Para las Bajas Temporales'];
        $tituloPag = ['d' => ' DEFINITIVAS', 't' => ' TEMPORALES'];
        $anterior = route('alumnos.index');

        return view('bajas.busqueda_alumnos', compact(
            'semestre',
            'status',
            'opc',
            'url',
            'tituloTabla',
            'tituloPag',
            'escuelasEstado',
            'anterior'
        ));
    }

    /**
     * listasAlumnos2
     * Regresa la lista de alumnos según los criterios de búsqueda alternativos (AJAX)
     */
    public function listasAlumnos2(Request $request, $opc = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $captura_actual = $ctx['captura_actual'] ?? session('captura_actual', 1);

        if (!$nivel) {
            return redirect()->route('login');
        }

        $id_escuela = null;
        if ($nivel == 2) {
            $id_escuela = $request->input('Busca.id_escuela');
        } elseif ($nivel == 4) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        }

        $campos = ['nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp', 'semestre', 'status', 'id_registro', 'status_conflictos', 'modalidad'];

        $buscaData = $request->input('Busca', []);
        $contador = 0;
        foreach ($buscaData as $valor) {
            if (empty($valor)) {
                $contador++;
            }
        }

        $alumnos = collect();
        if ($contador < count($buscaData) || count($buscaData) == 0) {
            $query = Historia::where('id_escuela', $id_escuela)
                ->where('captura', $captura_actual)
                ->where('status', $opc);

            foreach ($buscaData as $i => $valor) {
                if (!empty($valor)) {
                    $query->where($i, $valor);
                }
            }

            $alumnos = $query->select($campos)
                ->orderBy('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre')
                ->get();
        }

        $renglones = [];
        foreach ($alumnos as $alumno) {
            $idRegistro = $alumno->id_registro;
            $modalidad = $alumno->modalidad;
            $semestre = $alumno->semestre;
            $statusAlumno = $alumno->status;
            $statusConflictos = $alumno->status_conflictos;

            $boton = '';
            if ($nivel == 2) {
                if ($opc == 't') {
                    if ($modalidad == 'e' && $semestre == 8 && $statusAlumno != 't') {
                        // Lógica condicional original
                    } elseif ($modalidad == 'm' && $semestre == 12 && $statusAlumno != 't') {
                        // Lógica condicional original
                    }
                }
            } elseif ($nivel == 4) {
                if ($opc == 't') {
                    if ($modalidad == 'e' && $semestre <= 7 && $statusAlumno != 't') {
                        // Lógica condicional original
                    } elseif ($modalidad == 'm' && $semestre <= 11 && $statusAlumno != 't') {
                        // Lógica condicional original
                    }
                }
            }

            $optativaTipoBaja = $request->input('Optativa.tipo_baja');
            if ($statusConflictos != 'n' && $statusAlumno != 'd' || ($statusAlumno == 't' && $optativaTipoBaja == 'd')) {
                $alumno->accion = $boton;
            }

            $estilo = '';
            $estado = '';
            switch ($statusAlumno) {
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
            }

            $alumno->status = "<div style='{$estilo}'>{$estado}</div>";

            $indx = $idRegistro;
            $alumnoArray = $alumno->toArray();
            
            unset($alumnoArray['id_registro'], $alumnoArray['status_conflictos'], $alumnoArray['modalidad']);

            $renglones[$indx] = $alumnoArray;
        }

        $cabeceras = ['No.', 'nid', 'Primer Apellido', 'Segundo Apellido', 'Nombre', 'CURP', 'Sem', 'Estado'];

        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }
}