<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\sibenController;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Entidad;
use App\Models\Funcion;
use App\Models\Escuela2;
use App\Models\Personal;
use App\Models\Docente;
use App\Models\NivelEstudio;
use App\Models\AreaConocimiento;
use App\Models\Alumno;
use Illuminate\Support\Facades\DB;

class AdminController extends sibenController
{
    /**
     * Panel de administración (nivel 1).
     * Equivale a AdminController::index() del sistema viejo, que armaba
     * el arreglo $menu con las opciones de Alumnos y Personal.
     */
    public function index()
    {
        $menu = [
            'Alumnos' => [
                ['titulo' => 'Alta',        'ruta' => 'admin.alumnos.alta'],
                ['titulo' => 'Edición',     'ruta' => 'admin.alumnos.busqueda'],
            ],
            'Personal' => [
                ['titulo' => 'Alta',            'ruta' => 'admin.personal.alta'],
                ['titulo' => 'Edición/Activar', 'ruta' => 'admin.personal.busqueda'],
                ['titulo' => 'Jubilar',         'ruta' => 'admin.personal.jubilacion'],
                ['titulo' => 'Jubilados',       'ruta' => 'admin.personal.jubilados'],
            ],
        ];

        return view('admin.index', compact('menu'));
    }
    // Paso 1: Alta de alumnos

    /**
 * Muestra el formulario de alta de alumnos.
 */
public function agregaAlumno()
{
    $entidadesId = DB::table('cat_entidades')
    ->where('id', '!=', 33)
    ->orderBy('entidad')
    ->pluck('entidad', 'id');

    $entidadesRenapo = DB::table('cat_entidades')
        ->orderBy('entidad')
        ->pluck('entidad', 'id_renapo');

    $modalidades = DB::table('cat_modalidades')
        ->orderBy('id')
        ->pluck('modalidad', 'id');

    $periodo = (int) substr((string) $this->captura_actual, -1);

$semestres = [];

for ($i = 1; $i <= 12; $i++) {
    if (
        ($periodo === 1 && $i % 2 === 0) ||
        ($periodo === 2 && $i % 2 !== 0)
    ) {
        $semestres[$i] = $i;
    }
}

    $documentos = DB::table('cat_documentos')
        ->orderBy('id')
        ->pluck('documento', 'id');

    return view('admin.alumnos.agrega', compact(
        'entidadesId',
        'entidadesRenapo',
        'modalidades',
        'semestres',
        'documentos'
    ));
}

    /**
     * Obtiene las escuelas de una entidad para el alta de alumnos.
     */
    public function listaEscuelasAlumno(Request $request)
    {
        $request->validate([
            'entidad' => ['required', 'integer', 'exists:cat_entidades,id'],
        ]);

        $escuelas = Escuela2::where('id_entidad', $request->integer('entidad'))
            ->where('modulo', auth()->user()->modulo)
            ->orderBy('escuela')
            ->pluck('escuela', 'id');

        return response()->json($escuelas);
    }

    /**
     * Obtiene la oferta educativa de una escuela para el alta de alumnos.
     */
    public function listaOfertaAlumno(Request $request)
    {
        $request->validate([
            'escuela' => ['required', 'integer', 'exists:escuelas2,id'],
        ]);

        $oferta = DB::table('escuelas_licenciaturas')
            ->join('cat_licenciaturas', 'cat_licenciaturas.id', '=', 'escuelas_licenciaturas.id_licenciatura')
            ->join('cat_especialidades', 'cat_especialidades.id', '=', 'escuelas_licenciaturas.id_especialidad')
            ->where('escuelas_licenciaturas.id_escuela', $request->integer('escuela'))
            ->select(
                'escuelas_licenciaturas.id_licenciatura',
                'cat_licenciaturas.licenciatura',
                'escuelas_licenciaturas.id_especialidad',
                'cat_especialidades.especialidad',
                'escuelas_licenciaturas.modalidad',
                'escuelas_licenciaturas.turno'
            )
            ->distinct()
            ->get();

        return response()->json($oferta);
    }

    /**
     * Consulta una CURP y obtiene los datos personales del alumno.
     */
    public function consultaCurpAlumno(Request $request)
    {
        $request->merge([
            'curp' => strtoupper(trim($request->input('curp', '')))
        ]);

        $datos = $request->validate([
            'curp' => [
                'required',
                'string',
                'size:18',
                'regex:/^[A-Z]{4}\d{6}[HMX][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9]\d$/'
            ],
        ]);

        $curp = $datos['curp'];

        // Obtener información derivada de la CURP.
        $datosCurp = Alumno::infoCurpStatic(
            $curp,
            $this->captura_actual
        );

        if ($datosCurp['entidad'] === 0) {
            return response()->json([
                'valida' => false,
                'mensaje' => 'No fue posible obtener la información de la CURP.'
            ], 422);
        }

        // Buscar si el alumno ya existe.
        $alumno = DB::table('alumnos')
            ->where('curp', $curp)
            ->first([
                'apellido_paterno',
                'apellido_materno',
                'nombre',
                'curp',
                'edad',
                'genero',
                'fecha_nacimiento',
                'entidad_nacimiento',
                'nacionalidad',
                'id_documento',
            ]);

        if ($alumno) {
            $idRenapo = DB::table('cat_entidades')
                ->where('id', $alumno->entidad_nacimiento)
                ->value('id_renapo');

            return response()->json([
                'valida' => true,
                'existe' => true,
                'mensaje' => 'El alumno ya existe en el sistema.',
                'alumno' => [
                    'apellido_paterno' => $alumno->apellido_paterno,
                    'apellido_materno' => $alumno->apellido_materno,
                    'nombre' => $alumno->nombre,
                    'curp' => $alumno->curp,
                    'edad' => $alumno->edad,
                    'genero' => $alumno->genero,
                    'fecha_nacimiento' => $alumno->fecha_nacimiento,
                    'id_renapo' => $idRenapo,
                    'nacionalidad' => $alumno->nacionalidad,
                    'id_documento' => $alumno->id_documento,
                ]
            ]);
        }

        // CURP nueva: devolver solamente los datos que pueden derivarse.
        return response()->json([
            'valida' => true,
            'existe' => false,
            'mensaje' => 'CURP disponible para registrar.',
            'alumno' => [
                'curp' => $curp,
                'fecha_nacimiento' => $datosCurp['anio'] . '-' .
                    $datosCurp['mes'] . '-' . $datosCurp['dia'],
                'edad' => $datosCurp['edad'],
                'genero' => $datosCurp['genero'],
                'id_renapo' => $datosCurp['renapo'],
            ]
        ]);
    }

