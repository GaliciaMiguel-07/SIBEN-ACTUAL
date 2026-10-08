<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
<<<<<<< HEAD
=======
use Illuminate\Http\Request;
use App\Models\Entidad;
use App\Models\Funcion;
use App\Models\Escuela2;
use App\Models\Personal;
use App\Models\Docente;
use App\Models\NivelEstudio;
use App\Models\AreaConocimiento;
>>>>>>> e5c053639d35f69c9a9faa6036a69b6c398c4846

class AdminController extends Controller
{
    /**
     * Panel de administración (nivel 1).
     * Equivale a AdminController::index() del sistema viejo, que armaba
     * el arreglo $menu con las opciones de Alumnos y Personal.
     */
<<<<<<< HEAD
    public function index(): View
    {
        $menu = [
            'Alumnos' => [
                ['Alta',    'admin.alumnos.alta'],
                ['Edición', 'admin.alumnos.buscar'],
            ],
            'Personal' => [
                ['Alta',            'admin.personal.alta'],
                ['Edición/Activar', 'admin.personal.buscar'],
                ['Jubilar',         'admin.personal.jubilar'],
                ['Jubilados',       'admin.personal.jubilados'],
=======
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
>>>>>>> e5c053639d35f69c9a9faa6036a69b6c398c4846
            ],
        ];

        return view('admin.index', compact('menu'));
    }
<<<<<<< HEAD
=======

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
>>>>>>> e5c053639d35f69c9a9faa6036a69b6c398c4846
}