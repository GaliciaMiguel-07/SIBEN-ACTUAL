<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Posgrado;
use App\Models\Autorizaciones;
use App\Models\AutorizacionesPosgrado;
use App\Models\GeneracionesPosgrado;
use App\Models\Entidad;
use App\Models\Escuela;
use App\Models\EscuelaStatus;
use App\Models\Municipio;
use App\Models\Localidad;
use App\Models\DomicilioEscuela;
use App\Models\Usuario;

class PosgradosController extends sibenController
{
    /**
     * index
     */
    public function index()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura = $ctx['captura'] ?? null;

        $mensaje = null;
        if (!$captura) {
            $mensaje = 'La captura está finalizada';
        }

        $opcionesMenu = [
            ['posgrados', 'Agregar Posgrado', 'AgregarPosgrado', 0],
            ['Posgrados', 'Editar Posgrados', 'EscuelaPosgrados', 'p'],
            ['posgrados', 'Agregar Generaciones', 'EscuelaPosgrados', 'g'],
            ['Posgrados', 'Agregar Autorizaciones', 'EscuelaPosgrados', 'a'],
            ['posgrados', 'Escuela Información', 'EscuelaDetalle', $id_escuela],
        ];

        return view('posgrados.index', compact('id_escuela', 'opcionesMenu', 'mensaje'));
    }

    /**
     * AgregarPosgrado
     */
    public function AgregarPosgrado(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura = $ctx['captura'] ?? null;

        if ($captura != 1) {
            return redirect()->route('siben.index');
        }

        $posgradoData = $request->input('Posgrado');

        if ($request->isMethod('post') && !empty($posgradoData)) {
            $anio = $posgradoData['anio'] ?? '';
            $mes = $posgradoData['mes'] ?? '';
            $dia = $posgradoData['dia'] ?? '';

            $posgradoData['fecha_inicio_operacion'] = "{$anio}-{$mes}-{$dia}";
            $posgradoData['id_escuela'] = $id_escuela;

            $nuevoPosgrado = Posgrado::create($posgradoData);

            return redirect()->route('posgrados.posgradoInfo', ['id_posgrado' => $nuevoPosgrado->id]);
        }

        $anterior = route('posgrados.index');
        $valor = false;

        $year = [];
        for ($i = date('Y'); $i >= 1975; $i--) {
            $year[$i] = $i;
        }

        $dia = [];
        for ($i = 1; $i <= 31; $i++) {
            $key = $i < 10 ? '0' . $i : (string)$i;
            $dia[$key] = $key;
        }

        $mes = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        $modalidades = ['e' => 'Escolarizado', 'n' => 'No Escolarizado', 'm' => 'Mixta'];
        $tipo_periodo = [
            '1' => 'Mensual', '2' => 'Bimestral', '3' => 'Trimestral',
            '4' => 'Cuatrimestre', '5' => 'Pentamestre', '6' => 'Semestre', '7' => 'Anual'
        ];

        $periodo = [];
        for ($i = 1; $i < 14; $i++) {
            $periodo[$i] = $i;
        }

        $nivel = ['1' => 'Especialidad', '2' => 'Maestría', '3' => 'Doctorado'];
        $nivel_ciees = ['1' => 'No evaluado', '2' => 'Acreditación', '3' => 'Nivel 1', '4' => 'Nivel 2'];
        $orientacion = ['1' => 'Profesional', '2' => 'Investigación'];

        return view('posgrados.agregar_posgrado', compact(
            'anterior', 'valor', 'id_escuela', 'year', 'dia', 'mes',
            'modalidades', 'tipo_periodo', 'periodo', 'nivel', 'nivel_ciees', 'orientacion'
        ));
    }

    /**
     * posgradoInfo
     */
    public function posgradoInfo(Request $request, mixed $id_posgrado = null)
    {
        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;
        $id_escuela = $ctx['id_escuela'] ?? null;

        if ($captura != 1) {
            return redirect()->route('siben.index');
        }

        if (!$id_posgrado) {
            return redirect()->route('posgrados.index');
        }

        if ($request->isMethod('post') && !empty($request->all())) {
            return redirect()->route('posgrados.escuelaPosgrados', ['tipo' => 'a']);
        }

        $posgradoObj = Posgrado::find($id_posgrado);

        if (!$posgradoObj) {
            return redirect()->route('posgrados.index');
        }

        $posgradoData = $posgradoObj->toArray();

        $mapModalidad = ['m' => 'Mixta', 'e' => 'Escolarizada', 'n' => 'No escolarizada'];
        $posgradoData['nombremodalidad'] = $mapModalidad[$posgradoData['modalidad']] ?? '';

        $mapNivel = ['1' => 'Especialidad', '2' => 'Maestría', '3' => 'Doctorado'];
        $posgradoData['nivel'] = $mapNivel[$posgradoData['nivel_posgrado']] ?? '';

        $mapCiees = ['1' => 'No evaluado', '2' => 'Acreditación', '3' => 'Nivel 1', '4' => 'Nivel 2'];
        $posgradoData['nombre_nivel'] = $mapCiees[$posgradoData['nivel_ciees']] ?? '';

        $mapOrientacion = ['1' => 'Profesional', '2' => 'Investigación'];
        $posgradoData['nombre_orientacion'] = $mapOrientacion[$posgradoData['orientacion']] ?? '';

        $posgrado = [$posgradoData];

        $tipo_periodo = [
            '1' => 'Mensual', '2' => 'Bimestral', '3' => 'Trimestral',
            '4' => 'Cuatrimestre', '5' => 'Pentamestre', '6' => 'Semestre', '7' => 'Anual'
        ];

        $autorizaciones = Autorizaciones::pluck('autorizacion', 'id')->toArray();
        $posgradoAuto = AutorizacionesPosgrado::where('id_posgrado', $id_posgrado)->get()->toArray();
        $voluntarioAutorizacion = !empty($posgradoAuto);

        $generaciones = GeneracionesPosgrado::where('id_posgrado', $id_posgrado)
            ->pluck('generacion', 'generacion')
            ->toArray();

        $year = [];
        for ($i = date('Y'); $i >= 1975; $i--) {
            $year[$i] = $i;
        }

        $dia = [];
        for ($i = 1; $i <= 31; $i++) {
            $key = $i < 10 ? '0' . $i : (string)$i;
            $dia[$key] = $key;
        }

        $mes = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        $anterior = route('posgrados.index');

        return view('posgrados.posgrado_info', compact(
            'anterior', 'id_posgrado', 'id_escuela', 'tipo_periodo', 'posgrado',
            'autorizaciones', 'posgradoAuto', 'voluntarioAutorizacion', 'generaciones',
            'year', 'dia', 'mes'
        ));
    }

    /**
     * EditaAutorizacion
     */
    public function EditaAutorizacion(Request $request, mixed $id_autorizacion = null, mixed $id_posgrado = null)
    {
        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;

        if ($captura != 1) {
            return redirect()->route('siben.index');
        }

        if (!$id_autorizacion || !$id_posgrado) {
            return redirect()->route('posgrados.index');
        }

        $inputAuto = $request->input('AutorizacionesPosgrado');

        if ($request->isMethod('post') && !empty($inputAuto)) {
            $anio = $inputAuto['anio'] ?? '';
            $mes = $inputAuto['mes'] ?? '';
            $dia = $inputAuto['dia'] ?? '';

            $inputAuto['fecha_autorizacion'] = "{$anio}-{$mes}-{$dia}";

            $autoRecord = AutorizacionesPosgrado::find($id_autorizacion);
            if ($autoRecord) {
                $autoRecord->update($inputAuto);
            }

            return redirect()->route('posgrados.posgradoInfo', ['id_posgrado' => $id_posgrado]);
        }

        $autorizacionObj = AutorizacionesPosgrado::where('id', $id_autorizacion)
            ->where('id_posgrado', $id_posgrado)
            ->first();

        $autorizacion = [];
        $data = [];

        if ($autorizacionObj) {
            $autoArr = $autorizacionObj->toArray();
            if (!empty($autoArr['fecha_autorizacion'])) {
                $fechaParts = explode('-', $autoArr['fecha_autorizacion']);
                $autoArr['anio'] = $fechaParts[0] ?? '';
                $autoArr['mes'] = $fechaParts[1] ?? '';
                $autoArr['dia'] = $fechaParts[2] ?? '';
            }
            $autorizacion = [$autoArr];
            $data = $autoArr;
        }

        $year = [];
        for ($i = date('Y'); $i >= 1975; $i--) {
            $year[$i] = $i;
        }

        $dia = [];
        for ($i = 1; $i <= 31; $i++) {
            $key = $i < 10 ? '0' . $i : (string)$i;
            $dia[$key] = $key;
        }

        $mes = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        return view('posgrados.edita_autorizacion', compact(
            'id_autorizacion', 'id_posgrado', 'autorizacion', 'data', 'year', 'dia', 'mes'
        ));
    }

    /**
     * EscuelaPosgrados
     */
    public function EscuelaPosgrados(?string $tipo = null)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura = $ctx['captura'] ?? null;

        if ($captura != 1) {
            return redirect()->route('siben.index');
        }

        $posgradosList = Posgrado::where('id_escuela', $id_escuela)->get();

        $posgrados = [];
        foreach ($posgradosList as $apoyoInfo) {
            $posArr = $apoyoInfo->toArray();

            $mapModalidad = [
                'p' => 'Presencial', 'd' => 'A Distancia', 'm' => 'Mixta',
                'e' => 'Escolarizada', 'n' => 'No Escolarizada'
            ];
            $posArr['nombremodalidad'] = $mapModalidad[$posArr['modalidad']] ?? '';

            $mapNivel = [1 => 'Especialidad', 2 => 'Maestría', 3 => 'Doctorado'];
            $posArr['nombrenivel'] = $mapNivel[$posArr['nivel_posgrado']] ?? '';

            $posgrados[] = ['Posgrado' => $posArr];
        }

        $mapTitulo = ['a' => 'Autorizaciones', 'g' => 'Generaciones', 'p' => 'Prosgrados'];
        $titulo = $mapTitulo[$tipo] ?? '';

        $cabeceras = ['Nivel Posgrado&nbsp;', 'Nombre del programa&nbsp;', 'Modalidad&nbsp;', $titulo, '&nbsp;'];
        $anterior = route('posgrados.index');

        return view('posgrados.escuela_posgrados', compact(
            'anterior', 'tipo', 'posgrados', 'cabeceras'
        ));
    }

    /**
     * GeneracionesPosgrado
     */
    public function GeneracionesPosgrado(Request $request, mixed $id_posgrado = null)
    {
        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;

        if ($captura != 1) {
            return redirect()->route('siben.index');
        }

        if (!$id_posgrado) {
            return redirect()->route('posgrados.index');
        }

        if ($request->isMethod('post') && !empty($request->all())) {
            return redirect()->route('posgrados.escuelaPosgrados', ['tipo' => 'g']);
        }

        $nombre = Posgrado::where('id', $id_posgrado)
            ->select('posgrado', 'fecha_inicio_operacion')
            ->first();

        $anio_inicio = substr($nombre->fecha_inicio_operacion ?? date('Y'), 0, 4);

        $generaciones = GeneracionesPosgrado::where('id_posgrado', $id_posgrado)->get()->toArray();
        $valor = !empty($generaciones);

        $year = [];
        for ($i = date('Y'); $i >= $anio_inicio; $i--) {
            $year[$i] = $i;
        }

        $year2 = [];
        for ($i = (date('Y') + 2); $i >= $anio_inicio; $i--) {
            $year2[$i] = $i;
        }

        $argumentos = [
            ['GeneracionesPosgradoTermino', 'agregaGeneracion', '2', '2']
        ];

        $anterior = route('posgrados.escuelaPosgrados', ['tipo' => 'g']);
        $ocultarDatos = true;

        return view('posgrados.generaciones_posgrado', compact(
            'anterior', 'id_posgrado', 'nombre', 'generaciones', 'valor',
            'year', 'year2', 'ocultarDatos', 'argumentos'
        ));
    }

    /**
     * EliminarGeneracion
     */
    public function EliminarGeneracion(mixed $id_generacion = null, mixed $id_posgrado = null)
    {
        if (!$id_posgrado || !$id_generacion) {
            return redirect()->route('posgrados.index')->with('error', 'No cumple con todos los criterios para eliminar la generación');
        }

        GeneracionesPosgrado::where('id_posgrado', $id_posgrado)
            ->where('id', $id_generacion)
            ->delete();

        return redirect()->route('posgrados.generacionesPosgrado', ['id_posgrado' => $id_posgrado]);
    }

    /**
     * EditarGeneracion
     */
    public function EditarGeneracion(Request $request, mixed $id_generacion = null, mixed $id_posgrado = null)
    {
        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;

        $inputGen = $request->input('GeneracionesPosgrado');

        if (!$id_generacion && !$id_posgrado && !empty($inputGen)) {
            $id_generacion = $inputGen['id'] ?? null;
            $id_posgrado = $inputGen['id_posgrado'] ?? null;
            $inputGen = null;
        }

        if ($captura != 1 || !$id_posgrado || !$id_generacion) {
            return redirect()->route('siben.index');
        }

        $nombre = Posgrado::where('id', $id_posgrado)
            ->select('posgrado', 'fecha_inicio_operacion')
            ->first();

        $anio_inicio = substr($nombre->fecha_inicio_operacion ?? date('Y'), 0, 4);

        if ($request->isMethod('post') && !empty($inputGen)) {
            $anoInicio = $inputGen['ano_inicio'] ?? '';
            $anioConclusion = $inputGen['anio_conclusion'] ?? '';

            $inputGen['ano_inicio'] = $anoInicio;
            $inputGen['ano_conclusion'] = $anioConclusion;
            $inputGen['generacion'] = "{$anoInicio}-{$anioConclusion}";

            $genObj = GeneracionesPosgrado::find($id_generacion);
            if ($genObj) {
                $genObj->update($inputGen);
            }

            return redirect()->route('posgrados.generacionesPosgrado', ['id_posgrado' => $id_posgrado]);
        }

        $generacionesObj = GeneracionesPosgrado::where('id_posgrado', $id_posgrado)
            ->where('id', $id_generacion)
            ->first();

        $generaciones = $generacionesObj ? $generacionesObj->toArray() : [];
        $data = $generaciones;
        $valor = !empty($generaciones);

        $year = [];
        for ($i = date('Y'); $i >= $anio_inicio; $i--) {
            $year[$i] = $i;
        }

        $year2 = [];
        for ($i = (date('Y') + 2); $i >= $anio_inicio; $i--) {
            $year2[$i] = $i;
        }

        $anterior = route('posgrados.escuelaPosgrados', ['tipo' => 'g']);

        return view('posgrados.editar_generacion', compact(
            'anterior', 'nombre', 'generaciones', 'data', 'valor', 'year', 'year2',
            'id_posgrado', 'id_generacion'
        ));
    }

    /**
     * MostrarPosgrados (Enlace Estatal)
     */
    public function MostrarPosgrados(mixed $parametro = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $modulo = $ctx['modulo'] ?? null;
        $id_renapo = $ctx['id_renapo'] ?? null;

        if (!$parametro || $nivel != 2) {
            return redirect()->route('siben.index');
        }

        $entidad = Entidad::where('id_renapo', $id_renapo)->first();
        $escuelas2 = [];

        if ($entidad) {
            $escuelas = Escuela::where('id_entidad', $entidad->id)
                ->where('unica', '<', 2)
                ->select('id', 'escuela', 'escuela_key')
                ->get();

            foreach ($escuelas as $escuela) {
                $posgrado_modulo = EscuelaStatus::where('modulo', $modulo)
                    ->where('id_escuela', $escuela->id)
                    ->where('status', 'a')
                    ->first();

                if ($posgrado_modulo && $posgrado_modulo->id_escuela == $escuela->id) {
                    $escuelas2[] = [
                        'Escuela' => $escuela->toArray(),
                        'EscuelaStatus' => $posgrado_modulo->toArray()
                    ];
                }
            }
        }

        $cabeceras = ['Clave Escuela&nbsp;&nbsp;&nbsp;', 'Escuela&nbsp;&nbsp;', '&nbsp;&nbsp;Detalles'];

        return view('posgrados.mostrar_posgrados', compact(
            'parametro', 'entidad', 'escuelas2', 'cabeceras'
        ));
    }

    /**
     * EscuelaDetalle
     */
    public function EscuelaDetalle(mixed $id_escuela = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        if (!$id_escuela || !in_array($nivel, [2, 3])) {
            return redirect()->route('siben.index');
        }

        $DomicilioEscuelaObj = DomicilioEscuela::where('id_escuela', $id_escuela)->first();
        $DomicilioEscuela = DomicilioEscuelaObj ? DomicilioEscuelaObj->toArray() : [];

        $escuela = Escuela::find($id_escuela);
        $localidad = null;

        if ($DomicilioEscuelaObj) {
            $localidad = Localidad::where('id_localidad', $DomicilioEscuelaObj->cve_loc)
                ->where('id_municipio', $DomicilioEscuelaObj->cve_mun)
                ->where('id_entidad', $DomicilioEscuelaObj->cve_ent)
                ->first();
        }

        $sostenimiento = ($escuela && $escuela->sostenimiento == 'p') ? 'Pública' : 'Particular';

        $mapTurno = ['m' => 'Matutino', 'v' => 'Vespertino', 'd' => 'Discontinuo'];
        $turno = $mapTurno[$escuela->turno ?? ''] ?? '';

        $posgrados = Posgrado::where('id_escuela', $id_escuela)->get();
        $cabeceraPosgrado = ['Posgrados', 'Estatal', 'DGESuM', 'RVOE', 'RDGP'];

        return view('posgrados.escuela_detalle', compact(
            'escuela', 'sostenimiento', 'turno', 'posgrados', 'cabeceraPosgrado',
            'DomicilioEscuela', 'localidad'
        ));
    }

    /**
     * autorzaciones (Petición AJAX)
     */
    public function autorzaciones(Request $request)
    {
        $inputAuto = $request->input('AutorizacionesPosgrado', []);

        $id_posgrado = $inputAuto['id_posgrado'] ?? 0;
        $id_autorizacion = $inputAuto['id_autorizacion'] ?? 0;
        $anio = $inputAuto['anio'] ?? '';
        $mes = $inputAuto['mes'] ?? '';
        $dia = $inputAuto['dia'] ?? '';
        $oficio = $inputAuto['no_oficio'] ?? '';
        $generacion = $inputAuto['generacion'] ?? '';

        $fecha = "{$anio}/{$mes}/{$dia}";
        $opciones = [];

        if ($id_posgrado > 0 && $id_autorizacion > 0) {
            $autorizacionExiste = AutorizacionesPosgrado::where('id_autorizacion', $id_autorizacion)
                ->where('id_posgrado', $id_posgrado)
                ->where('no_oficio', $oficio)
                ->first();

            if (!$autorizacionExiste) {
                $inputAuto['fecha_autorizacion'] = "{$anio}-{$mes}-{$dia}";
                $nuevaAuto = AutorizacionesPosgrado::create($inputAuto);

                $autorizacion = Autorizaciones::find($id_autorizacion);

                $urlEdita = route('posgrados.editaAutorizacion', [
                    'id_autorizacion' => $nuevaAuto->id,
                    'id_posgrado' => $id_posgrado
                ]);
                $boton = '<input type="button" value="Editar" onclick="locacion(\'' . $urlEdita . '\')" />';

                $urlElimina = route('posgrados.eliminarAutorizacion', [
                    'id_autorizacion' => $nuevaAuto->id,
                    'id_posgrado' => $id_posgrado
                ]);
                $boton2 = '<input type="button" value="Eliminar" onclick="locacion(\'' . $urlElimina . '\')" />';

                $opciones = [
                    $id_autorizacion,
                    $generacion,
                    $autorizacion->autorizacion ?? '',
                    $fecha,
                    $oficio,
                    $boton,
                    $boton2
                ];
            }
        }

        return view('elements.renglonEstudios', ['renglon' => $opciones]);
    }

    /**
     * generaciones (Petición AJAX)
     */
    public function generaciones(Request $request)
    {
        $inputGen = $request->input('GeneracionesPosgrado', []);

        $fecha_inicio = $inputGen['anio_inicio'] ?? '';
        $fecha_fin = $inputGen['anio_fin'] ?? '';
        $total = $inputGen['total_alumnos'] ?? '';
        $egresados = $inputGen['no_egresados'] ?? '';
        $titulados = $inputGen['no_titulados'] ?? '';
        $generacion = $inputGen['termino'] ?? '';
        $id_posgrado = $inputGen['id_posgrado'] ?? null;

        $estado = ($generacion == 't') ? 'Concluida' : 'En Curso';

        $query = GeneracionesPosgrado::where('id_posgrado', $id_posgrado);

        if (!empty($fecha_inicio)) {
            $query->where('ano_inicio', $fecha_inicio);
        }
        if (!empty($fecha_fin)) {
            $query->where('anio_conclusion', $fecha_fin);
        }
        if (!empty($generacion)) {
            $query->where('termino', $generacion);
            if ($generacion == 't') {
                $query->where('no_egresados', $egresados)->where('no_titulados', $titulados);
            }
        }

        $resultado = $query->select('id')->get();
        $opciones = [];

        if ($resultado->isEmpty()) {
            if ($generacion == 't') {
                if ($fecha_inicio != '' && $fecha_fin != '' && $egresados > 0 && $titulados >= 0 && $fecha_inicio < $fecha_fin) {
                    $inputGen['ano_inicio'] = $fecha_inicio;
                    $inputGen['anio_conclusion'] = $fecha_fin;
                    $inputGen['generacion'] = "{$fecha_inicio}-{$fecha_fin}";

                    $nuevaGen = GeneracionesPosgrado::create($inputGen);

                    $urlEdita = route('posgrados.eliminarGeneracion', [
                        'id_generacion' => $nuevaGen->id,
                        'id_posgrado' => $id_posgrado
                    ]);
                    $imagen = '<img src="/siben3/img/baja_def.png" onclick="locacion(\'' . $urlEdita . '\')" title="Eliminar" alt="" />';

                    $opciones = [$fecha_inicio, $fecha_fin, $total, $egresados, $titulados, $estado, $imagen];
                }
            } elseif ($generacion == 'a') {
                if ($fecha_inicio < $fecha_fin) {
                    $inputGen['no_titulados'] = 0;
                    $inputGen['no_egresados'] = 0;
                    $inputGen['ano_inicio'] = $fecha_inicio;
                    $inputGen['anio_conclusion'] = $fecha_fin;
                    $inputGen['generacion'] = "{$fecha_inicio}-{$fecha_fin}";

                    $nuevaGen = GeneracionesPosgrado::create($inputGen);

                    $urlEdita = route('posgrados.editarGeneracion', [
                        'id_generacion' => $nuevaGen->id,
                        'id_posgrado' => $id_posgrado
                    ]);
                    $boton = '<input type="button" value="Editar" onclick="locacion(\'' . $urlEdita . '\')" />';

                    $opciones = [$fecha_inicio, $fecha_fin, $total, 0, 0, $estado, $boton];
                }
            }
        }

        return view('elements.renglon', ['renglon' => $opciones]);
    }

    /**
     * parametros
     */
    public function parametros(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel = $ctx['nivel'] ?? null;
        $captura = $ctx['captura'] ?? null;

        if ($captura != 1 || $nivel != 3) {
            return redirect()->route('siben.index');
        }

        $inputUsuario = $request->input('Usuario');

        if ($request->isMethod('post') && !empty($inputUsuario)) {
            $inputUsuario['status'] = 'a';
            if (isset($inputUsuario['id'])) {
                $usr = Usuario::find($inputUsuario['id']);
                if ($usr) $usr->update($inputUsuario);
            }
        }

        $capturistas = Usuario::where('nivel', 4)
            ->where('modulo', 'p')
            ->where('id_escuela', $id_escuela)
            ->select('nombre', 'id_usuario', 'id')
            ->get();

        $anterior = route('posgrados.configuracion');

        return view('posgrados.parametros', compact('anterior', 'capturistas'));
    }

    /**
     * configuracion
     */
    public function configuracion(?string $cerrar = null)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura = $ctx['captura'] ?? null;

        $mensaje = null;
        if (!$captura) {
            $mensaje = 'La captura está finalizada';
        }

        $opcionesMenu = [
            ['Posgrados', 'Activar Capturista', 'parametros', 0],
            ['posgrados', 'Finalizar Captura', 'configuracion', 'c'],
            ['Posgrados', 'Editar domicilio', 'editarDatos', 0],
        ];

        if ($cerrar == 'c') {
            $parametros = EscuelaStatus::where('id_escuela', $id_escuela)
                ->where('modulo', 'p')
                ->first();

            if ($parametros) {
                $parametros->captura = 0;
                $parametros->save();
            }
        }

        return view('posgrados.configuracion', compact('opcionesMenu', 'mensaje'));
    }

    /**
     * editarPosgrado
     */
    public function editarPosgrado(Request $request, mixed $id_posgrado = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $id_escuela = $ctx['id_escuela'] ?? null;

        if (!$id_posgrado || $nivel != 3) {
            return redirect()->route('siben.index');
        }

        $posgradoObj = Posgrado::where('id', $id_posgrado)
            ->where('id_escuela', $id_escuela)
            ->first();

        $inputPosgrado = $request->input('Posgrado');

        if ($request->isMethod('post') && !empty($inputPosgrado)) {
            $anio = $inputPosgrado['anio'] ?? '';
            $mes = $inputPosgrado['mes'] ?? '';
            $dia = $inputPosgrado['dia'] ?? '';

            $inputPosgrado['fecha_inicio_operacion'] = "{$anio}-{$mes}-{$dia}";

            if ($posgradoObj) {
                $posgradoObj->update($inputPosgrado);
            }

            return redirect()->route('posgrados.escuelaPosgrados', ['tipo' => 'p']);
        }

        $data = [];
        if ($posgradoObj) {
            $posArr = $posgradoObj->toArray();
            if (!empty($posArr['fecha_inicio_operacion'])) {
                $fechaParts = explode('-', $posArr['fecha_inicio_operacion']);
                $posArr['anio'] = $fechaParts[0] ?? '';
                $posArr['mes'] = $fechaParts[1] ?? '';
                $posArr['dia'] = $fechaParts[2] ?? '';
            }
            $data = $posArr;
        }

        $year = [];
        for ($i = 2026; $i >= 1980; $i--) {
            $year[$i] = $i;
        }

        $dia = [];
        for ($i = 1; $i <= 31; $i++) {
            $key = $i < 10 ? '0' . $i : (string)$i;
            $dia[$key] = $key;
        }

        $mes = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        $modalidades = ['e' => 'Escolarizada', 'n' => 'No escolarizada', 'm' => 'Mixta'];
        $tipo_periodo = [
            '1' => 'Mensual', '2' => 'Bimestral', '3' => 'Trimestral',
            '4' => 'Cuatrimestre', '5' => 'Pentamestre', '6' => 'Semestre'
        ];

        $periodo = [];
        for ($i = 1; $i < 14; $i++) {
            $periodo[$i] = $i;
        }

        $nivelPos = ['1' => 'Especialidad', '2' => 'Maestría', '3' => 'Doctorado'];
        $nivel_ciees = ['1' => 'No evaluado', '2' => 'Acreditación', '3' => 'Nivel 1', '4' => 'Nivel 2'];
        $orientacion = ['1' => 'Profesional', '2' => 'Investigación'];

        return view('posgrados.editar_posgrado', compact(
            'data', 'year', 'dia', 'mes', 'modalidades', 'tipo_periodo', 'periodo',
            'nivelPos', 'nivel_ciees', 'orientacion', 'id_escuela', 'id_posgrado'
        ));
    }

    /**
     * editarDatos
     */
    public function editarDatos()
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $captura = $ctx['captura'] ?? null;
        $id_escuela = $ctx['id_escuela'] ?? null;
        $id_entidad = $ctx['id_entidad'] ?? null;
        $escuelaNombre = $ctx['escuela'] ?? '';

        if ($nivel != 3 || $captura != 1) {
            return redirect()->route('siben.index');
        }

        $title = 'Edición Datos Generales, ' . $escuelaNombre;

        $DomicilioEscuelaObj = DomicilioEscuela::where('id_escuela', $id_escuela)->first();
        $DomicilioEscuela = DomicilioEscuelaObj ? DomicilioEscuelaObj->toArray() : [];
        $id_municipio = $DomicilioEscuela['cve_mun'] ?? null;

        $vialidad = [
            'AMPLIACION' => 'AMPLIACION', 'ANDADOR' => 'ANDADOR', 'AVENIDA' => 'AVENIDA',
            'BOULEVARD' => 'BOULEVARD', 'CALLE' => 'CALLE', 'CALLEJON' => 'CALLEJON',
            'CALZADA' => 'CALZADA', 'CAMINO' => 'CAMINO', 'CARRETERA' => 'CARRETERA',
            'CERRADA' => 'CERRADA', 'CIRCUITO' => 'CIRCUITO', 'CIRCUNVALACION' => 'CIRCUNVALACION',
            'CONTINUACION' => 'CONTINUACION', 'CORREDOR' => 'CORREDOR', 'DIAGONAL' => 'DIAGONAL',
            'EJE VIAL' => 'EJE VIAL', 'PASAJE' => 'PASAJE', 'PEATONAL' => 'PEATONAL',
            'PERIFERICO' => 'PERIFERICO', 'PRIVADA' => 'PRIVADA', 'PROLONGACION' => 'PROLONGACION',
            'RETORNO' => 'RETORNO', 'VIADUCTO' => 'VIADUCTO'
        ];

        $asentamientos = [
            'AEROPUERTO' => 'AEROPUERTO', 'AMPLIACION' => 'AMPLIACION', 'BARRIO' => 'BARRIO',
            'CANTON' => 'CANTON', 'CIUDAD' => 'CIUDAD', 'CIUDAD INDUSTRIAL' => 'CIUDAD INDUSTRIAL',
            'COLONIA' => 'COLONIA', 'CONDOMINIO' => 'CONDOMINIO', 'CONJUNTO HABITACIONAL' => 'CONJUNTO HABITACIONAL',
            'CORREDOR INDUSTRIAL' => 'CORREDOR INDUSTRIAL', 'COTO' => 'COTO', 'CUARTEL' => 'CUARTEL',
            'EJIDO' => 'EJIDO', 'EXHACIENDA' => 'EXHACIENDA', 'FRACCION' => 'FRACCION',
            'FRACCIONAMIENTO' => 'FRACCIONAMIENTO', 'GRANJA' => 'GRANJA', 'HACIENDA' => 'HACIENDA',
            'INGENIO' => 'INGENIO', 'MANZANA' => 'MANZANA', 'PARAJE' => 'PARAJE', 'PARQUE INDUSTRIAL' => 'PARQUE INDUSTRIAL',
            'PRIVADA' => 'PRIVADA', 'PROLONGACION' => 'PROLONGACION', 'PUEBLO' => 'PUEBLO', 'PUERTO' => 'PUERTO',
            'RANCHERIA' => 'RANCHERIA', 'RANCHO' => 'RANCHO', 'REGION' => 'REGION', 'RESIDENCIAL' => 'RESIDENCIAL',
            'RINCONADA' => 'RINCONADA', 'SECCION' => 'SECCION', 'SECTOR' => 'SECTOR', 'SUPERMANZANA' => 'SUPERMANZANA',
            'UNIDAD' => 'UNIDAD', 'UNIDAD HABITACIONAL' => 'UNIDAD HABITACIONAL', 'VILLA' => 'VILLA',
            'ZONA FEDERAL' => 'ZONA FEDERAL', 'ZONA INDUSTRIAL' => 'ZONA INDUSTRIAL', 'ZONA MILITAR' => 'ZONA MILITAR'
        ];

        $admon = ['ESTATAL' => 'ESTATAL', 'FEDERAL' => 'FEDERAL'];
        $derecho = ['DE CUOTA' => 'DE CUOTA', 'LIBRE' => 'LIBRE'];
        $admon2 = ['BRECHA' => 'BRECHA', 'CAMINO' => 'CAMINO', 'TERRACERIA' => 'TERRACERIA', 'VEREDA' => 'VEREDA'];
        $margen = ['DERECHO' => 'DERECHO', 'IZQUIERDO' => 'IZQUIERDO'];

        $municipios = Municipio::where('id_entidad', $id_entidad)
            ->orderBy('municipio')
            ->pluck('municipio', 'id_municipio');

        $locs = Localidad::where('id_entidad', $id_entidad)
            ->where('id_municipio', $id_municipio)
            ->orderBy('localidad')
            ->pluck('localidad', 'id_localidad');

        $turnos = ['m' => 'Matutino', 'v' => 'Vespertino', 'd' => 'Discontinuo', 'n' => 'Nocturno'];
        $sostenimientos = ['p' => 'Público', 'q' => 'Particular'];

        $data = $DomicilioEscuela;

        return view('posgrados.editar_datos', compact(
            'title', 'DomicilioEscuela', 'data', 'municipios', 'asentamientos', 'locs',
            'admon', 'admon2', 'derecho', 'margen', 'id_entidad', 'turnos',
            'sostenimientos', 'vialidad'
        ));
    }

    /**
     * guardaDomicilio
     */
    public function guardaDomicilio(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();

        if ($status) {
            $status->domicilio = 1;
            $status->save();
        }

        $domicilioInput = $request->input('DomicilioEscuela', []);
        $escuelaInput = $request->input('Escuela', []);
        $vialidad = $domicilioInput['tipovial'] ?? '';

        if ($vialidad == 'CARRETERA') {
            $admon = $domicilioInput['admon'] ?? '';
            $derecho = $domicilioInput['derecho'] ?? '';
            $codigo = $domicilioInput['codigo'] ?? '';
            $tramo = $domicilioInput['tramo'] ?? '';
            $cadena = $domicilioInput['cadena'] ?? '';

            if (!empty($codigo) && !empty($tramo) && !empty($cadena)) {
                $domicilioInput['nomvial'] = "{$admon} {$derecho} {$codigo} TRAMO {$tramo} KILOMETRO {$cadena}";
            }
        }

        if ($vialidad == 'CAMINO') {
            $admon = $domicilioInput['admon2'] ?? '';
            $tramo = $domicilioInput['tramo2'] ?? '';
            $margen = $domicilioInput['margen'] ?? '';
            $cadena = $domicilioInput['cadena2'] ?? '';

            if (!empty($tramo) && !empty($cadena)) {
                $domicilioInput['nomvial'] = "{$admon} {$tramo} MARGEN {$margen} KILOMETRO {$cadena}";
            }
        }

        if (!empty($escuelaInput)) {
            $escuelaObj = Escuela::find($id_escuela);
            if ($escuelaObj) {
                $escuelaObj->update($escuelaInput);
            }
        }

        $domicilioObj = DomicilioEscuela::where('id_escuela', $id_escuela)->first();
        if ($domicilioObj) {
            $saved = $domicilioObj->update($domicilioInput);
        } else {
            $domicilioInput['id_escuela'] = $id_escuela;
            $saved = DomicilioEscuela::create($domicilioInput);
        }

        if (!$saved) {
            return response('Error: no se guardó la información', 400);
        }

        return redirect()->route('posgrados.editarDatos');
    }

    /**
     * eliminarAutorizacion
     */
    public function eliminarAutorizacion(mixed $id_autorizacion = null, mixed $id_posgrado = null)
    {
        if ($id_autorizacion) {
            AutorizacionesPosgrado::where('id', $id_autorizacion)->delete();
        }

        return redirect()->route('posgrados.posgradoInfo', ['id_posgrado' => $id_posgrado]);
    }

    /**
     * eliminarPosgrado
     */
    public function eliminarPosgrado(mixed $id_posgrado = null)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;

        if ($id_posgrado) {
            Posgrado::where('id', $id_posgrado)
                ->where('id_escuela', $id_escuela)
                ->delete();
        }

        return redirect()->route('posgrados.escuelaPosgrados', ['tipo' => 'p']);
    }
}