<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Escuela;
use App\Models\Escuela2;
use App\Models\EscuelaStatus;
use App\Models\EscuelaLicenciatura;
use App\Models\Historia;
use App\Models\Alumno;
use App\Models\AlumnoC;
use App\Models\Personal;
use App\Models\PersonalC;
use App\Models\Usuario;
use App\Models\Licenciatura;
use App\Models\Especialidad;
use App\Models\Entidad;
use App\Models\Municipio;
use App\Models\Localidad;
use App\Models\DomicilioEscuela;
use App\Models\Construccion;
use App\Models\SeguridadSocial;
use App\Models\Directorio;
use App\Models\Disfuncion;
use App\Models\ListaBeca;
use App\Models\RegistroEgresados;
use App\Models\Funcion;

class InformesController extends sibenController
{
    /**
     * Obtiene la verificación de firmas pendientes para alumnos no inscritos / no egresados.
     */
    protected function obtenerStatusFirmas(mixed $id_escuela, mixed $captura_actual): array
    {
        $cuenta = Historia::where('id_escuela', $id_escuela)
            ->where('captura', $captura_actual)
            ->where('status', 'x')
            ->where('modalidad', 'e')
            ->count();

        $firmas = $cuenta <= 0;
        $mensaje_firmas = $cuenta > 0 ? "Hay pendientes {$cuenta} alumnos en status X (No Inscritos)" : '';

        $cuenta2 = RegistroEgresados::where('id_escuela', $id_escuela)
            ->where('status', 'x')
            ->count();

        $firmas2 = $cuenta2 <= 0;
        $mensaje_firmas2 = $cuenta2 > 0 ? "Hay pendientes {$cuenta2} alumnos en status X (No Egresados)" : '';

        return compact('firmas', 'mensaje_firmas', 'firmas2', 'mensaje_firmas2');
    }

    /**
     * listadoAlumnos
     */
    public function listadoAlumnos()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;
        $captura_actual = $this->captura_actual ?? null;

        $escuela = Escuela::with('entidad')
            ->where('id', $id_escuela)
            ->select('id', 'escuela_key', 'escuela', 'clave_escuela', 'id_entidad')
            ->first();

        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();

        $captura = $status->captura ?? null;
        $oficializar = $status->oficializar ?? null;
        $oficializar_fecha = $status->oficializar_fecha ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';

        $title = 'Listado Alumnos, ' . $escuela_key;
        $encargados = $this->obtenerEncargados($id_escuela, $modulo, $id_renapo);

        $escuelaLicenciaturas = EscuelaLicenciatura::with(['licenciatura', 'especialidad'])
            ->where('id_escuela', $id_escuela)
            ->select('id_licenciatura', 'id_especialidad')
            ->groupBy('id_licenciatura', 'id_especialidad')
            ->get()
            ->toArray();

        $especial = [
            'EscuelaLicenciatura' => ['id_licenciatura' => 7, 'id_especialidad' => 0],
            'Licenciatura' => ['licenciatura' => 'Especial'],
            'Especialidad' => ['especialidad' => 'n/a']
        ];
        $escuelaLicenciaturas[] = $especial;

        foreach ($escuelaLicenciaturas as $i => $escuelaLicenciatura) {
            $id_lic = $escuelaLicenciatura['EscuelaLicenciatura']['id_licenciatura'] ?? $escuelaLicenciatura['id_licenciatura'] ?? null;
            $id_esp = $escuelaLicenciatura['EscuelaLicenciatura']['id_especialidad'] ?? $escuelaLicenciatura['id_especialidad'] ?? null;

            $alumnos = Historia::where('id_escuela', $id_escuela)
                ->where('status', 'i')
                ->where('id_licenciatura', $id_lic)
                ->where('id_especialidad', $id_esp)
                ->where('captura', $captura_actual)
                ->orderBy('semestre')
                ->orderBy('grupo')
                ->orderBy('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre')
                ->orderBy('id_especialidad')
                ->select('id', 'nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp', 'id_escuela', 'id_licenciatura', 'id_especialidad', 'semestre', 'modalidad', 'status')
                ->get()
                ->toArray();

            if (empty($alumnos)) {
                unset($escuelaLicenciaturas[$i]);
            } else {
                $escuelaLicenciaturas[$i]['Alumno'] = $alumnos;
            }
        }

        $especialidades = Especialidad::pluck('especialidad', 'id')->toArray();
        $firmasData = $this->obtenerStatusFirmas($id_escuela, $captura_actual);

        $ancho = '720';