    /**
     * Guarda un alumno nuevo (tabla alumnos) y su inscripción (tabla registros).
     * Responde JSON porque el formulario se envía por fetch.
     */
    public function guardarAlumno(Request $request)
    {
        $request->merge([
            'curp'             => strtoupper(trim((string) $request->input('curp', ''))),
            'apellido_paterno' => strtoupper(trim((string) $request->input('apellido_paterno', ''))),
            'apellido_materno' => strtoupper(trim((string) $request->input('apellido_materno', ''))),
            'nombre'           => strtoupper(trim((string) $request->input('nombre', ''))),
            'grupo'            => strtoupper(trim((string) $request->input('grupo', ''))),
            'matricula'        => strtoupper(trim((string) $request->input('matricula', ''))),
        ]);

        $datos = $request->validate([
            'id_entidad'       => ['required', 'integer', 'exists:cat_entidades,id'],
            'id_escuela'       => ['required', 'integer', 'exists:escuelas2,id'],
            'id_licenciatura'  => ['required', 'integer'],
            'id_especialidad'  => ['required', 'integer'],
            'modalidad'        => ['required', 'string', 'size:1'],
            'turno'            => ['required', 'string', 'size:1'],
            'semestre'         => ['required', 'integer', 'between:1,12'],
            'grupo'            => ['nullable', 'string', 'max:5'],
            'matricula'        => ['nullable', 'string', 'max:14'],
            'apellido_paterno' => ['required', 'string', 'max:60'],
            'apellido_materno' => ['nullable', 'string', 'max:60'],
            'nombre'           => ['required', 'string', 'max:60'],
            'curp'             => ['required', 'string', 'size:18',
                                   'regex:/^[A-Z]{4}\d{6}[HMX][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9]\d$/'],
            'genero'           => ['required', 'in:H,M,X'],
            'edad'             => ['required', 'integer', 'between:1,99'],
            'id_renapo'        => ['required', 'string', 'exists:cat_entidades,id_renapo'],
            'nacionalidad'     => ['required', 'in:m,e'],
            'id_documento'     => ['required', 'integer', 'exists:cat_documentos,id'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'integer'  => 'El campo :attribute debe ser un número.',
            'string'   => 'El campo :attribute no es válido.',
            'max'      => 'El campo :attribute no debe exceder :max caracteres.',
            'size'     => 'El campo :attribute debe tener :size caracteres.',
            'between'  => 'El campo :attribute debe estar entre :min y :max.',
            'in'       => 'El valor de :attribute no es válido.',
            'exists'   => 'El valor de :attribute no existe en el catálogo.',
            'regex'    => 'El formato de :attribute no es válido.',
        ], [
            'nombre' => 'nombre', 'curp' => 'CURP', 'edad' => 'edad', 'semestre' => 'semestre',
            'grupo' => 'grupo', 'modalidad' => 'modalidad', 'turno' => 'turno', 'nacionalidad' => 'nacionalidad',
            'id_entidad' => 'entidad', 'id_escuela' => 'escuela', 'id_licenciatura' => 'licenciatura',
            'id_especialidad' => 'especialidad', 'apellido_paterno' => 'primer apellido',
            'apellido_materno' => 'segundo apellido', 'id_renapo' => 'entidad de nacimiento',
            'id_documento' => 'documentación', 'matricula' => 'matrícula', 'genero' => 'género',
        ]);

        $error = fn (string $msg) => response()->json(['ok' => false, 'mensaje' => $msg], 422);

        if (!Alumno::validaNombreStatic($datos)) {
            return $error('El nombre o los apellidos contienen caracteres no permitidos (solo letras, Ñ y espacios).');
        }

        if (!Alumno::validaEntidadStatic($datos)) {
            return $error('La entidad de nacimiento no coincide con la de la CURP.');
        }

        $infoCurp = Alumno::infoCurpStatic($datos['curp'], $this->captura_actual);
        if ($infoCurp['entidad'] === 0) {
            return $error('No fue posible obtener la fecha/entidad de nacimiento de la CURP.');
        }

        // La escuela debe pertenecer al módulo del usuario.
        $escuelaValida = Escuela2::where('id', $datos['id_escuela'])
            ->where('id_entidad', $datos['id_entidad'])
            ->where('modulo', auth()->user()->modulo)
            ->exists();
        if (!$escuelaValida) {
            return $error('La escuela seleccionada no es válida para su usuario.');
        }

        // La combinación licenciatura/especialidad/modalidad/turno debe existir en la oferta de la escuela.
        $ofertaValida = DB::table('escuelas_licenciaturas')
            ->where('id_escuela', $datos['id_escuela'])
            ->where('id_licenciatura', $datos['id_licenciatura'])
            ->where('id_especialidad', $datos['id_especialidad'])
            ->where('modalidad', $datos['modalidad'])
            ->where('turno', $datos['turno'])
            ->exists();
        if (!$ofertaValida) {
            return $error('La licenciatura, especialidad, modalidad o turno no corresponden a la oferta de la escuela.');
        }

        if (DB::table('alumnos')->where('curp', $datos['curp'])->exists()) {
            return $error('Ya existe un alumno registrado con esa CURP.');
        }

        if ($datos['matricula'] !== '' && $datos['matricula'] !== null) {
            $matriculaRepetida = DB::table('registros')
                ->where('id_escuela', $datos['id_escuela'])
                ->where('matricula', $datos['matricula'])
                ->where('captura', $this->captura_actual)
                ->exists();
            if ($matriculaRepetida) {
                return $error('La matrícula ya existe en esta escuela para el periodo actual.');
            }
        }

        $nid = Alumno::generarNidStatic($this->captura_actual);
        $hoy = now()->toDateString();
        $idAlumno = null;

        try {
            // alumnos tiene varias columnas NOT NULL sin valor por defecto: se envían vacías/0.
            $idAlumno = DB::table('alumnos')->insertGetId([
                'numero_identificador' => $nid,
                'identificados'        => 0,
                'apellido_paterno'     => $datos['apellido_paterno'],
                'apellido_materno'     => $datos['apellido_materno'] ?? '',
                'nombre'               => $datos['nombre'],
                'curp'                 => $datos['curp'],
                'edad'                 => $datos['edad'],
                'genero'               => $datos['genero'],
                'correo'               => '',
                'telefono'             => '',
                'fecha_nacimiento'     => $infoCurp['anio'] . '-' . $infoCurp['mes'] . '-' . $infoCurp['dia'],
                'entidad_nacimiento'   => $infoCurp['entidad'],
                'municipio_nacimiento' => 0,
                'nacionalidad'         => $datos['nacionalidad'],
                'id_documento'         => $datos['id_documento'],
                'discapacidad'         => 'n',
                'tipo_discapacidad'    => 0,
                'pais'                 => 0,
                'cp'                   => '',
                'pais_procedencia'     => 0,
                'entidad_procedencia'  => 0,
                'segunda_lengua'       => 0,
                'fecha'                => $hoy,
            ]);

            DB::table('registros')->insert([
                'nid'                 => $nid,
                'id_alumno'           => $idAlumno,
                'id_escuela'          => $datos['id_escuela'],
                'id_licenciatura'     => $datos['id_licenciatura'],
                'id_especialidad'     => $datos['id_especialidad'],
                'modalidad'           => $datos['modalidad'],
                'turno'               => $datos['turno'],
                'semestre'            => $datos['semestre'],
                'grupo'               => $datos['grupo'] ?? '',
                'matricula'           => ($datos['matricula'] ?? '') !== '' ? $datos['matricula'] : null,
                'sepa_ingles'         => '',
                'apoyos'              => '',
                'status'              => 'i',
                'fecha_actualizacion' => $hoy,
                'captura'             => (int) $this->captura_actual,
            ]);
        } catch (\Throwable $e) {
            // Las tablas son MyISAM (sin transacciones): si falló registros, se elimina el alumno insertado.
            if ($idAlumno) {
                DB::table('alumnos')->where('id', $idAlumno)->delete();
            }
            report($e);

            return response()->json([
                'ok'      => false,
                'mensaje' => 'No se pudo guardar el alumno. Inténtelo de nuevo.',
                'detalle' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        session()->flash('success', "Alumno registrado correctamente. NID: {$nid}");

        return response()->json([
            'ok'       => true,
            'mensaje'  => "Alumno registrado correctamente. NID: {$nid}",
            'nid'      => $nid,
            'redirect' => route('admin.alumnos.alta'),
        ]);
    }

    // ── Paso 2: Alta de personal ──────────────────────────────────────

// Muestra el formulario de captura
    public function agregaPersonal()
    {
        $entidades = Entidad::orderBy('id')->pluck('entidad', 'id');
        $funciones = Funcion::orderBy('id')->pluck('funcion', 'id');
        $niveles   = NivelEstudio::orderBy('id')->pluck('nivel_estudio', 'id');
        $areas     = AreaConocimiento::orderBy('id_campo')->orderBy('id')->get();
        $discapacidades = [1 => 'Auditiva y de lenguaje', 2 => 'Intelectual', 3 => 'Motriz', 4 => 'Visual'];

        return view('admin.personal.agrega', compact(
            'entidades', 'funciones', 'niveles', 'areas', 'discapacidades'
        ));
    }

    // Devuelve las escuelas de la entidad seleccionada (AJAX)
    public function listaEscuelas(Request $request)
    {
        $escuelas = Escuela2::where('id_entidad', $request->integer('entidad'))
            ->where('modulo', auth()->user()->modulo)
            ->where('status', 'a')
            ->where('unica', '<', 2)
            ->orderBy('escuela')
            ->pluck('escuela', 'id');

        return response()->json($escuelas);
    }

    // Guarda el personal (y el docente si la función lo requiere)
   // Guarda el personal (y el docente si la función lo requiere)
   // Guarda el personal (y el docente si la función lo requiere)
    public function guardaAgregacion(Request $request)
    {
        // Normalizar CURP ANTES de validar: mayúsculas y sin espacios
        $request->merge(['curp' => strtoupper(trim($request->input('curp', '')))]);
        $data = $request->all();

        // Validación de formato de CURP (idéntica a la del sistema viejo)
        $request->validate([
            'curp' => ['required', 'regex:/^[A-Z]{4}\d{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9][0-9]$/'],
        ]);

        // Verificación de duplicado: misma CURP en la misma escuela
        $existe = Personal::where('curp', $data['curp'])
            ->where('id_escuela', $data['id_escuela'])
            ->exists();

        if ($existe) {
            return back()
                ->withErrors(['curp' => "Error: ya existe un registro con la curp {$data['curp']}"])
                ->withInput();
        }

        // Guardar en la tabla personal2 con status 'a' (se activa de inmediato)
        $personal = Personal::create([
            'curp'                => $data['curp'],
            'edad'                => $data['edad'] ?? 0,
            'genero'              => $data['genero'] ?? 'm',
            'apellido_paterno'    => strtoupper($data['apellido_paterno'] ?? ''),
            'apellido_materno'    => strtoupper($data['apellido_materno'] ?? ''),
            'nombre'              => strtoupper($data['nombre'] ?? ''),
            'id_escuela'          => $data['id_escuela'],
            'id_funcion'          => $data['id_funcion'],
            'correo_electronico'  => strtolower(trim($data['correo_electronico'] ?? '')),
            'status'              => 'a',
            'fecha_actualizacion' => now(),
            'discapacidad'        => $data['discapacidad'] ?? 'n',
            'tipo_discapacidad'   => ($data['discapacidad'] ?? 'n') === 's' ? ($data['tipo_discapacidad'] ?? 0) : 0,
        ]);

        // Docentes y directivos (función 1-6): guardar también en la tabla docentes
        if ((int) $data['id_funcion'] <= 6) {
            // Fecha de titulación: sin título o sin fecha → '0000-00-00'
            $fechaTit = trim($data['fecha_titulacion'] ?? '');
            if (($data['titulo'] ?? 'n') === 'n' || $fechaTit === '') {
                $fechaTit = '0000-00-00';
            }

            try {
                Docente::create([
                    'id_personal'            => $personal->id,
                    'id_nivel_estudio'       => $data['id_nivel_estudio'] ?? 0,
                    'titulo'                 => $data['titulo'] ?? 'n',
                    'fecha_titulacion'       => $fechaTit,
                    'tema_titulacion'        => $data['tema_titulacion'] ?? '',
                    'escuela_egreso'         => $data['escuela_egreso'] ?? '',
                    'lengua_indigena'        => ($data['lengua'] ?? 'n') === 's' ? ($data['lengua_indigena'] ?? '') : '',
                    'antiguedad_escuela'     => $data['antiguedad_escuela'] ?? 0,
                    'antiguedad_docente'     => $data['antiguedad_docente'] ?? 0,
                    'proyecto_investigacion' => ($data['investigacion'] ?? 'n') === 's' ? ($data['proyecto_investigacion'] ?? '') : '',
                    'estancia_investigacion' => $data['estancia_investigacion'] ?? 'n',
                    'id_area_conocimiento'   => $data['id_area_conocimiento'] ?? 0,
                    // Columna NOT NULL sin valor por defecto; el formulario viejo no la capturaba → 0
                    'id_nivel_sni'           => 0,
                ]);
            } catch (\Throwable $e) {
                // Las tablas son MyISAM (no hay transacciones): si falla el docente,
                // borramos el registro de personal2 para no dejarlo huérfano.
                $personal->delete();
                throw $e;
            }
        }

        return redirect()->route('admin.personal.alta')
            ->with('success', "Personal registrado correctamente (CURP: {$personal->curp}).");
    }

    // ── Paso 3: Edición/Activar de personal ──────────────────────────────

    /** Muestra la pantalla de búsqueda: entidad → escuela y filtros */
    public function busquedaPersonal(Request $request)
    {
        $entidades = Entidad::orderBy('id')->pluck('entidad', 'id');
        $funciones = Funcion::orderBy('id')->pluck('funcion', 'id');

        // Sirve para volver a la misma escuela después de editar
        $entidad_id = $request->integer('entidad');
        $escuela_id = $request->integer('escuela');

        return view('admin.personal.busqueda', compact(
            'entidades', 'funciones', 'entidad_id', 'escuela_id'
        ));
    }

    /** Devuelve (AJAX) el fragmento con la tabla de personal de una escuela */
   /** Devuelve (AJAX) el fragmento con la tabla de personal de una escuela */
    public function listaPersonal(Request $request)
    {
        $escuelaId = $request->input('escuela');
        if (!$escuelaId) {
            return response('<p>Seleccione primero una escuela.</p>');
        }

        // El dropdown manda el id entero de la tabla escuelas (vista escuelas2).
        // Pero los registros de personal2 pudieron guardarse con OTRA referencia
        // a la escuela (clave_escuela, escuela_key o clave_institucion),
        // así que buscamos por todos los identificadores de esa escuela a la vez.
        $escuela = Escuela2::find($escuelaId);
        $ids = [$escuelaId];
        if ($escuela) {
            foreach (['clave_escuela', 'escuela_key', 'clave_institucion'] as $campo) {
                if (!empty($escuela->{$campo})) {
                    $ids[] = $escuela->{$campo};
                }
            }
        }
        $ids = array_values(array_unique($ids));

        $query = Personal::with('funcion')
            ->whereIn('id_escuela', $ids)
            ->whereIn('status', ['a', 'x']); // los jubilados no se muestran

        $busca = $request->input('busca', []);
        if (!empty($busca['texto'])) {
            $texto = $busca['texto'];
            $query->where(function ($q) use ($texto) {
                $q->where('nombre', 'like', "%{$texto}%")
                  ->orWhere('apellido_paterno', 'like', "%{$texto}%")
                  ->orWhere('apellido_materno', 'like', "%{$texto}%")
                  ->orWhere('curp', 'like', '%' . strtoupper($texto) . '%');
            });
        }
        if (!empty($busca['funcion'])) {
            $query->where('id_funcion', (int) $busca['funcion']);
        }

        $personal = $query
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        // Si no hay resultados, mostramos en pantalla los ids que se probaron
        // para que el diagnóstico sea inmediato (sin abrir ninguna herramienta).
        if ($personal->isEmpty()) {
            $escuelaNom = $escuela->escuela ?? '';
            return response(
                "<p><b>No se encontró personal</b> en \"" . e($escuelaNom) . "\".<br>" .
                "Identificadores probados: " . implode(', ', $ids) . ".</p>"
            );
        }

        return view('admin.personal.lista_parcial', compact('personal'));
    }

    /** Activa/desactiva un registro (status a ↔ x) vía AJAX */
    public function cambiaStatus(Request $request)
    {
        $personal = Personal::findOrFail($request->integer('id'));
        $personal->status = $personal->status === 'a' ? 'x' : 'a';
        $personal->fecha_actualizacion = now();
        $personal->save();

        return response()->json(['status' => $personal->status]);
    }

    /** Muestra el formulario de edición prefilled */
    public function editaPersonal(int $id)
    {
        $p = Personal::with('docente')->findOrFail($id);
        $d       = $p->docenteoby;
        $escuela = Escuela2::find($p->id_escuela);
        $entidad_id = $escuela->id_entidad ?? 0;

        $entidades = Entidad::orderBy('id')->pluck('entidad', 'id');
        $funciones = Funcion::orderBy('id')->pluck('funcion', 'id');
        $niveles   = NivelEstudio::orderBy('id')->pluck('nivel_estudio', 'id');
        $areas     = AreaConocimiento::orderBy('id_campo')->orderBy('id')->get();
        $discapacidades = [1 => 'Auditiva y de lenguaje', 2 => 'Intelectual', 3 => 'Motriz', 4 => 'Visual'];

        // La antigüedad se guarda en una columna YEAR: elegir "1 año" guarda 2001.
        // Convertimos a los "años" que el usuario eligió (2000-2049 → restar 2000).
        $antEscuela = $d->antiguedad_escuela ?? 0;
        $antDocente = $d->antiguedad_docente ?? 0;
        if ($antEscuela >= 2000 && $antEscuela <= 2049) $antEscuela -= 2000;
        if ($antDocente >= 2000 && $antDocente <= 2049) $antDocente -= 2000;

        return view('admin.personal.edita', compact(
            'p', 'd', 'escuela', 'entidad_id', 'entidades', 'funciones',
            'niveles', 'areas', 'discapacidades', 'antEscuela', 'antDocente'
        ));
    }

    /** Guarda los cambios de la edición */
    public function guardaEdicion(Request $request)
    {
        $request->merge(['curp' => strtoupper(trim($request->input('curp', '')))]);
        $data = $request->all();

        $request->validate([
            'curp' => ['required', 'regex:/^[A-Z]{4}\d{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9][0-9]$/'],
        ]);

        $personal = Personal::findOrFail((int) ($data['id'] ?? 0));

        // Duplicado: misma CURP en la misma escuela, excluyendo este registro
        $existe = Personal::where('curp', $data['curp'])
            ->where('id_escuela', $personal->id_escuela)
            ->where('id', '!=', $personal->id)
            ->exists();

        if ($existe) {
            return back()
                ->withErrors(['curp' => "Error: ya existe un registro con la curp {$data['curp']} en esta escuela"])
                ->withInput();
        }

        $personal->update([
            'curp'                => $data['curp'],
            'edad'                => $data['edad'] ?? 0,
            'genero'              => $data['genero'] ?? 'm',
            'apellido_paterno'    => strtoupper($data['apellido_paterno'] ?? ''),
            'apellido_materno'    => strtoupper($data['apellido_materno'] ?? ''),
            'nombre'              => strtoupper($data['nombre'] ?? ''),
            'id_funcion'          => $data['id_funcion'],
            'correo_electronico'  => strtolower(trim($data['correo_electronico'] ?? '')),
            'status'              => 'a', // nivel 1: al guardar, el registro queda activo
            'fecha_actualizacion' => now(),
            'discapacidad'        => $data['discapacidad'] ?? 'n',
            'tipo_discapacidad'   => ($data['discapacidad'] ?? 'n') === 's' ? ($data['tipo_discapacidad'] ?? 0) : 0,
        ]);

        // Docentes y directivos (función 1-6): actualizar o crear en la tabla docentes
        if ((int) $personal->id_funcion <= 6) {
            $fechaTit = trim($data['fecha_titulacion'] ?? '');
            if (($data['titulo'] ?? 'n') === 'n' || $fechaTit === '') {
                $fechaTit = '0000-00-00';
            }

            $docente = Docente::firstOrNew(['id_personal' => $personal->id]);
            $docente->fill([
                'id_nivel_estudio'       => $data['id_nivel_estudio'] ?? 0,
                'titulo'                 => $data['titulo'] ?? 'n',
                'fecha_titulacion'       => $fechaTit,
                'tema_titulacion'        => $data['tema_titulacion'] ?? '',
                'escuela_egreso'         => $data['escuela_egreso'] ?? '',
                'lengua_indigena'        => ($data['lengua'] ?? 'n') === 's' ? ($data['lengua_indigena'] ?? '') : '',
                'antiguedad_escuela'     => $data['antiguedad_escuela'] ?? 0,
                'antiguedad_docente'     => $data['antiguedad_docente'] ?? 0,
                'proyecto_investigacion' => ($data['investigacion'] ?? 'n') === 's' ? ($data['proyecto_investigacion'] ?? '') : '',
                'estancia_investigacion' => $data['estancia_investigacion'] ?? 'n',
                'id_area_conocimiento'   => $data['id_area_conocimiento'] ?? 0,
                // Si el docente ya existía, conservamos su valor; si no, 0 como en Alta
                'id_nivel_sni'           => $docente->id_nivel_sni ?? 0,
            ]);
            $docente->save();
        }

        return redirect()->route('admin.personal.busqueda', [
            'entidad' => $request->input('id_entidad', 0),
            'escuela' => $personal->id_escuela,
        ])->with('success', "Personal actualizado correctamente (CURP: {$personal->curp}).");
    }

    // ── Edición de alumnos ────────────────────────────────────────────

    /**
     * Muestra el formulario de búsqueda de alumnos para edición.
     */
    public function busquedaAlumno(Request $request)
    {
        $entidades = DB::table('cat_entidades')
            ->where('id', '!=', 33)
            ->orderBy('entidad')
            ->pluck('entidad', 'id');

        $modalidades = DB::table('cat_modalidades')->orderBy('id')->pluck('modalidad', 'id');
        $turnos = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];
        $status = ['i' => 'Inscrito', 'x' => 'No Inscrito', 't' => 'Baja Temporal', 'd' => 'Baja Definitiva'];

        // Periodos disponibles para el filtro "Captura" (actual y tres anteriores).
        $capturas = [];
        $anio = (int) substr($this->captura_actual, 0, 4);
        $periodo = (int) substr($this->captura_actual, -1);
        for ($i = 0; $i < 4; $i++) {
            $capturas[$anio . $periodo] = $anio . '-' . $periodo;
            if ($periodo === 1) { $periodo = 2; $anio--; } else { $periodo = 1; }
        }

        $entidad_id = $request->integer('entidad');
        $escuela_id = $request->integer('escuela');

        return view('admin.alumnos.busqueda', compact(
            'entidades', 'modalidades', 'turnos', 'status', 'capturas',
            'entidad_id', 'escuela_id'
        ));
    }

    /**
     * Devuelve (AJAX) el fragmento con la lista de alumnos que coinciden con la búsqueda.
     */
    public function listaAlumnosEdicion(Request $request)
    {
        $escuelaId = $request->integer('id_escuela');
        $curp = strtoupper(trim((string) $request->input('curp', '')));
        $nid  = trim((string) $request->input('nid', ''));

        // Sin escuela solo se permite buscar por CURP o NID (mínimo 4 caracteres para no traer listas enormes).
        $porIdentificador = !$escuelaId;
        if ($porIdentificador && mb_strlen($curp) < 4 && mb_strlen($nid) < 4) {
            return response('<p style="color:#721c24;background:#f8d7da;padding:12px 16px;border:1px solid #f5c6cb;border-radius:4px;">Seleccione una escuela, o escriba el CURP o el NID del alumno (al menos 4 caracteres).</p>');
        }

        // Solo se muestran alumnos de escuelas del módulo del usuario.
        $query = DB::table('registros')
            ->join('alumnos', 'alumnos.numero_identificador', '=', 'registros.nid')
            ->join('escuelas2', 'escuelas2.id', '=', 'registros.id_escuela')
            ->where('escuelas2.modulo', auth()->user()->modulo)
            ->where('registros.status', '!=', 's');

        if ($escuelaId) {
            $query->where('registros.id_escuela', $escuelaId);
        }

        // Al buscar por CURP/NID sin escuela se muestran todos los periodos del alumno.
        if (!$porIdentificador && $request->filled('captura')) {
            $query->where('registros.captura', (int) $request->input('captura'));
        }
        foreach ([
            'id_licenciatura' => 'registros.id_licenciatura',
            'id_especialidad' => 'registros.id_especialidad',
            'semestre'        => 'registros.semestre',
        ] as $campo => $columna) {
            if ($request->filled($campo)) {
                $query->where($columna, (int) $request->input($campo));
            }
        }
        foreach ([
            'modalidad' => 'registros.modalidad',
            'turno'     => 'registros.turno',
            'status'    => 'registros.status',
        ] as $campo => $columna) {
            if ($request->filled($campo)) {
                $query->where($columna, $request->input($campo));
            }
        }
        if ($request->filled('grupo')) {
            $query->where('registros.grupo', strtoupper(trim($request->input('grupo'))));
        }
        if ($curp !== '') {
            $query->where('alumnos.curp', 'like', '%' . $curp . '%');
        }
        if ($nid !== '') {
            $query->where('alumnos.numero_identificador', 'like', '%' . $nid . '%');
        }

        if ($porIdentificador) {
            $query->limit(200);
        }

        $alumnos = $query
            ->orderBy('alumnos.apellido_paterno')
            ->orderBy('alumnos.apellido_materno')
            ->orderBy('alumnos.nombre')
            ->get([
                'alumnos.numero_identificador as nid',
                'alumnos.apellido_paterno',
                'alumnos.apellido_materno',
                'alumnos.nombre',
                'alumnos.curp',
                'escuelas2.escuela',
                'registros.captura',
                'registros.id as id_registro',
                'registros.id_licenciatura',
                'registros.id_especialidad',
                'registros.modalidad',
                'registros.turno',
                'registros.semestre',
                'registros.grupo',
                'registros.matricula',
                'registros.status',
            ]);

        // Nombres de licenciatura y especialidad para mostrar en la tabla.
        $licenciaturas = DB::table('cat_licenciaturas')->pluck('licenciatura', 'id');
        $especialidades = DB::table('cat_especialidades')->pluck('especialidad', 'id');

        return view('admin.alumnos.lista_parcial', compact(
            'alumnos', 'licenciaturas', 'especialidades', 'escuelaId', 'porIdentificador'
        ));
    }

    /**
     * Muestra el formulario de edición de un alumno (datos personales y de inscripción).
     */
    public function editaAlumnoEdicion(Request $request, string $nid)
    {
        $alumno = DB::table('alumnos')->where('numero_identificador', $nid)->first();
        if (!$alumno) {
            return redirect()->route('admin.alumnos.busqueda')
                ->with('error', "No se encontró el alumno con NID {$nid}.");
        }

        $registroQuery = DB::table('registros')->where('nid', $nid);
        if ($request->filled('registro')) {
            $registroQuery->where('id', $request->integer('registro'));
        } else {
            $registroQuery->orderByDesc('captura')->orderByDesc('id');
        }
        $registro = $registroQuery->first();

        if (!$registro) {
            return redirect()->route('admin.alumnos.busqueda')
                ->with('error', "El alumno {$nid} no tiene inscripciones registradas.");
        }

        $escuela = Escuela2::find($registro->id_escuela);
        $entidad_id = $escuela->id_entidad ?? 0;

        $entidades = DB::table('cat_entidades')->where('id', '!=', 33)->orderBy('entidad')->pluck('entidad', 'id');
        $entidadesRenapo = DB::table('cat_entidades')->orderBy('entidad')->pluck('entidad', 'id_renapo');
        $documentos = DB::table('cat_documentos')->orderBy('id')->pluck('documento', 'id');
        $modalidades = DB::table('cat_modalidades')->orderBy('id')->pluck('modalidad', 'id');
        $turnos = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];
        $status = ['i' => 'Inscrito', 'x' => 'No Inscrito', 't' => 'Baja Temporal', 'd' => 'Baja Definitiva'];

        // Oferta de la escuela para los selectores de licenciatura/especialidad/modalidad/turno.
        $oferta = DB::table('escuelas_licenciaturas')
            ->join('cat_licenciaturas', 'cat_licenciaturas.id', '=', 'escuelas_licenciaturas.id_licenciatura')
            ->join('cat_especialidades', 'cat_especialidades.id', '=', 'escuelas_licenciaturas.id_especialidad')
            ->where('escuelas_licenciaturas.id_escuela', $registro->id_escuela)
            ->select(
                'escuelas_licenciaturas.id_licenciatura',
                'cat_licenciaturas.licenciatura',
                'escuelas_licenciaturas.id_especialidad',
                'cat_especialidades.especialidad',
                'escuelas_licenciaturas.modalidad',
                'escuelas_licenciaturas.turno'
            )
            ->distinct()
            ->get();

        $licenciaturas = $oferta->pluck('licenciatura', 'id_licenciatura')->unique();
        $especialidades = $oferta->pluck('especialidad', 'id_especialidad')->unique();

        $semestres = [];
        $paridad = ((int) $this->captura_actual) % 2;
        for ($i = 1; $i <= 13; $i++) {
            if (($i % 2) === $paridad) {
                $semestres[$i] = $i;
            }
        }
        $semestres[$registro->semestre] = $registro->semestre;
        ksort($semestres);

        return view('admin.alumnos.edita', compact(
            'alumno', 'registro', 'escuela', 'entidad_id', 'entidades', 'entidadesRenapo',
            'documentos', 'modalidades', 'turnos', 'status', 'licenciaturas',
            'especialidades', 'semestres'
        ));
    }

    /**
     * Guarda los cambios de la edición de un alumno.
     */
    public function actualizaAlumno(Request $request)
    {
        $request->merge([
            'curp'             => strtoupper(trim((string) $request->input('curp', ''))),
            'apellido_paterno' => strtoupper(trim((string) $request->input('apellido_paterno', ''))),
            'apellido_materno' => strtoupper(trim((string) $request->input('apellido_materno', ''))),
            'nombre'           => strtoupper(trim((string) $request->input('nombre', ''))),
            'grupo'            => strtoupper(trim((string) $request->input('grupo', ''))),
            'matricula'        => strtoupper(trim((string) $request->input('matricula', ''))),
        ]);

        $datos = $request->validate([
            'nid'              => ['required', 'string', 'max:10'],
            'id_registro'      => ['required', 'integer'],
            'apellido_paterno' => ['required', 'string', 'max:60'],
            'apellido_materno' => ['nullable', 'string', 'max:60'],
            'nombre'           => ['required', 'string', 'max:60'],
            'curp'             => ['required', 'string', 'size:18',
                                   'regex:/^[A-Z]{4}\d{6}[HMX][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9]\d$/'],
            'genero'           => ['required', 'in:H,M,X'],
            'edad'             => ['required', 'integer', 'between:1,99'],
            'id_renapo'        => ['required', 'string', 'exists:cat_entidades,id_renapo'],
            'nacionalidad'     => ['required', 'in:m,e'],
            'id_documento'     => ['required', 'integer', 'exists:cat_documentos,id'],
            'id_licenciatura'  => ['required', 'integer'],
            'id_especialidad'  => ['required', 'integer'],
            'modalidad'        => ['required', 'string', 'size:1'],
            'turno'            => ['required', 'string', 'size:1'],
            'semestre'         => ['required', 'integer', 'between:1,13'],
            'grupo'            => ['nullable', 'string', 'max:5'],
            'matricula'        => ['nullable', 'string', 'max:14'],
            'status'           => ['required', 'in:i,x,t,d'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'integer'  => 'El campo :attribute debe ser un número.',
            'string'   => 'El campo :attribute no es válido.',
            'max'      => 'El campo :attribute no debe exceder :max caracteres.',
            'size'     => 'El campo :attribute debe tener :size caracteres.',
            'between'  => 'El campo :attribute debe estar entre :min y :max.',
            'in'       => 'El valor de :attribute no es válido.',
            'exists'   => 'El valor de :attribute no existe en el catálogo.',
            'regex'    => 'El formato de :attribute no es válido.',
        ], [
            'nid' => 'NID', 'curp' => 'CURP', 'nombre' => 'nombre', 'edad' => 'edad',
            'semestre' => 'semestre', 'grupo' => 'grupo', 'modalidad' => 'modalidad',
            'turno' => 'turno', 'status' => 'estatus', 'nacionalidad' => 'nacionalidad',
            'apellido_paterno' => 'primer apellido', 'apellido_materno' => 'segundo apellido',
            'id_renapo' => 'entidad de nacimiento', 'id_documento' => 'documentación',
            'id_licenciatura' => 'licenciatura', 'id_especialidad' => 'especialidad',
            'matricula' => 'matrícula', 'genero' => 'género',
        ]);

        $alumno = DB::table('alumnos')->where('numero_identificador', $datos['nid'])->first();
        if (!$alumno) {
            return back()->withErrors(['nid' => "No se encontró el alumno con NID {$datos['nid']}."])->withInput();
        }

        $registro = DB::table('registros')->where('id', $datos['id_registro'])->first();
        if (!$registro) {
            return back()->withErrors(['id_registro' => 'No se encontró la inscripción del alumno.'])->withInput();
        }

        if (!Alumno::validaNombreStatic($datos)) {
            return back()->withErrors([
                'nombre' => 'El nombre o los apellidos contienen caracteres no permitidos (solo letras, Ñ y espacios).',
            ])->withInput();
        }

        if (!Alumno::validaEntidadStatic($datos)) {
            return back()->withErrors([
                'id_renapo' => 'La entidad de nacimiento no coincide con la de la CURP.',
            ])->withInput();
        }

        $infoCurp = Alumno::infoCurpStatic($datos['curp'], $this->captura_actual);
        if ($infoCurp['entidad'] === 0) {
            return back()->withErrors([
                'curp' => 'No fue posible obtener la fecha/entidad de nacimiento de la CURP.',
            ])->withInput();
        }

        $curpOcupada = DB::table('alumnos')
            ->where('curp', $datos['curp'])
            ->where('id', '!=', $alumno->id)
            ->exists();
        if ($curpOcupada) {
            return back()->withErrors([
                'curp' => "Ya existe otro alumno registrado con la CURP {$datos['curp']}.",
            ])->withInput();
        }

        $matricula = $datos['matricula'] ?? '';
        if ($matricula !== '') {
            $matriculaOcupada = DB::table('registros')
                ->where('id_escuela', $registro->id_escuela)
                ->where('matricula', $matricula)
                ->where('captura', $registro->captura)
                ->where('id', '!=', $registro->id)
                ->exists();
            if ($matriculaOcupada) {
                return back()->withErrors([
                    'matricula' => "La matrícula {$matricula} ya está asignada a otro alumno de esta escuela.",
                ])->withInput();
            }
        }

        try {
            DB::table('alumnos')->where('id', $alumno->id)->update([
                'apellido_paterno'   => $datos['apellido_paterno'],
                'apellido_materno'   => $datos['apellido_materno'] ?? '',
                'nombre'             => $datos['nombre'],
                'curp'               => $datos['curp'],
                'edad'               => $datos['edad'],
                'genero'             => $datos['genero'],
                'fecha_nacimiento'   => $infoCurp['anio'] . '-' . $infoCurp['mes'] . '-' . $infoCurp['dia'],
                'entidad_nacimiento' => $infoCurp['entidad'],
                'nacionalidad'       => $datos['nacionalidad'],
                'id_documento'       => $datos['id_documento'],
            ]);

            DB::table('registros')->where('id', $registro->id)->update([
                'id_licenciatura'     => $datos['id_licenciatura'],
                'id_especialidad'     => $datos['id_especialidad'],
                'modalidad'           => $datos['modalidad'],
                'turno'               => $datos['turno'],
                'semestre'            => $datos['semestre'],
                'grupo'               => $datos['grupo'] ?? '',
                'matricula'           => $matricula !== '' ? $matricula : null,
                'status'              => $datos['status'],
                'fecha_actualizacion' => now()->toDateString(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors([
                'general' => 'No se pudo guardar la edición. Inténtelo de nuevo.',
            ])->withInput();
        }

        return redirect()->route('admin.alumnos.busqueda', [
            'entidad' => $request->input('id_entidad', 0),
            'escuela' => $registro->id_escuela,
        ])->with('success', "Alumno actualizado correctamente (NID: {$datos['nid']}).");
    }

}