        return view('informes.listado_alumnos', array_merge(
            compact(
                'title', 'encargados', 'escuela', 'escuelaLicenciaturas', 'ancho',
                'captura', 'especialidades', 'oficializar', 'oficializar_fecha'
            ),
            $firmasData
        ));
    }

    /**
     * listadoDocentes
     */
    public function listadoDocentes()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with('entidad')
            ->where('id', $id_escuela)
            ->select('id', 'escuela_key', 'escuela', 'clave_escuela', 'id_entidad')
            ->first();

        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();

        $captura = $status->captura ?? null;
        $oficializar = $status->oficializar ?? null;
        $oficializar_fecha = $status->oficializar_fecha ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';

        $title = 'Listado Docentes, ' . $escuela_key;
        $encargados = $this->obtenerEncargados($id_escuela, $modulo, $id_renapo);

        $escuelaLicenciaturas = EscuelaLicenciatura::with(['licenciatura', 'especialidad'])
            ->where('id_escuela', $id_escuela)
            ->select('id_licenciatura')
            ->groupBy('id_licenciatura')
            ->get()
            ->toArray();

        foreach ($escuelaLicenciaturas as $i => $escuelaLicenciatura) {
            $id_lic = $escuelaLicenciatura['id_licenciatura'] ?? $escuelaLicenciatura['EscuelaLicenciatura']['id_licenciatura'];

            $docentes = DB::select("
                SELECT
                    Personal.id, Personal.nombre, Personal.apellido_paterno, Personal.apellido_materno, Personal.curp, Personal.genero,
                    Personal.id_escuela, Personal.id_funcion, Personal.status,
                    Docente.id AS docente_id, Docente.id_personal,
                    AsignaturaDocente.id AS asignatura_docente_id, AsignaturaDocente.id_docente, AsignaturaDocente.id_asignatura,
                    Asignatura.curricular
                FROM personal2 AS Personal
                JOIN docentes AS Docente ON (Docente.id_personal = Personal.id)
                JOIN asignaturas_docentes AS AsignaturaDocente ON (AsignaturaDocente.id_docente = Docente.id_personal)
                JOIN cat_asignaturas AS Asignatura ON (AsignaturaDocente.id_asignatura = Asignatura.id)
                WHERE Personal.id_escuela = ?
                AND AsignaturaDocente.id_licenciatura = ?
                AND Personal.status = 'a'
                GROUP BY Personal.curp, Asignatura.curricular DESC
            ", [$id_escuela, $id_lic]);

            $escuelaLicenciaturas[$i]['Docente'] = json_decode(json_encode($docentes), true);
        }

        $ancho = '720';
        $firmasData = $this->obtenerStatusFirmas($id_escuela, $this->captura_actual ?? null);

        return view('informes.listado_docentes', array_merge(
            compact(
                'title', 'encargados', 'escuela', 'escuelaLicenciaturas', 'ancho',
                'captura', 'oficializar', 'oficializar_fecha'
            ),
            $firmasData
        ));
    }

    /**
     * listadoPersonal
     */
    public function listadoPersonal()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with('entidad')
            ->where('id', $id_escuela)
            ->select('id', 'escuela_key', 'escuela', 'clave_escuela', 'id_entidad')
            ->first();

        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();

        $captura = $status->captura ?? null;
        $oficializar = $status->oficializar ?? null;
        $oficializar_fecha = $status->oficializar_fecha ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';

        $title = 'Listado Personal, ' . $escuela_key;
        $encargados = $this->obtenerEncargados($id_escuela, $modulo, $id_renapo);

        $personal = Personal::where('id_escuela', $id_escuela)
            ->whereIn('id_funcion', range(1, 10))
            ->where('status', 'a')
            ->orderBy('id_funcion')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $funciones = Funcion::pluck('funcion', 'id')->toArray();
        $ancho = '720';
        $firmasData = $this->obtenerStatusFirmas($id_escuela, $this->captura_actual ?? null);

        return view('informes.listado_personal', array_merge(
            compact(
                'title', 'encargados', 'escuela', 'personal', 'funciones', 'ancho',
                'captura', 'oficializar', 'oficializar_fecha'
            ),
            $firmasData
        ));
    }

    /**
     * listadoBecas
     */
    public function listadoBecas(mixed $id_escuela = null)
    {
        $ctx = $this->contexto();
        if (!$id_escuela) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        }
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with('entidad')
            ->where('id', $id_escuela)
            ->select('id', 'escuela_key', 'escuela', 'clave_escuela', 'id_entidad')
            ->first();

        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();

        $captura = $status->captura ?? null;
        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';

        $title = 'Listado Becas, ' . $escuela_key;
        $encargados = $this->obtenerEncargados($id_escuela, $modulo, $id_renapo);

        $becas = ListaBeca::where('id_escuela', $id_escuela)
            ->where('semestre', '7')
            ->where('captura', $this->captura_actual ?? null)
            ->orderBy('id_licenciatura')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $licenciaturas = Licenciatura::pluck('licenciatura', 'id')->toArray();
        $especialidades = Especialidad::pluck('especialidad', 'id')->toArray();
        $situaciones = ['n' => 'AB', 'r' => 'AR'];
        $entidades = Entidad::pluck('id_renapo', 'id')->toArray();

        $municipios = Municipio::where('id_entidad', $escuela->id_entidad ?? 0)
            ->pluck('municipio', 'id')
            ->toArray();

        $escuelas_licenciaturas = EscuelaLicenciatura::where('id_escuela', $id_escuela)
            ->groupBy('id_licenciatura', 'id_especialidad')
            ->pluck('clave', 'id_licenciatura')
            ->toArray();

        $ancho = '1280';
        $tipo_beca = 'ÚNICA';
        $monto_mensual = '700';
        $factor_periodo = '3.5';

        return view('informes.listado_becas2', compact(
            'title', 'licenciaturas', 'especialidades', 'situaciones', 'entidades',
            'municipios', 'encargados', 'escuela', 'ancho', 'becas', 'captura',
            'escuelas_licenciaturas', 'tipo_beca', 'monto_mensual', 'factor_periodo'
        ));
    }

    /**
     * estadisticaAlumnos
     */
    
    public function estadisticaAlumnos()
    {
        $ctx = $this->contexto();

        $nivel = (int) ($ctx['nivel'] ?? 0);
        $idEscuela = $ctx['id_escuela'] ?? null;
        $idEntidad = $ctx['id_entidad'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        // El reporte antiguo se generaba para una escuela seleccionada.
        if (!$idEscuela) {
            abort(403, 'No hay una escuela seleccionada para generar el reporte.');
        }

        if (!in_array($nivel, [1, 2, 3, 4, 6], true)) {
            abort(403, 'No tienes permiso para consultar estas estadísticas.');
        }

        $escuela = Escuela::with('entidad')
            ->select('id', 'escuela_key', 'escuela', 'clave_escuela', 'id_entidad')
            ->findOrFail($idEscuela);

        // Los niveles estatales y de enlace deben respetar su entidad.
        if (
            in_array($nivel, [2, 6], true) &&
            (string) $escuela->id_entidad !== (string) $idEntidad
        ) {
            abort(403, 'La escuela no pertenece a tu entidad.');
        }

        // Recuperar el estado de captura y oficialización.
        $status = EscuelaStatus::where('id_escuela', $idEscuela)
            ->where('modulo', $modulo)
            ->first();

        $captura = $status->captura ?? null;
        $oficializar = $status->oficializar ?? null;
        $oficializarFecha = $status->oficializar_fecha ?? null;

        // El reporte antiguo utiliza estos seis semestres pares.
        $semestres = ['2', '4', '6', '8', '10', '12'];

        $totales = [
            'hombres' => 0,
            'mujeres' => 0,
            'general' => 0,
        ];

        foreach ($semestres as $semestre) {
            $totales[$semestre] = 0;
        }

        // Obtener las licenciaturas y especialidades de la escuela.
        $ofertas = EscuelaLicenciatura::with([
            'licenciatura',
            'especialidad'
        ])
            ->where('id_escuela', $idEscuela)
            ->select(
                'id_escuela',
                'id_licenciatura',
                'id_especialidad',
                'modalidad'
            )
            ->distinct()
            ->get();

        // Incluir Educación Especial sin especialidad cuando corresponda.
        $cuentaEspecial = Historia::where('id_escuela', $idEscuela)
            ->where('id_licenciatura', 7)
            ->where('id_especialidad', 0)
            ->where('status', 'i')
            ->where('captura', $this->captura_actual)
            ->count();

        if ($cuentaEspecial > 0) {
            $ofertas->push((object) [
                'id_escuela' => $idEscuela,
                'id_licenciatura' => 7,
                'id_especialidad' => 0,
                'modalidad' => 'e',
                'licenciatura' => (object) [
                    'licenciatura' => 'Especial',
                ],
                'especialidad' => (object) [
                    'especialidad' => '',
                ],
            ]);
        }

        $modalidades = [
            'e' => 'Escolarizada',
            'm' => 'Mixta',
        ];

        $filas = [];

        foreach ($ofertas as $oferta) {
            $queryBase = Historia::where('id_escuela', $idEscuela)
                ->where('status', 'i')
                ->where('id_licenciatura', $oferta->id_licenciatura)
                ->where('id_especialidad', $oferta->id_especialidad)
                ->where('modalidad', $oferta->modalidad)
                ->where('captura', $this->captura_actual);

            $hombres = (clone $queryBase)
                ->where('genero', 'h')
                ->count();

            $mujeres = (clone $queryBase)
                ->where('genero', 'm')
                ->count();

            $cuentas = [
                'hombres' => $hombres,
                'mujeres' => $mujeres,
                'general' => $hombres + $mujeres,
            ];

            foreach ($semestres as $semestre) {
                $cantidad = (clone $queryBase)
                    ->where('semestre', $semestre)
                    ->count();

                $cuentas[$semestre] = $cantidad;
                $totales[$semestre] += $cantidad;
            }

            $totales['hombres'] += $hombres;
            $totales['mujeres'] += $mujeres;
            $totales['general'] += $cuentas['general'];

            // No mostrar licenciaturas sin alumnos.
            if ($cuentas['general'] === 0) {
                continue;
            }

            $filas[] = [
                'licenciatura' => $oferta->licenciatura->licenciatura
                    ?? ($oferta->id_licenciatura == 7 ? 'Especial' : 'Sin nombre'),

                'especialidad' => $oferta->especialidad->especialidad ?? '',

                'modalidad' => $modalidades[$oferta->modalidad] ?? $oferta->modalidad,

                'cuentas' => $cuentas,
            ];
        }

        // Obtener los responsables de la escuela.
        $idRenapo = $escuela->entidad->id_renapo ?? '';

        $encargadosOriginales = $this->obtenerEncargados(
            $idEscuela,
            $modulo,
            $idRenapo
        );

        $encargados = [
            'capturista' => '',
            'supervisor' => '',
            'enlace' => '',
        ];

        if (is_array($encargadosOriginales)) {
            $encargados['capturista'] =
                $encargadosOriginales['capturista']
                ?? $encargadosOriginales[4]
                ?? '';

            $encargados['supervisor'] =
                $encargadosOriginales['supervisor']
                ?? $encargadosOriginales[3]
                ?? '';

            $encargados['enlace'] =
                $encargadosOriginales['enlace']
                ?? $encargadosOriginales[2]
                ?? '';
        }

        // Crear el documento PDF.
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('SIBEN');
        $pdf->SetAuthor('SIBEN');
        $pdf->SetTitle('Listado Acumulado Estadístico de Alumnos');
        $pdf->SetSubject('Estadística de alumnos');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetFooterMargin(8);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->SetFont('helvetica', '', 8);

        $pdf->AddPage();

        $escapar = function ($valor) {
            return htmlspecialchars(
                (string) $valor,
                ENT_QUOTES,
                'UTF-8'
            );
        };

        $nombreEscuela = $escapar($escuela->escuela);
        $claveEscuela = $escapar($escuela->clave_escuela);
        $entidad = $escapar($escuela->entidad->entidad ?? '');

        $html = '
        <h2 style="text-align:center;">
            Secretaría de Educación Pública
        </h2>

        <h3 style="text-align:center;">
            Subsecretaría de Educación Superior<br>
            Dirección General de Educación Superior para el Magisterio
        </h3>

        <h3 style="text-align:center;">
            Sistema de Información Básica de la Educación Normal
        </h3>

        <h3 style="text-align:center;">
            Listado Acumulado Estadístico de Alumnos
        </h3>

        <br>

        <table cellpadding="4">
            <tr>
                <td><b>Fecha:</b> ' . date('d/m/Y') . '</td>
                <td><b>Hora:</b> ' . date('H:i') . '</td>
            </tr>
            <tr>
                <td colspan="2"><b>Estado:</b> ' . $entidad . '</td>
            </tr>
            <tr>
                <td colspan="2"><b>Clave del Centro de Trabajo:</b> ' . $claveEscuela . '</td>
            </tr>
            <tr>
                <td colspan="2"><b>Nombre de la Escuela:</b> ' . $nombreEscuela . '</td>
            </tr>
        </table>

        <br>

        <table border="1" cellpadding="4">
            <thead>
                <tr style="background-color:#e9ecef;">
                    <th rowspan="2" width="19%"><b>LICENCIATURA</b></th>
                    <th rowspan="2" width="15%"><b>ESPECIALIDAD O ÁREA</b></th>
                    <th rowspan="2" width="11%"><b>MODALIDAD</b></th>
                    <th colspan="2" width="10%"><b>SEXO</b></th>
                    <th colspan="6" width="36%"><b>TOTAL DE INSCRITOS POR SEMESTRE</b></th>
                    <th rowspan="2" width="9%"><b>TOTAL</b></th>
                </tr>
                <tr style="background-color:#e9ecef;">
                    <th width="5%">H</th>
                    <th width="5%">M</th>';

        foreach ($semestres as $semestre) {
            $html .= '<th width="6%">' . $semestre . 'o</th>';
        }

        $html .= '
                </tr>
            </thead>
            <tbody>';

        foreach ($filas as $fila) {
            $html .= '
                <tr>
                    <td>EDUCACIÓN ' . $escapar(mb_strtoupper($fila['licenciatura'])) . '</td>
                    <td>' . $escapar(mb_strtoupper($fila['especialidad'])) . '</td>
                    <td>' . $escapar($fila['modalidad']) . '</td>
                    <td align="center">' . $fila['cuentas']['hombres'] . '</td>
                    <td align="center">' . $fila['cuentas']['mujeres'] . '</td>';

            foreach ($semestres as $semestre) {
                $html .= '
                    <td align="center">' . $fila['cuentas'][$semestre] . '</td>';
            }

            $html .= '
                    <td align="center"><b>' . $fila['cuentas']['general'] . '</b></td>
                </tr>';
        }

        if (empty($filas)) {
            $html .= '
                <tr>
                    <td colspan="11" align="center">
                        No se encontraron alumnos inscritos para la captura actual.
                    </td>
                </tr>';
        }

        $html .= '
                <tr style="background-color:#e9ecef;">
                    <td colspan="3"><b>TOTALES</b></td>
                    <td align="center"><b>' . $totales['hombres'] . '</b></td>
                    <td align="center"><b>' . $totales['mujeres'] . '</b></td>';

        foreach ($semestres as $semestre) {
            $html .= '
                    <td align="center"><b>' . $totales[$semestre] . '</b></td>';
        }

        $html .= '
                    <td align="center"><b>' . $totales['general'] . '</b></td>
                </tr>
            </tbody>
        </table>

        <br><br>';

        // Aviso de captura abierta.
        if ($captura) {
            $html .= '
            <p style="text-align:center;color:red;">
                <b>Este informe no será admitido hasta que finalice la captura.</b>
            </p>';
        }

        // Responsables.
        $html .= '
        <br>
        <table cellpadding="5">
            <tr>
                <td><b>CAPTURÓ:</b> ' . $escapar($encargados['capturista']) . '</td>
            </tr>
            <tr>
                <td><b>SUPERVISÓ:</b> ' . $escapar($encargados['supervisor']) . '</td>
            </tr>
            <tr>
                <td><b>ENLACE DE LA AUTORIDAD EDUCATIVA ESTATAL:</b> '
                    . $escapar($encargados['enlace']) . '</td>
            </tr>
        </table>';

        if ($oficializar) {
            $fechaOficializacion = $oficializarFecha
                ? date('d/m/Y', strtotime($oficializarFecha))
                : '';

            $html .= '
            <br>
            <p style="text-align:center;">
                Este informe fue oficializado por el supervisor de la escuela
                el día: ' . $escapar($fechaOficializacion) . '
            </p>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');

        $nombreArchivo = 'Estadistica_alumnos_' .
            preg_replace('/[^A-Za-z0-9_-]/', '_', $escuela->clave_escuela) .
            '.pdf';

        return response($pdf->Output($nombreArchivo, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ]);
    }


    /**
     * estadisticaDocentes
     */
    public function estadisticaDocentes()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with('entidad')
            ->where('id', $id_escuela)
            ->select('id', 'escuela_key', 'escuela', 'clave_escuela', 'id_entidad')
            ->first();

        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();

        $captura = $status->captura ?? null;
        $oficializar = $status->oficializar ?? null;
        $oficializar_fecha = $status->oficializar_fecha ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';
        $encargados = $this->obtenerEncargados($id_escuela, $modulo, $id_renapo);

        $escuelaLicenciaturas = EscuelaLicenciatura::with(['licenciatura', 'especialidad'])
            ->where('id_escuela', $id_escuela)
            ->get();

        $licenciaturas = $escuelaLicenciaturas->pluck('licenciatura.licenciatura', 'id_licenciatura')->toArray();
        $especialidades = ['0' => 'n/a'] + $escuelaLicenciaturas->pluck('especialidad.especialidad', 'id_especialidad')->toArray();

        $cuenta_hombres = DB::select("
            SELECT COUNT(DISTINCT id_docente) AS cuenta, AsignaturaDocente.id_licenciatura, AsignaturaDocente.id_especialidad, AsignaturaDocente.modalidad
            FROM asignaturas_docentes AS AsignaturaDocente
            JOIN cat_asignaturas AS Asignatura ON (AsignaturaDocente.id_asignatura=Asignatura.id)
            WHERE id_docente IN (
                SELECT docentes.id_personal FROM docentes JOIN personal2 ON (docentes.id_personal=personal2.id)
                WHERE id_escuela = ? AND status='a' AND genero='h'
            )
            GROUP BY id_licenciatura, id_especialidad, modalidad
            ORDER BY id_licenciatura, id_especialidad, modalidad
        ", [$id_escuela]);

        $cuenta_mujeres = DB::select("
            SELECT COUNT(DISTINCT id_docente) AS cuenta, AsignaturaDocente.id_licenciatura, AsignaturaDocente.id_especialidad, AsignaturaDocente.modalidad
            FROM asignaturas_docentes AS AsignaturaDocente
            JOIN cat_asignaturas AS Asignatura ON (AsignaturaDocente.id_asignatura=Asignatura.id)
            WHERE id_docente IN (
                SELECT docentes.id_personal FROM docentes JOIN personal2 ON (docentes.id_personal=personal2.id)
                WHERE id_escuela = ? AND status='a' AND genero='m'
            )
            GROUP BY id_licenciatura, id_especialidad, modalidad
            ORDER BY id_licenciatura, id_especialidad, modalidad
        ", [$id_escuela]);

        $cuentas = [];
        foreach ($cuenta_hombres as $h) {
            $cuentas[$h->id_licenciatura][$h->id_especialidad][$h->modalidad]['h'] = ($cuentas[$h->id_licenciatura][$h->id_especialidad][$h->modalidad]['h'] ?? 0) + $h->cuenta;
        }

        foreach ($cuenta_mujeres as $m) {
            $cuentas[$m->id_licenciatura][$m->id_especialidad][$m->modalidad]['m'] = ($cuentas[$m->id_licenciatura][$m->id_especialidad][$m->modalidad]['m'] ?? 0) + $m->cuenta;
        }

        $cuenta_total = DB::select("
            SELECT COUNT(*) AS cuenta FROM (
                SELECT Personal.id
                FROM personal2 AS Personal
                JOIN docentes AS Docente ON (Docente.id_personal=Personal.id)
                JOIN asignaturas_docentes AS AsignaturaDocente ON (AsignaturaDocente.id_docente=Docente.id_personal)
                JOIN cat_asignaturas AS Asignatura ON (AsignaturaDocente.id_asignatura=Asignatura.id)
                WHERE Personal.id_escuela = ? AND Personal.status='a'
                GROUP BY Personal.curp
            ) AS tabla
        ", [$id_escuela]);

        $total = $cuenta_total[0]->cuenta ?? 0;
        $modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta'];
        $ancho = '720';
        $firmasData = $this->obtenerStatusFirmas($id_escuela, $this->captura_actual ?? null);

        return view('informes.estadistica_docentes', array_merge(
            compact(
                'escuela', 'encargados', 'licenciaturas', 'especialidades', 'cuentas',
                'ancho', 'modalidades', 'total', 'captura', 'oficializar', 'oficializar_fecha'
            ),
            $firmasData
        ));
    }

    /**
     * estadisticaPersonal
     */
    public function estadisticaPersonal()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with('entidad')
            ->where('id', $id_escuela)
            ->select('id', 'escuela_key', 'escuela', 'clave_escuela', 'id_entidad')
            ->first();

        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();

        $captura = $status->captura ?? null;
        $oficializar = $status->oficializar ?? null;
        $oficializar_fecha = $status->oficializar_fecha ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';
        $encargados = $this->obtenerEncargados($id_escuela, $modulo, $id_renapo);

        $funciones = Funcion::pluck('funcion', 'id')->toArray();

        $personal = Personal::where('id_escuela', $id_escuela)
            ->where('status', 'a')
            ->where('id_funcion', '<>', '5')
            ->orderBy('id_funcion')
            ->get();

        $cuenta_docentes_hombres = Personal::where('id_escuela', $id_escuela)
            ->where('id_funcion', 5)
            ->where('status', 'a')
            ->where('genero', 'h')
            ->count();

        $cuenta_docentes_mujeres = Personal::where('id_escuela', $id_escuela)
            ->where('id_funcion', 5)
            ->where('status', 'a')
            ->where('genero', 'm')
            ->count();

        $cuentaTotal = 0;
        $cuentas = [];
        $i = 1;

        foreach ($funciones as $id_funcion => $funcion) {
            $hombres = 0;
            $mujeres = 0;
            foreach ($personal as $persona) {
                if ($persona->id_funcion == $id_funcion) {
                    (strtolower($persona->genero) == 'h') ? $hombres++ : $mujeres++;
                }
            }
            $cuentas[$i]['hombres'] = $hombres;
            $cuentas[$i]['mujeres'] = $mujeres;
            $cuentaTotal += ($hombres + $mujeres);
            $i++;
        }

        $cuentas[5]['hombres'] = $cuenta_docentes_hombres;
        $cuentas[5]['mujeres'] = $cuenta_docentes_mujeres;
        $cuentaTotal += ($cuentas[5]['hombres'] + $cuentas[5]['mujeres']);

        $ancho = '720';
        $firmasData = $this->obtenerStatusFirmas($id_escuela, $this->captura_actual ?? null);

        return view('informes.estadistica_personal', array_merge(
            compact(
                'escuela', 'encargados', 'ancho', 'personal', 'funciones', 'cuentas',
                'cuentaTotal', 'captura', 'oficializar', 'oficializar_fecha'
            ),
            $firmasData
        ));
    }

    /**
     * datosEscuela
     */
    public function datosEscuela()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with('entidad')->find($id_escuela);
        $domicilioEscuela = DomicilioEscuela::where('id_escuela', $id_escuela)->first();

        $localidad = null;
        $entidad = null;
        $municipio = null;
        $seguridad_social = null;

        if ($domicilioEscuela) {
            $localidad = Localidad::where('id_localidad', $domicilioEscuela->cve_loc)
                ->where('id_municipio', $domicilioEscuela->cve_mun)
                ->where('id_entidad', $domicilioEscuela->cve_ent)
                ->first();

            $entidad = Entidad::find($domicilioEscuela->cve_ent);
            $municipio = Municipio::find($domicilioEscuela->cve_mun);
            $seguridad_social = SeguridadSocial::find($domicilioEscuela->id_seguridad_social);
        }

        $status = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();
        $captura = $status->captura ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';
        $encargados = $this->obtenerEncargados($id_escuela, $modulo, $id_renapo);

        $licenciaturas = Licenciatura::pluck('licenciatura', 'id')->toArray();
        $especialidades = Especialidad::pluck('especialidad', 'id')->toArray();

        $directivos = Personal::where('id_escuela', $id_escuela)
            ->where('id_funcion', '<=', '3')
            ->where('status', 'a')
            ->orderBy('id_funcion')
            ->get();

        $construcciones = Construccion::pluck('construccion', 'id')->toArray();

        $turnosLicenciatura = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno'];
        $modalidades = ['m' => 'Mixta', 'e' => 'Escolarizada', 'i' => 'Intensiva'];
        $tipos = ['m' => 'Maestría', 'd' => 'Doctorado', 'e' => 'Especialidad', 'c' => 'Educación Continua', 'i' => 'SEPA Inglés'];
        $sostenimientos = ['p' => 'Público', 'q' => 'Particular'];
        $turnosEscuela = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];
        $sino = ['s' => 'Sí', 'n' => 'No'];
        $ancho = '720';

        return view('informes.datos_escuela', compact(
            'seguridad_social', 'sino', 'escuela', 'localidad', 'entidad', 'municipio',
            'domicilioEscuela', 'encargados', 'ancho', 'construcciones', 'licenciaturas',
            'especialidades', 'directivos', 'turnosLicenciatura', 'turnosEscuela',
            'modalidades', 'tipos', 'sostenimientos', 'captura'
        ));
    }

    /**
     * avanceNacional
     */
    public function avanceNacional()
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        if ($nivel != 1) {
            return redirect()->route('siben.index');
        }

        $entidades = Entidad::where('id', '<>', '0')->pluck('entidad', 'id_renapo')->toArray();
        $cuentas = [];

        foreach ($entidades as $id_renapo => $entidadNombre) {
            $cuentaAlumnosEstado = 0;
            $cuentaPersonalEstado = 0;
            $cantidadAlumnosEstado = 0;
            $cantidadPersonalEstado = 0;

            $entidadObj = Entidad::where('id_renapo', $id_renapo)->first();

            if ($entidadObj) {
                $escuelas = Escuela::where('id_entidad', $entidadObj->id)
                    ->where('status', 'a')
                    ->get();

                foreach ($escuelas as $escuela) {
                    $cuentaPersonal = Personal::where('id_escuela', $escuela->id)
                        ->where('status', 'a')
                        ->distinct('curp')
                        ->count('curp');

                    $cuentaAlumnos = Historia::where('id_escuela', $escuela->id)
                        ->where('status', 'i')
                        ->count();

                    $cuentaAlumnosEstado += $cuentaAlumnos;
                    $cuentaPersonalEstado += $cuentaPersonal;
                    $cantidadAlumnosEstado += $escuela->cantidad_alumnos;
                    $cantidadPersonalEstado += $escuela->cantidad_personal;
                }
            }

            $cuentas[$id_renapo] = [
                'cuentaAlumnosEstado' => $cuentaAlumnosEstado,
                'cantidadAlumnosEstado' => $cantidadAlumnosEstado,
                'cuentaPersonalEstado' => $cuentaPersonalEstado,
                'cantidadPersonalEstado' => $cantidadPersonalEstado
            ];
        }

        return view('informes.avance_nacional', compact('entidades', 'cuentas'));
    }

    /**
     * informeNacional
     */
    public function informeNacional(mixed $id_renapo = 'DF')
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        if ($nivel != 1) {
            return redirect()->route('siben.index');
        }

        $entidades = Entidad::where('id', '<>', '0')->pluck('entidad', 'id_renapo')->toArray();

        $queryEntidades = Entidad::where('id', '<>', '0');
        if ($id_renapo) {
            $queryEntidades->where('id_renapo', $id_renapo);
        }
        $entidades_escuelas = $queryEntidades->pluck('entidad', 'id_renapo')->toArray();

        $cuentas = [];
        $escuelas = [];

        foreach ($entidades_escuelas as $renapoKey => $entidadNombre) {
            $cuentaAlumnosEstado = 0;
            $cuentaPersonalEstado = 0;
            $cantidadAlumnosEstado = 0;
            $cantidadPersonalEstado = 0;

            $entidadObj = Entidad::where('id_renapo', $renapoKey)->first();

            if ($entidadObj) {
                $escuelasEntidad = Escuela2::where('id_entidad', $entidadObj->id)
                    ->where('status', 'a')
                    ->whereIn('unica', ['0', '1'])
                    ->where('modulo', $modulo)
                    ->get();

                foreach ($escuelasEntidad as $escuela) {
                    $cuentaPersonal = Personal::where('id_escuela', $escuela->id)
                        ->where('status', 'a')
                        ->distinct('curp')
                        ->count('curp');

                    $cuentaAlumnos = Historia::where('id_escuela', $escuela->id)
                        ->where('status', 'i')
                        ->where('captura', $this->captura_actual ?? null)
                        ->count();

                    $escuela->cuenta_alumnos = $cuentaAlumnos;
                    $escuela->cuenta_personal = $cuentaPersonal;

                    $cuentaAlumnosEstado += $cuentaAlumnos;
                    $cuentaPersonalEstado += $cuentaPersonal;
                    $cantidadAlumnosEstado += $escuela->cantidad_alumnos;
                    $cantidadPersonalEstado += $escuela->cantidad_personal;
                }

                $escuelas[$renapoKey] = $escuelasEntidad;
            }

            $cuentas[$renapoKey] = [
                'cuentaAlumnosEstado' => $cuentaAlumnosEstado,
                'cantidadAlumnosEstado' => $cantidadAlumnosEstado,
                'cuentaPersonalEstado' => $cuentaPersonalEstado,
                'cantidadPersonalEstado' => $cantidadPersonalEstado
            ];
        }

        return view('informes.informe_nacional', compact(
            'entidades_escuelas', 'entidades', 'escuelas', 'cuentas'
        ));
    }

    /**
     * csvAlumnos
     */
    public function csvAlumnos()
    {
        $ctx = $this->contexto();
        if (($ctx['nivel'] ?? null) != 1) {
            return redirect()->route('siben.index');
        }

        $csv = (new Alumno())->csv(1);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="alumnos.csv"');
    }

    /**
     * csvPersonal
     */
    public function csvPersonal()
    {
        $ctx = $this->contexto();
        if (($ctx['nivel'] ?? null) != 1) {
            return redirect()->route('siben.index');
        }

        $csv = (new Personal())->csv(1);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="personal.csv"');
    }

    /**
     * csvDocentes
     */
    public function csvDocentes()
    {
        $ctx = $this->contexto();
        if (($ctx['nivel'] ?? null) != 1) {
            return redirect()->route('siben.index');
        }

        return view('informes.csv_docentes');
    }

    /**
     * estadisticas
     */
    public function estadisticas()
    {
        $ctx = $this->contexto();

        $nivel = (int) ($ctx['nivel'] ?? 0);

        return view('informes.estadisticas', compact('nivel'));
    }

    /**
     * directorio
     */
    public function directorio(Request $request, mixed $id_entidad = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        if ($nivel == 2 || $nivel == 6) {
            $id_entidad = $ctx['id_entidad'] ?? null;
            $cabeceras = ['Función', 'Cargo', 'Título', 'Nombre', 'Primer Ap.', 'Segundo Ap.', 'Correo Electrónico', '', ''];
        } else {
            return redirect()->route('siben.index');
        }

        $inputDirectorio = $request->input('Directorio');
        if ($request->isMethod('post') && !empty($inputDirectorio)) {
            Directorio::create($inputDirectorio);
            return redirect()->route('informes.directorio', ['id_entidad' => $id_entidad]);
        }

        $entidad = Entidad::find($id_entidad);
        $funciones = Disfuncion::pluck('funcion', 'id')->toArray();

        $directorio = Directorio::where('id_entidad', $id_entidad)
            ->orderBy('id_funcion')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        return view('informes.directorio', compact('nivel', 'entidad', 'funciones', 'directorio', 'cabeceras'));
    }

    /**
     * editaDirectivo
     */
    public function editaDirectivo(Request $request, mixed $id_directivo = null)
    {
        if (!$id_directivo) {
            return redirect()->back();
        }

        $ctx = $this->contexto();
        $inputDirectorio = $request->input('Directorio');

        if ($request->isMethod('post') && !empty($inputDirectorio)) {
            $directivoObj = Directorio::find($id_directivo);
            if ($directivoObj) {
                $directivoObj->update($inputDirectorio);
            }
            $id_entidad = $ctx['id_entidad'] ?? null;
            return redirect()->route('informes.directorio', ['id_entidad' => $id_entidad]);
        }

        $funciones = Disfuncion::pluck('funcion', 'id')->toArray();
        $data = Directorio::find($id_directivo);

        return view('informes.edita_directivo', compact('id_directivo', 'funciones', 'data'));
    }

    /**
     * eliminaDirectivo
     */
    public function eliminaDirectivo(mixed $id_directivo = null)
    {
        if ($id_directivo) {
            Directorio::where('id', $id_directivo)->delete();
        }

        return redirect()->back();
    }

    /**
     * listadoBecasEntidad
     */
    public function listadoBecasEntidad(mixed $id_entidad = null)
    {
        $ctx = $this->contexto();
        if (!$id_entidad) {
            $id_entidad = $ctx['id_entidad'] ?? null;
        }

        $entidad = Entidad::find($id_entidad);
        $modulo = $ctx['modulo'] ?? null;

        $escuelas = Escuela2::where('id_entidad', $id_entidad)
            ->where('modulo', $modulo)
            ->where('status', 'a')
            ->where('unica', '<', 2)
            ->where('sostenimiento', 'p')
            ->orderBy('escuela')
            ->pluck('escuela', 'id')
            ->toArray();

        $becas = ListaBeca::where('id_entidad', $id_entidad)
            ->where('semestre', '7')
            ->orderBy('escuela')
            ->orderBy('id_licenciatura')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $title = 'Listado Becas Entidad';
        $licenciaturas = Licenciatura::pluck('licenciatura', 'id')->toArray();
        $especialidades = ['0' => ''] + Especialidad::pluck('especialidad', 'id')->toArray();
        $situaciones = ['n' => 'AB', 'r' => 'AR'];
        $municipios = Municipio::where('id_entidad', $id_entidad)->pluck('municipio', 'id')->toArray();

        $escuelas_licenciaturas = [];
        foreach ($escuelas as $id_esc => $escNombre) {
            $escuelas_licenciaturas[$id_esc] = EscuelaLicenciatura::where('id_escuela', $id_esc)
                ->groupBy('id_licenciatura', 'id_especialidad')
                ->pluck('clave', 'id_licenciatura')
                ->toArray();
        }

        $ancho = '1280';
        $tipo_beca = 'ÚNICA';
        $monto_mensual = '700';
        $factor_periodo = '3.5';
        $captura = 0;

        return view('informes.listado_becas_entidad', compact(
            'title', 'licenciaturas', 'especialidades', 'situaciones', 'municipios',
            'escuelas', 'ancho', 'becas', 'captura', 'escuelas_licenciaturas',
            'tipo_beca', 'monto_mensual', 'factor_periodo', 'entidad'
        ));
    }

    /**
     * listaBecariosEntidad
     */
    public function listaBecariosEntidad(mixed $semestre = 7)
    {
        $ctx = $this->contexto();
        $modulo = $ctx['modulo'] ?? null;

        $entidades = Entidad::where('id', '<', 33)->get();
        $archivos = [];

        return view('informes.lista_becarios', compact('archivos'));
    }

    /**
     * listaBecariosEscuela
     */
    public function listaBecariosEscuela(mixed $id_escuela = null, mixed $semestre = 7)
    {
        if (!$id_escuela) {
            return [];
        }

        return ListaBeca::where('id_escuela', $id_escuela)
            ->where('semestre', $semestre)
            ->where('captura', $this->captura_actual ?? null)
            ->orderBy('id_licenciatura')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get()
            ->toArray();
    }

    /**
     * informeBecariosFuturos
     */
    public function informeBecariosFuturos(Request $request, mixed $informe = null, mixed $id_escuela = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $id_entidad = $ctx['id_entidad'] ?? null;

        $licenciaturas = [2, 4, 6, 8, 13];
        $semestres = [4, 6, 8];

        $condiciones_alumnos = [];

        switch ($informe) {
            case 2:
                $licenciaturas = [2, 4, 6, 7, 8, 13];
                $semestres = [8];
                break;
            case 3:
                $licenciaturas = [2, 4, 6, 7, 8, 13];
                $semestres = [6];
                break;
            case 4:
                $licenciaturas = [2, 4, 6, 7, 8, 13];
                $semestres = [4];
                break;
            case 1:
            default:
                $licenciaturas = [2, 4, 6, 8, 13];
                $semestres = [4, 6, 8];
                $condiciones_alumnos[] = ['id_especialidad', 'NOT IN', [41, 42]];
                break;
        }

        $cabeceras = [''];
        $escuela = [];

        switch ($nivel) {
            case 2:
                $cabeceras[] = 'Escuela';
                if ($id_escuela) {
                    $escuelaObj = Escuela2::find($id_escuela);
                    if ($escuelaObj) {
                        $escuela[$escuelaObj->id] = $escuelaObj->escuela;
                    }
                } else {
                    $escuela = Escuela2::where('id_entidad', $id_entidad)
                        ->where('status', 'a')
                        ->where('unica', '<', 2)
                        ->pluck('escuela', 'id')
                        ->toArray();
                    $id_escuela = array_keys($escuela);
                }
                break;
            case 3:
            case 4:
                $escuela[] = $ctx['escuela'] ?? '';
                $id_escuela = $ctx['id_escuela'] ?? null;
                break;
        }

        $cabeceras[] = 'Alumno';
        $cabeceras[] = 'CURP';
        $cabeceras[] = 'Licenciatura';
        $cabeceras[] = 'Especialidad';
        $cabeceras[] = 'Semestre';

        $queryAlumnos = Historia::query();

        if (is_array($id_escuela)) {
            $queryAlumnos->whereIn('id_escuela', $id_escuela);
        } elseif ($id_escuela) {
            $queryAlumnos->where('id_escuela', $id_escuela);
        }

        $alumnos = $queryAlumnos->whereIn('semestre', $semestres)
            ->whereIn('id_licenciatura', $licenciaturas)
            ->where('status', 'i')
            ->where('captura', $this->captura_actual ?? null)
            ->orderBy('id_escuela')
            ->orderBy('semestre')
            ->orderBy('id_licenciatura')
            ->orderBy('id_especialidad')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->select('id_escuela', 'nombre', 'apellido_paterno', 'apellido_materno', 'curp', 'id_licenciatura', 'id_especialidad', 'semestre')
            ->get();

        $licenciaturasMap = Licenciatura::pluck('licenciatura', 'id')->toArray();
        $especialidadesMap = ['0' => 'n/a'] + Especialidad::pluck('especialidad', 'id')->toArray();
        $anterior = true;

        return view('informes.informe_becarios_futuros', compact(
            'cabeceras', 'alumnos', 'escuela', 'licenciaturasMap', 'especialidadesMap', 'anterior'
        ));
    }

    /**
     * informesBecarios
     */
    public function informesBecarios(mixed $id_escuela = null)
    {
        $ctx = $this->contexto();
        $anterior = false;

        if (!$id_escuela) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        } else {
            $anterior = true;
        }

        $escuela = Escuela::where('id', $id_escuela)->first();
        $sostenimiento = $escuela->sostenimiento ?? '';
        $nivel = $ctx['nivel'] ?? null;

        $opcionesMenu = [
            ['informes', 'Examen Junio 2010', route('informes.informeBecariosFuturos', ['informe' => 1, 'id_escuela' => $id_escuela]), 0]
        ];

        if ($sostenimiento == 'p' || $nivel == 2) {
            $opcionesMenu[] = ['informes', 'Becados Ene-Jun 2010', route('informes.informeBecariosFuturos', ['informe' => 2, 'id_escuela' => $id_escuela]), 0];
            $opcionesMenu[] = ['informes', 'Becados Sep-Dic 2010', route('informes.informeBecariosFuturos', ['informe' => 3, 'id_escuela' => $id_escuela]), 0];
            $opcionesMenu[] = ['informes', 'Becados Sep-Dic 2011', route('informes.informeBecariosFuturos', ['informe' => 4, 'id_escuela' => $id_escuela]), 0];
        }

        return view('informes.informes_becarios', compact('opcionesMenu', 'anterior'));
    }

    /**
     * listaCurp
     */
    public function listaCurp()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with('entidad')->find($id_escuela);
        $escuela_key = $escuela->escuela_key ?? '';
        $title = 'Listado Alumnos, ' . $escuela_key;

        $alumnos = AlumnoC::where('id_escuela', $id_escuela)
            ->whereIn('status_curp', ['x'])
            ->whereIn('status_comparacion', [6, 7])
            ->orderBy('primer_apellido')
            ->orderBy('segundo_apellido')
            ->orderBy('nombre_c')
            ->orderBy('genero_c')
            ->orderBy('fecha_nacimiento_c')
            ->orderBy('entidad_nacimiento_c')
            ->get();

        $ancho = '720';

        return view('informes.lista_curp', compact('alumnos', 'title', 'escuela', 'ancho'));
    }

    /**
     * listaCurpPer
     */
    public function listaCurpPer()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $escuela = Escuela::with('entidad')->find($id_escuela);
        $escuela_key = $escuela->escuela_key ?? '';
        $title = 'Listado Personal, ' . $escuela_key;

        $personas = PersonalC::where('id_escuela', $id_escuela)
            ->whereIn('status_curp', ['x'])
            ->whereIn('status_comparacion', [6, 7])
            ->orderBy('primer_apellido')
            ->orderBy('segundo_apellido')
            ->orderBy('nombre_c')
            ->orderBy('genero_c')
            ->get();

        $ancho = '720';

        return view('informes.lista_curp_per', compact('personas', 'title', 'escuela', 'ancho'));
    }

    /**
     * listadoAsignatura
     */
    public function listadoAsignatura()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with('entidad')->find($id_escuela);
        $status = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();
        $captura = $status->captura ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';

        $title = 'Asignaturas Doc., ' . $escuela_key;
        $encargados = $this->obtenerEncargados($id_escuela, $modulo, $id_renapo);

        $docentes = DB::select("
            SELECT
                Personal.id, Personal.nombre, Personal.apellido_paterno, Personal.apellido_materno, Personal.curp, Personal.genero, Personal.id_escuela,
                Personal.id_funcion, Personal.status, Docente.id AS docente_id, AsignaturaDocente.id AS asignatura_docente_id, AsignaturaDocente.id_docente,
                AsignaturaDocente.id_asignatura, Asignatura.curricular, Asignatura.asignatura, AsignaturaDocente.semestre, AsignaturaDocente.grupo,
                AsignaturaDocente.horas, cl.licenciatura
            FROM personal2 AS Personal
            JOIN docentes AS Docente ON (Docente.id_personal = Personal.id)
            JOIN asignaturas_docentes AS AsignaturaDocente ON (AsignaturaDocente.id_docente = Docente.id_personal)
            JOIN cat_asignaturas AS Asignatura ON (AsignaturaDocente.id_asignatura = Asignatura.id)
            JOIN cat_licenciaturas AS cl ON (AsignaturaDocente.id_licenciatura = cl.id)
            WHERE Personal.id_escuela = ?
            AND Personal.status = 'a'
            ORDER BY Personal.apellido_paterno, Personal.apellido_materno, Personal.curp DESC
        ", [$id_escuela]);

        $ancho = '720';

        return view('informes.listado_asignatura', compact(
            'title', 'encargados', 'escuela', 'docentes', 'ancho', 'captura'
        ));
    }

    /**
     * listadoContra
     */
    public function listadoContra()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with('entidad')->find($id_escuela);
        $status = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();
        $captura = $status->captura ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';

        $title = 'Contrataciones Doc., ' . $escuela_key;
        $encargados = $this->obtenerEncargados($id_escuela, $modulo, $id_renapo);

        $docentes = DB::select("
            SELECT
                Personal.id, Personal.nombre, Personal.apellido_paterno, Personal.apellido_materno,
                cc.contratacion AS forma_contratacion, cc2.contratacion AS tiempo_contratacion,
                Personal.curp, Personal.genero, cn.nivel_estudio, cat.nombre AS categoria_nombre, Docente.titulo,
                cd.horas_grupo, cd.clave_contratacion
            FROM personal2 AS Personal
            JOIN docentes AS Docente ON (Docente.id_personal = Personal.id)
            JOIN contrataciones_docentes AS cd ON (cd.id_personal = Personal.id)
            JOIN cat_contrataciones AS cc ON (cc.id = cd.forma_contratacion)
            JOIN cat_contrataciones AS cc2 ON (cc2.id = cd.tiempo_contratacion)
            JOIN cat_nivel_estudios AS cn ON (cn.id = Docente.id_nivel_estudio)
            LEFT JOIN cat_categorias AS cat ON (cat.id = cd.categoria)
            WHERE Personal.id_escuela = ?
            AND Personal.status = 'a'
            AND Personal.id_funcion IN ('1','2','3','4','5','6')
            ORDER BY Personal.apellido_paterno, Personal.apellido_materno, Personal.curp, cn.nivel_estudio DESC
        ", [$id_escuela]);

        $ancho = '720';

        return view('informes.listado_contra', compact(
            'title', 'encargados', 'escuela', 'docentes', 'ancho', 'captura'
        ));
    }

    /**
     * listadoEgresados
     */
    public function listadoEgresados()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $escuela = Escuela::with('entidad')->find($id_escuela);
        $status = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();
        $captura = $status->captura ?? null;

        $escuela_key = $escuela->escuela_key ?? '';
        $id_renapo = $escuela->entidad->id_renapo ?? '';

        $title = 'Listado Egresados, ' . $escuela_key;
        $encargados = $this->obtenerEncargados($id_escuela, $modulo, $id_renapo);

        $docentes = DB::select("
            SELECT
                alumnos.numero_identificador, alumnos.nombre, alumnos.apellido_paterno, alumnos.apellido_materno, alumnos.curp,
                alumnos.genero, alumnos.edad, registro_egresados.nid, registro_egresados.id_escuela, registro_egresados.id_licenciatura,
                registro_egresados.status, registro_egresados.captura
            FROM alumnos
            JOIN registro_egresados ON (alumnos.numero_identificador = registro_egresados.nid)
            WHERE registro_egresados.status = 'x'
            AND registro_egresados.id_escuela = ?
            ORDER BY alumnos.apellido_paterno, alumnos.apellido_materno DESC
        ", [$id_escuela]);

        $ancho = '720';

        return view('informes.listado_egresados', compact(
            'title', 'encargados', 'escuela', 'docentes', 'ancho', 'captura'
        ));
    }

    /**
     * Devuelve los encargados de una escuela: enlace estatal, supervisor y capturista.
     */
    protected function obtenerEncargados(mixed $id_escuela, mixed $modulo, mixed $id_renapo): array
    {
        $mapa = [];

        $filas = Usuario::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->orderBy('status', 'asc')
            ->orderBy('nombre', 'asc')
            ->select('nivel', 'nombre', 'status')
            ->get();

        $filasEnlace = Usuario::where('id_renapo', $id_renapo)
            ->where('modulo', $modulo)
            ->where('nivel', '2')
            ->orderBy('status', 'asc')
            ->orderBy('nombre', 'asc')
            ->select('nivel', 'nombre', 'status')
            ->get();

        foreach ($filas->concat($filasEnlace) as $fila) {
            $nivel = $fila->nivel;
            $nombre = trim((string)$fila->nombre);
            if (!isset($mapa[$nivel]) || $mapa[$nivel] === '') {
                $mapa[$nivel] = $nombre;
            }
        }

        $variables = [];
        if (!empty($mapa[2])) { $variables['enlace'] = $mapa[2]; }
        if (!empty($mapa[3])) { $variables['supervisor'] = $mapa[3]; }
        if (!empty($mapa[4])) { $variables['capturista'] = $mapa[4]; }

        return $variables;
    }
}