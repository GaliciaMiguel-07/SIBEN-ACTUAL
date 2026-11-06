<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Posgrado;
use App\Models\AlumnosPosgrado;
use App\Models\DocentesPosgrado;
use App\Models\EstudiosRealizados;
use App\Models\Escuela;
use App\Models\Entidad;
use App\Models\Estudios;
use App\Models\Persona;
use App\Models\LenguaHabilidades;
use App\Models\PreguntasPersona;
use App\Models\FormaContratacion;
use App\Models\ExperienciaDocente;
use App\Models\NivelesEducacion;
use App\Models\PosgradosDocentes;
use App\Models\ContratacionesPosgrado;
use App\Models\GeneracionesPosgrado;
use App\Models\ExtranjeraIndigena;
use App\Models\ModosTitulacion;
use App\Models\BajasPosgrado;

class AlumnosDocentesPosgradoController extends sibenController
{
    /**
     * index
     */
    public function index()
    {
        session()->forget('criteriosAlta');
        
        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;
        $nivel = $ctx['nivel'] ?? null;

        $mensaje = null;
        if (!$captura) {
            $mensaje = 'La captura está finalizada';
        }

        $opcionesMenu = [
            ['AlumnosDocentesPosgrado', 'Alta Alumnos Posgrado', 'opcionesAlta', 0],
            ['AlumnosDocentesPosgrado', 'Reinscripción Alumnos', 'opcionesAlta', 'r'],
            ['AlumnosDocentesPosgrado', 'Baja Alumnos', 'opcionesBaja', 'b'],
        ];

        $opcionesMenu2 = [
            ['AlumnosDocentesPosgrado', 'Editar Alumnos Posgrado', 'opcionesAlta', 'r'],
        ];

        return view('alumnos_docentes_posgrado.index', compact('opcionesMenu', 'opcionesMenu2', 'mensaje'));
    }

    /**
     * home
     */
    public function home()
    {
        session()->forget('criteriosAlta');
        
        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;
        $nivel = $ctx['nivel'] ?? null;

        $mensaje = null;
        if (!$captura) {
            $mensaje = 'La captura está finalizada';
        }

        $opcionesMenu = [
            ['AlumnosDocentesPosgrado', 'Agregar Docente', 'agregarDocentes', 0],
            ['AlumnosDocentesPosgrado', 'Activar Docente', 'ListasDocentes', 0],
        ];

        $opcionesMenu2 = [
            ['AlumnosDocentesPosgrado', 'Editar Docente', 'ListasDocentes', 0],
        ];

        return view('alumnos_docentes_posgrado.home', compact('opcionesMenu', 'opcionesMenu2', 'mensaje'));
    }

    /**
     * opcionesAlta
     */
    public function opcionesAlta(Request $request, ?string $r = null)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $busqueda = $r ? true : false;

        $modalidadesList = Posgrado::where('id_escuela', $id_escuela)
            ->select('modalidad')
            ->groupBy('modalidad')
            ->get();

        $posgrado_modalidad = [];
        foreach ($modalidadesList as $mod) {
            switch ($mod->modalidad) {
                case 'm': $posgrado_modalidad['m'] = 'Mixta'; break;
                case 'e': $posgrado_modalidad['e'] = 'Escolarizada'; break;
                case 'n': $posgrado_modalidad['n'] = 'No escolarizada'; break;
            }
        }

        $error = null;
        if (empty($posgrado_modalidad)) {
            $error = 'Primero se deben de agregar los posgrados que se imparten en la escuela.';
        }

        $anterior = route('alumnos_docentes_posgrado.index');

        $inputData = $request->input('AlumnosPosgrado');
        if ($request->isMethod('post') && !empty($inputData)) {
            $inputData['rol'] = 'a';
            session()->put('criteriosAlta', ['AlumnosPosgrado' => $inputData]);

            if (empty($inputData['busqueda'])) {
                return redirect()->route('alumnos_docentes_posgrado.agregarAlumno');
            } else {
                return redirect()->route('alumnos_docentes_posgrado.listas');
            }
        } else {
            session()->forget('criteriosAlta');
        }

        return view('alumnos_docentes_posgrado.opciones_alta', compact(
            'r', 'busqueda', 'posgrado_modalidad', 'error', 'anterior'
        ));
    }

    /**
     * opcionesBaja
     */
    public function opcionesBaja(Request $request, ?string $b = null)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $busqueda = $b ? true : false;

        $modalidadesList = Posgrado::where('id_escuela', $id_escuela)
            ->select('modalidad')
            ->groupBy('modalidad')
            ->get();

        $posgrado_modalidad = [];
        foreach ($modalidadesList as $mod) {
            switch ($mod->modalidad) {
                case 'm': $posgrado_modalidad['m'] = 'Mixta'; break;
                case 'e': $posgrado_modalidad['e'] = 'Escolarizada'; break;
                case 'n': $posgrado_modalidad['n'] = 'No escolarizada'; break;
            }
        }

        $error = null;
        if (empty($posgrado_modalidad)) {
            $error = 'Primero se deben de agregar los posgrados que se imparten en la escuela.';
        }

        $anterior = route('alumnos_docentes_posgrado.index');

        $inputData = $request->input('AlumnosPosgrado');
        if ($request->isMethod('post') && !empty($inputData)) {
            $inputData['rol'] = 'a';
            session()->put('criteriosAlta', ['AlumnosPosgrado' => $inputData]);

            if (empty($inputData['busqueda'])) {
                return redirect()->route('alumnos_docentes_posgrado.agregarAlumno');
            } else {
                return redirect()->route('alumnos_docentes_posgrado.listas', ['opcion' => 'b']);
            }
        } else {
            session()->forget('criteriosAlta');
        }

        return view('alumnos_docentes_posgrado.opciones_baja', compact(
            'b', 'busqueda', 'posgrado_modalidad', 'error', 'anterior'
        ));
    }

    /**
     * agregarAlumno
     */
    public function agregarAlumno(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $criteriosAlta = session()->get('criteriosAlta');

        if (empty($criteriosAlta)) {
            return redirect()->route('alumnos_docentes_posgrado.opcionesAlta');
        }

        $rol = $criteriosAlta['AlumnosPosgrado']['rol'] ?? 'a';
        $errorPosgrado = null;

        $inputAlumnos = $request->input('AlumnosPosgrado');
        $inputPersona = $request->input('Persona');

        if ($request->isMethod('post') && !empty($inputAlumnos) && !empty($inputPersona)) {
            $curpStr = $inputPersona['curp'];
            $id_posgrado_req = $inputAlumnos['id_posgrado'];

            $posgradoRepetido = AlumnosPosgrado::where('id_escuela', $id_escuela)
                ->where('id_posgrado', $id_posgrado_req)
                ->whereHas('persona', fn($q) => $q->where('curp', $curpStr))
                ->exists();

            $personaExiste = Persona::where('curp', $curpStr)->first();

            $docenteExiste = DocentesPosgrado::where('id_escuela', $id_escuela)
                ->whereHas('persona', fn($q) => $q->where('curp', $curpStr))
                ->first();

            $esDocenteEnPosgrado = false;
            if ($docenteExiste) {
                $esDocenteEnPosgrado = PosgradosDocentes::where('id_docente', $docenteExiste->id)
                    ->where('id_posgrado', $id_posgrado_req)
                    ->exists();
            }

            $entidad_nac = Entidad::where('id_renapo', $inputPersona['renapo_nac'] ?? '')->first();
            $entidad_rad = Entidad::where('id_renapo', $inputAlumnos['renapo_rad'] ?? '')->first();

            $inputPersona['entidad_nacimiento'] = $entidad_nac->id ?? null;
            $inputAlumnos['entidad_radica'] = $entidad_rad->id ?? null;

            $datosCurp = Persona::datosCurp($curpStr);
            $inputPersona['fecha_nacimiento'] = "{$datosCurp['year']}-{$datosCurp['mes']}-{$datosCurp['dia']}";

            if (!$esDocenteEnPosgrado) {
                if (!$posgradoRepetido) {
                    $inputAlumnos['id_escuela'] = $id_escuela;

                    DB::beginTransaction();
                    try {
                        if (!$personaExiste) {
                            $nuevaPersona = Persona::create($inputPersona);
                            $inputAlumnos['id_persona'] = $nuevaPersona->id;
                            $url = route('alumnos_docentes_posgrado.editarInformacion', [
                                'id_persona' => $nuevaPersona->id,
                                'id_posgrado' => $id_posgrado_req,
                                'rol' => $rol
                            ]);
                        } else {
                            $personaExiste->update($inputPersona);
                            $inputAlumnos['id_persona'] = $personaExiste->id;
                            $url = route('alumnos_docentes_posgrado.editarInformacion', [
                                'id_persona' => $personaExiste->id,
                                'id_posgrado' => $id_posgrado_req,
                                'rol' => $rol
                            ]);
                        }

                        AlumnosPosgrado::create($inputAlumnos);
                        DB::commit();
                        return redirect($url);

                    } catch (\Exception $e) {
                        DB::rollBack();
                        $errorPosgrado = 'Error al guardar los datos del alumno.';
                    }
                } else {
                    $errorPosgrado = 'El alumno ya está estudiando este posgrado';
                }
            } else {
                $errorPosgrado = 'No puedes estudiar en el posgrado donde eres docente';
            }
        }

        $idPosgradoCriterio = $criteriosAlta['AlumnosPosgrado']['id_posgrado'] ?? null;
        $posgrado = Posgrado::where('id', $idPosgradoCriterio)->pluck('posgrado', 'id')->toArray();
        $periodo = [$criteriosAlta['AlumnosPosgrado']['periodo'] => $criteriosAlta['AlumnosPosgrado']['periodo']];
        $entidades = Entidad::pluck('entidad', 'id_renapo')->toArray();
        $generaciones = GeneracionesPosgrado::where('id_posgrado', $idPosgradoCriterio)->pluck('generacion', 'generacion')->toArray();

        return view('alumnos_docentes_posgrado.agregar_alumno', compact(
            'rol', 'posgrado', 'periodo', 'entidades', 'generaciones', 'errorPosgrado'
        ));
    }

    /**
     * agregarDocentes
     */
    public function agregarDocentes(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        
        $rol = 'd';
        $entidades = Entidad::pluck('entidad', 'id_renapo')->toArray();
        $estado_civil = ['c' => 'Casado(a)', 'd' => 'Divorciado(a)', 's' => 'Soltero(a)', 'v' => 'Viudo(a)'];
        
        $antiguedad = [];
        for ($i = 1; $i <= 40; $i++) {
            $antiguedad[$i] = $i;
        }

        $errorPersona = null;
        $inputPersona = $request->input('Persona');
        $inputDocente = $request->input('DocentesPosgrado');

        if ($request->isMethod('post') && !empty($inputPersona)) {
            $curpStr = $inputPersona['curp'];
            $personaExiste = Persona::where('curp', $curpStr)->first();

            $docenteExiste = null;
            if ($personaExiste) {
                $docenteExiste = DocentesPosgrado::where('id_persona', $personaExiste->id)
                    ->where('id_escuela', $id_escuela)
                    ->first();
            }

            $entidad_nacimiento = Entidad::where('id_renapo', $inputPersona['entidad_nacimiento'] ?? '')->first();
            $inputPersona['entidad_nacimiento'] = $entidad_nacimiento->id ?? null;

            $datosCurp = Persona::datosCurp($curpStr);
            $inputPersona['fecha_nacimiento'] = "{$datosCurp['year']}-{$datosCurp['mes']}-{$datosCurp['dia']}";

            if ($personaExiste && $docenteExiste) {
                $errorPersona = 'El docente ya existe en la institución';
            } else {
                DB::beginTransaction();
                try {
                    if (!$personaExiste && !$docenteExiste) {
                        $nuevaPersona = Persona::create($inputPersona);
                        $inputDocente['id_persona'] = $nuevaPersona->id;
                        $inputDocente['id_escuela'] = $id_escuela;
                        DocentesPosgrado::create($inputDocente);
                        
                        DB::commit();
                        return redirect()->route('alumnos_docentes_posgrado.editarInformacion', [
                            'id_persona' => $nuevaPersona->id, 'id_posgrado' => 'n', 'rol' => 'd'
                        ]);
                    }

                    if ($personaExiste && !$docenteExiste) {
                        $personaExiste->update($inputPersona);
                        $inputDocente['id_persona'] = $personaExiste->id;
                        $inputDocente['id_escuela'] = $id_escuela;
                        DocentesPosgrado::create($inputDocente);
                        
                        DB::commit();
                        return redirect()->route('alumnos_docentes_posgrado.editarInformacion', [
                            'id_persona' => $personaExiste->id, 'id_posgrado' => 'n', 'rol' => 'd'
                        ]);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    $errorPersona = 'Ocurrió un error al guardar el docente.';
                }
            }
        }

        return view('alumnos_docentes_posgrado.agregar_docentes', compact(
            'rol', 'id_escuela', 'entidades', 'estado_civil', 'antiguedad', 'errorPersona'
        ));
    }

    /**
     * ultimoGradoEstudios (Petición AJAX)
     */
    public function ultimoGradoEstudios(Request $request, mixed $id_estudio = null, mixed $id_persona = null, mixed $rol = null, mixed $id_posgrado = null)
    {
        $inputParametros = $request->input('EstudiosParametros');
        if (!empty($inputParametros)) {
            $id_estudio = $inputParametros['id_estudio'] ?? $id_estudio;
            $id_persona = $inputParametros['id_persona'] ?? $id_persona;
            $rol = $inputParametros['rol'] ?? $rol;
            $id_posgrado = $inputParametros['id_posgrado'] ?? $id_posgrado;
        }

        $inputEstudios = $request->input('EstudiosRealizados');
        $inputPreguntas = $request->input('PreguntasPersona');

        if ($request->isMethod('post') && !empty($inputEstudios)) {
            $estudioExistente = EstudiosRealizados::where('id_persona', $inputEstudios['id_persona'])
                ->where('id_estudio', $inputEstudios['id_estudio'])
                ->first();

            if ($estudioExistente) {
                $fecha_tit = ($inputEstudios['anio'] ?? '') . '-' . ($inputEstudios['mes'] ?? '') . '-' . ($inputEstudios['dia'] ?? '');
                $inputEstudios['fecha_titulacion'] = $fecha_tit;
                $estudioExistente->update($inputEstudios);
            }

            if (!empty($inputPreguntas)) {
                foreach ($inputPreguntas as $preg) {
                    PreguntasPersona::updateOrCreate(
                        ['id_persona' => $preg['id_persona'], 'id_pregunta' => $preg['id_pregunta']],
                        ['confirmacion' => $preg['confirmacion'], 'respuesta' => $preg['respuesta']]
                    );
                }
            }
        }

        $notificacion = ['s' => 'Si', 'n' => 'No'];
        
        $year = [];
        for ($i = 2026; $i >= 1950; $i--) { $year[$i] = $i; }
        
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

        $mod_titulacion = ModosTitulacion::pluck('tipo_titulacion', 'id')->toArray();
        $respuestas_preguntas = PreguntasPersona::where('id_persona', $id_persona)->get();
        $persona_estudios = EstudiosRealizados::where('id_estudio', $id_estudio)->where('id_persona', $id_persona)->first();

        $data = [];
        if ($persona_estudios && empty($inputEstudios)) {
            $data['EstudiosRealizados'] = $persona_estudios->toArray();
            if (!empty($data['EstudiosRealizados']['fecha_titulacion'])) {
                $parts = explode('-', $data['EstudiosRealizados']['fecha_titulacion']);
                $data['EstudiosRealizados']['anio'] = $parts[0] ?? '';
                $data['EstudiosRealizados']['mes'] = $parts[1] ?? '';
                $data['EstudiosRealizados']['dia'] = $parts[2] ?? '';
            }
            foreach ($respuestas_preguntas as $resp) {
                $data['PreguntasPersona'][$resp->id_pregunta] = $resp->toArray();
            }
        }

        return view('alumnos_docentes_posgrado.ultimo_grado_estudios', compact(
            'rol', 'id_posgrado', 'notificacion', 'id_estudio', 'id_persona',
            'year', 'dia', 'mes', 'mod_titulacion', 'data'
        ));
    }

    /**
     * editarInformacion
     */
    public function editarInformacion(Request $request, mixed $id_persona = null, mixed $id_posgrado = null, mixed $rol = null)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel = $ctx['nivel'] ?? null;

        if (!$id_persona || !$id_posgrado || !$rol) {
            return redirect()->route('siben.index');
        }

        $queryPersona = Persona::where('id', $id_persona);
        if ($rol == 'a') {
            $queryPersona->whereHas('alumnosPosgrado', function($q) use ($id_escuela, $id_posgrado) {
                $q->where('id_escuela', $id_escuela)->where('id_posgrado', $id_posgrado);
            });
        } elseif ($rol == 'd') {
            $queryPersona->whereHas('docentesPosgrado', function($q) use ($id_escuela) {
                $q->where('id_escuela', $id_escuela);
            });
        }

        $info_captada = $queryPersona->first();
        if (!$info_captada) {
            return redirect()->route('alumnos_docentes_posgrado.index');
        }

        $num_periodosObj = Posgrado::where('id_escuela', $id_escuela)->where('id', $id_posgrado)->first();
        $periodos = [];
        if ($num_periodosObj) {
            for ($i = 1; $i <= $num_periodosObj->num_periodos; $i++) {
                $periodos[$i] = $i;
            }
        }

        // Variables de vista base
        $dataView = compact('id_persona', 'id_posgrado', 'rol', 'periodos', 'info_captada');

        if ($rol == 'd') {
            $docente = DocentesPosgrado::where('id_persona', $id_persona)->where('id_escuela', $id_escuela)->first();
            $id_docente = $docente->id ?? 0;

            $dataView['estado_civil'] = ['c' => 'Casado(a)', 'd' => 'Divorciado(a)', 's' => 'Soltero(a)', 'v' => 'Viudo(a)'];
            $antiguedad = [];
            for ($i = 1; $i <= 40; $i++) { $antiguedad[$i] = $i; }
            $dataView['antiguedad'] = $antiguedad;

            $dataView['posgrados'] = Posgrado::where('id_escuela', $id_escuela)->pluck('posgrado', 'id')->toArray();
            $dataView['contrataciones'] = FormaContratacion::where('id', '<', 20)->pluck('contratacion', 'id')->toArray();
            $dataView['tiempos'] = FormaContratacion::where('id', '>', 20)->pluck('contratacion', 'id')->toArray();
            $dataView['niveles'] = NivelesEducacion::groupBy('id_nivel')->pluck('Nivel', 'id_nivel')->toArray();
            
            $experiencia = ExperienciaDocente::with('nivelesEducacion')->whereHas('docentesPosgrado', function($q) use ($id_escuela) {
                $q->where('id_escuela', $id_escuela);
            })->where('id_docente', $id_docente)->get();
            
            $dataView['experiencia'] = $experiencia;
            $dataView['voluntarioExperiencia'] = $experiencia->isNotEmpty();

            $contrataciones_docente = ContratacionesPosgrado::whereHas('docentesPosgrado', function($q) use ($id_escuela) {
                $q->where('id_escuela', $id_escuela);
            })->where('id_docente', $id_docente)->get();

            $dataView['contrataciones_docente'] = $contrataciones_docente;
            $dataView['voluntarioContrataciones'] = $contrataciones_docente->isNotEmpty();

            $posgrados_docente = PosgradosDocentes::with('posgrado')->whereHas('docentesPosgrado', function($q) use ($id_escuela) {
                $q->where('id_escuela', $id_escuela);
            })->where('id_docente', $id_docente)->get();

            $dataView['posgrados_docente'] = $posgrados_docente;
            $dataView['DoscentePosgrado'] = $posgrados_docente->isNotEmpty();
        }

        $estudios_realizados = EstudiosRealizados::with('estudios')->where('id_persona', $id_persona)->get();
        $mayor = EstudiosRealizados::where('id_persona', $id_persona)->max('id_estudio');
        
        $dataView['mayor'] = $mayor;
        $dataView['estudios_realizados'] = $estudios_realizados;
        $dataView['voluntarioEstudios'] = $estudios_realizados->isNotEmpty();

        $persona_lenguas = LenguaHabilidades::with('extranjeraIndigena')->where('id_persona', $id_persona)->get();
        $dataView['persona_lenguas'] = $persona_lenguas;
        $dataView['voluntarioLenguas'] = $persona_lenguas->isNotEmpty();

        $dataView['estudios'] = Estudios::pluck('estudios', 'id')->toArray();
        $dataView['lenguas'] = ExtranjeraIndigena::pluck('lengua', 'id')->toArray();

        $alumnoPosgrado = AlumnosPosgrado::where('id_persona', $id_persona)->where('id_posgrado', $id_posgrado)->first();
        $dataView['posgrado'] = Posgrado::where('id', $id_posgrado)->pluck('posgrado', 'id')->toArray();
        
        $entidades = Entidad::where('id', '<=', 33)->pluck('entidad', 'id')->toArray();
        $dataView['entidades'] = $entidades;
        $dataView['entidades_nacimiento'] = $entidades + ['33' => 'Nacido en el Extranjero'];
        
        $periodoAlumno = $alumnoPosgrado->periodo ?? null;
        $dataView['periodo'] = $periodoAlumno ? [$periodoAlumno => $periodoAlumno] : [];
        $dataView['generaciones'] = GeneracionesPosgrado::where('id_posgrado', $id_posgrado)->pluck('generacion', 'generacion')->toArray();
        $dataView['error'] = null;

        $inputPersona = $request->input('Persona');
        $inputAlumnos = $request->input('AlumnosPosgrado');
        $inputDocentes = $request->input('DocentesPosgrado');

        if ($request->isMethod('post') && !empty($inputPersona)) {
            $datosCurp = Persona::datosCurp($inputPersona['curp']);
            $inputPersona['fecha_nacimiento'] = "{$datosCurp['year']}-{$datosCurp['mes']}-{$datosCurp['dia']}";

            $cuenta_pregunta = PreguntasPersona::where('id_persona', $id_persona)->count();

            if ($cuenta_pregunta > 0) {
                if ($nivel == 3) {
                    Persona::where('id', $id_persona)->update($inputPersona);
                }

                if (!empty($inputAlumnos) && $rol == 'a') {
                    if ($nivel == 4) {
                        $inputAlumnos['status'] = 'a';
                    }
                    $alumno = AlumnosPosgrado::where('id_persona', $id_persona)->where('id_posgrado', $id_posgrado)->first();
                    if ($alumno) {
                        $alumno->update($inputAlumnos);
                    }

                    $criteriosAlta = session()->get('criteriosAlta');
                    if (!empty($criteriosAlta)) {
                        $uri = ($criteriosAlta['AlumnosPosgrado']['busqueda'] ?? '') == 'r' 
                            ? route('alumnos_docentes_posgrado.listas') 
                            : route('alumnos_docentes_posgrado.agregarAlumno');
                    } else {
                        $uri = route('alumnos_docentes_posgrado.listas');
                    }
                    return redirect($uri);
                }

                if (!empty($inputDocentes) && $rol == 'd') {
                    if ($nivel == 4) {
                        $inputDocentes['status'] = 'a';
                    }
                    $docente = DocentesPosgrado::where('id_persona', $id_persona)->where('id_escuela', $id_escuela)->first();
                    if ($docente) {
                        $docente->update($inputDocentes);
                    }
                    return redirect()->route('alumnos_docentes_posgrado.listasDocentes');
                }
            } else {
                $dataView['error'] = 'De click en el boton que dice maximo para llenar la informacion que falte';
            }
        }

        return view('alumnos_docentes_posgrado.editar_informacion', $dataView);
    }

    /**
     * listas
     */
    public function listas(?string $opcion = null)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel = $ctx['nivel'] ?? null;
        $captura = $ctx['captura'] ?? null;
        
        if ($captura != 1) {
            return redirect()->route('alumnos_docentes_posgrado.index');
        }

        $criterios = session()->get('criteriosAlta');
        $posgradosList = Posgrado::where('id_escuela', $id_escuela)->pluck('posgrado', 'id')->toArray();

        $query = AlumnosPosgrado::with('persona', 'posgrado')
            ->where('id_escuela', $id_escuela)
            ->whereIn('status', ['a', 'x', 'b']);

        if ($criterios) {
            if (!empty($criterios['AlumnosPosgrado']['modalidad'])) {
                $query->whereHas('posgrado', fn($q) => $q->where('modalidad', $criterios['AlumnosPosgrado']['modalidad']));
            }
            if (!empty($criterios['AlumnosPosgrado']['id_posgrado'])) {
                $query->where('id_posgrado', $criterios['AlumnosPosgrado']['id_posgrado']);
            }
            if (!empty($criterios['AlumnosPosgrado']['periodo'])) {
                $query->where('periodo', $criterios['AlumnosPosgrado']['periodo']);
            }
        }

        $posgradosAlumnos = $query->join('p_personas', 'p_alumnos.id_persona', '=', 'p_personas.id')
            ->orderBy('p_alumnos.id_posgrado')
            ->orderBy('p_alumnos.periodo')
            ->orderBy('p_alumnos.status', 'desc')
            ->orderBy('p_personas.apellido_paterno', 'asc')
            ->select('p_alumnos.*')
            ->get();

        $datosFormateados = [];
        foreach ($posgradosAlumnos as $alumno) {
            $evento = "";
            $imagen = "";
            $titulo = "";
            $estado = "";
            $estilo = "";

            if ($opcion == 'b' && $nivel == 4) {
                $urlBaja = route('alumnos_docentes_posgrado.bajaAlumno', ['id_persona' => $alumno->persona->id, 'id_posgrado' => $alumno->id_posgrado, 'rol' => 'a']);
                $evento = "locacion('{$urlBaja}')";
                $imagen = 'button2.png';
                $titulo = 'Baja';

                switch ($alumno->status) {
                    case 'a':
                        $estilo = 'text-align:center;background-color:#054205;color:#ccffcc;font-weight:bold;';
                        $estado = 'Activo';
                        break;
                    case 'x':
                        $estilo = 'text-align:center;background-color:#cc0000;color:#ffcccc;font-weight:bold;';
                        $estado = 'Inactivo';
                        break;
                    case 'b':
                        $estilo = 'text-align:center;background-color:#0066cc;color:#ccffff;font-weight:bold;';
                        $estado = 'Baja';
                        $evento = ''; $imagen = ''; $titulo = '';
                        break;
                }
            } else {
                switch ($alumno->status) {
                    case 'a':
                        $estilo = 'text-align:center;background-color:#054205;color:#ccffcc;font-weight:bold;';
                        $estado = 'Activo';
                        if ($nivel == 3) {
                            $urlEdit = route('alumnos_docentes_posgrado.editarInformacion', ['id_persona' => $alumno->persona->id, 'id_posgrado' => $alumno->id_posgrado, 'rol' => 'a']);
                            $evento = "locacion('{$urlEdit}')";
                            $imagen = 'editar3.png';
                            $titulo = 'Editar';
                        }
                        if ($nivel == 4) {
                            $urlDesactivar = route('alumnos_docentes_posgrado.editaEstado', ['id' => $alumno->persona->id]);
                            $evento = "locacion('{$urlDesactivar}')";
                            $imagen = 'desactivar4.png';
                            $titulo = 'Desactivar';
                        }
                        break;
                    case 'x':
                        $estilo = 'text-align:center;background-color:#cc0000;color:#ffcccc;font-weight:bold;';
                        $estado = 'Inactivo';
                        if ($nivel == 4 || $nivel == 3) {
                            $urlAccion = route('alumnos_docentes_posgrado.editarInformacion', ['id_persona' => $alumno->persona->id, 'id_posgrado' => $alumno->id_posgrado, 'rol' => 'a']);
                            $evento = "locacion('{$urlAccion}')";
                            $imagen = ($nivel == 4) ? 'activar3.png' : 'editar3.png';
                            $titulo = ($nivel == 4) ? 'Activar' : 'Editar';
                        }
                        break;
                    case 'b':
                        $estilo = 'text-align:center;background-color:#0066cc;color:#ccffff;font-weight:bold;';
                        $estado = 'Baja';
                        break;
                }
            }

            $datosFormateados[] = [
                'Persona' => $alumno->persona->toArray(),
                'AlumnosPosgrado' => array_merge($alumno->toArray(), [
                    'estilo' => "<div style='{$estilo}'>{$estado}</div>",
                    'evento' => $evento,
                    'imagen' => $imagen,
                    'titulo' => $titulo,
                ])
            ];
        }

        $anterior = route('alumnos_docentes_posgrado.index');
        $cabeceras = ['No&nbsp;&nbsp;', 'Primer Apellido&nbsp;&nbsp;', 'Segundo Apellido&nbsp;&nbsp;', 'Nombre(s)&nbsp;&nbsp;', 'Curp&nbsp;&nbsp;', 'Status&nbsp;&nbsp;', 'Acción&nbsp;&nbsp;'];

        return view('alumnos_docentes_posgrado.listas', compact('datosFormateados', 'posgradosList', 'cabeceras', 'anterior'));
    }

    /**
     * ListasDocentes
     */
    public function ListasDocentes()
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel = $ctx['nivel'] ?? null;

        $docentesRaw = DocentesPosgrado::with('persona')
            ->where('id_escuela', $id_escuela)
            ->join('p_personas', 'p_docentes.id_persona', '=', 'p_personas.id')
            ->orderBy('p_docentes.status')
            ->orderBy('p_personas.apellido_paterno')
            ->orderBy('p_personas.apellido_materno')
            ->orderBy('p_personas.nombre')
            ->select('p_docentes.*')
            ->get();

        $docentes = [];
        foreach ($docentesRaw as $docente) {
            $evento = "";
            $imagen = "";
            $titulo = "";

            switch ($docente->status) {
                case 'a':
                    if ($nivel == 4) {
                        $urlAccion = route('alumnos_docentes_posgrado.editaEstadodoc', ['id' => $docente->persona->id]);
                        $evento = "locacion('{$urlAccion}')";
                        $imagen = 'desactivar4.png';
                        $titulo = 'Desactivar';
                    }
                    if ($nivel == 3) {
                        $urlAccion = route('alumnos_docentes_posgrado.editarInformacion', ['id_persona' => $docente->persona->id, 'id_posgrado' => 'n', 'rol' => 'd']);
                        $evento = "locacion('{$urlAccion}')";
                        $imagen = 'editar3.png';
                        $titulo = 'Editar';
                    }
                    break;
                case 'x':
                    if ($nivel == 4 || $nivel == 3) {
                        $urlAccion = route('alumnos_docentes_posgrado.editarInformacion', ['id_persona' => $docente->persona->id, 'id_posgrado' => 'n', 'rol' => 'd']);
                        $evento = "locacion('{$urlAccion}')";
                        $imagen = ($nivel == 4) ? 'activar3.png' : 'editar3.png';
                        $titulo = ($nivel == 4) ? 'Activar' : 'Editar';
                    }
                    break;
            }

            $docentes[] = [
                'Persona' => $docente->persona->toArray(),
                'DocentesPosgrado' => array_merge($docente->toArray(), [
                    'estilo' => ($docente->status == 'a' ? 'background-color:green;' : 'background-color:red;'),
                    'evento' => $evento,
                    'imagen' => $imagen,
                    'titulo' => $titulo,
                ])
            ];
        }

        $cabeceras = ['No&nbsp;&nbsp;', 'Primer Apellido&nbsp;&nbsp;', 'Segundo Apellido&nbsp;&nbsp;', 'Nombre(s)&nbsp;&nbsp;', 'Curp&nbsp;&nbsp;', 'Status&nbsp;&nbsp;', 'Acción'];

        return view('alumnos_docentes_posgrado.listas_docentes', compact('docentes', 'cabeceras'));
    }

    /**
     * periodosPosgrado (AJAX)
     */
    public function periodosPosgrado(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $input = $request->input('AlumnosPosgrado', []);
        
        $posgrado = Posgrado::where('id_escuela', $id_escuela)->where('id', $input['id_posgrado'] ?? 0)->first();
        
        $opciones = [];
        if ($posgrado) {
            for ($i = 1; $i <= $posgrado->num_periodos; $i++) {
                $opciones[$i] = $i;
            }
        }

        return view('elements.opciones', ['opciones' => $opciones, 'noelegido' => false]);
    }

    /**
     * posgrados (AJAX)
     */
    public function posgrados(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $input = $request->input('AlumnosPosgrado', []);
        
        $opciones = Posgrado::where('id_escuela', $id_escuela)
            ->where('modalidad', $input['modalidad'] ?? '')
            ->pluck('posgrado', 'id')
            ->toArray();

        return view('elements.opciones', ['opciones' => $opciones, 'noelegido' => false]);
    }

    /**
     * estudiosAlumno (AJAX)
     */
    public function estudiosAlumno(Request $request)
    {
        $input = $request->input('EstudiosRealizados', []);
        
        $id_estudio = $input['id_estudio'] ?? null;
        $id_persona = $input['id_persona'] ?? null;

        $opciones = [];
        $clase = '';

        if (!empty($id_estudio)) {
            $existente = EstudiosRealizados::where('id_persona', $id_persona)->where('id_estudio', $id_estudio)->first();
            
            if (!$existente) {
                $nuevo = EstudiosRealizados::create($input);
                $estudio = Estudios::find($id_estudio);

                $urlElimina = route('alumnos_docentes_posgrado.eliminarDatosDocente');
                $evento_elimina = "eliminar('{$urlElimina}', 'estudios', {$nuevo->id})";

                $opciones = [
                    $id_estudio,
                    $estudio->estudios ?? '',
                    '<div style="display:none;" class ="gradoMAximo" id = "gradoMaximo' . $id_estudio . '"></div>',
                    "<input type=\"button\" value=\"Elimina\" onclick=\"$evento_elimina\" />"
                ];
                $clase = 'estudios_' . $nuevo->id;
            }
        }

        return view('elements.renglonEstudios2', ['renglon' => $opciones, 'clase' => $clase]);
    }

    /**
     * lenguasPersona (AJAX)
     */
    public function lenguasPersona(Request $request)
    {
        $input = $request->input('lenguaHabilidades', []);
        
        $id_extrangera_indigena = $input['id_extrangera_indigena'] ?? null;
        $id_persona = $input['id_persona'] ?? null;
        $lectura = $input['comprension_lectora'] ?? 0;
        $escritura = $input['escritura'] ?? 0;
        $expresion = $input['expresion_oral'] ?? 0;

        $opciones = [];
        $clase = '';

        if ($id_extrangera_indigena && $lectura <= 100 && $escritura <= 100 && $expresion <= 100) {
            $existente = LenguaHabilidades::where('id_extrangera_indigena', $id_extrangera_indigena)
                ->where('id_persona', $id_persona)
                ->first();

            if (!$existente) {
                $nueva = LenguaHabilidades::create($input);
                $lengua = ExtranjeraIndigena::find($id_extrangera_indigena);

                $urlElimina = route('alumnos_docentes_posgrado.eliminarDatosDocente');
                $evento_elimina = "eliminar('{$urlElimina}', 'lenguas', {$nueva->id})";

                $opciones = [
                    $lengua->lengua ?? '',
                    $lectura,
                    $escritura,
                    $expresion,
                    "<input type=\"button\" value=\"Elimina\" onclick=\"$evento_elimina\" />"
                ];
                $clase = 'lenguas_' . $nueva->id;
            }
        }

        return view('elements.renglon_clase', ['renglon' => $opciones, 'clase' => $clase]);
    }

    /**
     * eliminarDatosDocente (AJAX)
     */
    public function eliminarDatosDocente(Request $request)
    {
        $tipo = $request->input('tipo');
        $id = $request->input('id');
        $success = false;

        if ($tipo && $id) {
            switch ($tipo) {
                case 'lenguas':
                    $success = (bool) LenguaHabilidades::where('id', $id)->delete();
                    break;
                case 'contrataciones':
                    $success = (bool) ContratacionesPosgrado::where('id', $id)->delete();
                    break;
                case 'posgrados':
                    $success = (bool) PosgradosDocentes::where('id', $id)->delete();
                    break;
                case 'experiencia':
                    $success = (bool) ExperienciaDocente::where('id', $id)->delete();
                    break;
                case 'estudios':
                    $success = (bool) EstudiosRealizados::where('id', $id)->delete();
                    break;
            }
        }

        return response()->json(['success' => $success, 'tipo' => $tipo]);
    }

    /**
     * NivelesEducacionDocente (AJAX)
     */
    public function NivelesEducacionDocente(Request $request)
    {
        $input = $request->input('ExperienciaDocente', []);
        $nivel = $input['tipo'] ?? null;
        
        $subniveles = NivelesEducacion::where('id_nivel', $nivel)
            ->groupBy('id')
            ->pluck('sub_nivel', 'id')
            ->toArray();

        return view('elements.opciones', ['opciones' => $subniveles, 'noelegido' => false]);
    }

    /**
     * posgradosDocentes (AJAX)
     */
    public function posgradosDocentes(Request $request)
    {
        $inputPos = $request->input('PosgradosDocentes', []);
        $inputPer = $request->input('Persona', []);
        $inputDoc = $request->input('DocentesPosgrado', []);

        $posgrado = $inputPos['id_posgrado'] ?? 0;
        $persona = $inputPer['id'] ?? 0;
        $id_docente = $inputDoc['id'] ?? 0;

        $opciones = [];
        $clase = '';

        if ($posgrado > 0 && $id_docente > 0) {
            $alumno = AlumnosPosgrado::where('id_posgrado', $posgrado)
                ->where('id_persona', $persona)
                ->where('status', 'a')
                ->first();

            if ($alumno) {
                $opciones[0] = "<div style='color:#FF0000;'>Eres Alumno de este posgrado no puedes impartirlo</div>";
            } else {
                $existente = PosgradosDocentes::where('id_posgrado', $posgrado)
                    ->where('id_docente', $id_docente)
                    ->first();

                if (!$existente) {
                    $inputPos['id_docente'] = $id_docente;
                    $nuevo = PosgradosDocentes::create($inputPos);
                    
                    $nombre_pos = Posgrado::find($posgrado);
                    
                    $urlElimina = route('alumnos_docentes_posgrado.eliminarDatosDocente');
                    $evento_elimina = "eliminar('{$urlElimina}', 'posgrados', {$nuevo->id})";

                    $opciones = [
                        $nombre_pos->posgrado ?? '',
                        "<input type=\"button\" value=\"Elimina\" onclick=\"$evento_elimina\" />"
                    ];
                    $clase = 'posgrados_' . $nuevo->id;
                }
            }
        }

        return view('elements.renglon_clase', ['renglon' => $opciones, 'clase' => $clase]);
    }

    /**
     * contratacionesDocentes (AJAX)
     */
    public function contratacionesDocentes(Request $request)
    {
        $input = $request->input('ContratacionesPosgrado', []);
        $inputDoc = $request->input('DocentesPosgrado', []);
        
        $forma_contratacion = $input['id_contratacion'] ?? 0;
        $tiempo_contratacion = $input['id_tiempo_dedicacion'] ?? 0;
        $id_docente = $inputDoc['id'] ?? 0;

        $contrataciones = FormaContratacion::pluck('contratacion', 'id')->toArray();
        
        $opcionArr = [];
        $clase = '';

        if ($tiempo_contratacion > 0 && $forma_contratacion > 0 && $id_docente > 0) {
            $existente = ContratacionesPosgrado::where('id_docente', $id_docente)
                ->where('id_contratacion', $forma_contratacion)
                ->where('id_tiempo_dedicacion', $tiempo_contratacion)
                ->first();

            if (!$existente) {
                $input['id_docente'] = $id_docente;
                $nuevo = ContratacionesPosgrado::create($input);

                $urlElimina = route('alumnos_docentes_posgrado.eliminarDatosDocente');
                $evento_elimina = "eliminar('{$urlElimina}', 'contrataciones', {$nuevo->id})";

                $opcionArr = [
                    $contrataciones[$forma_contratacion] ?? '',
                    $contrataciones[$tiempo_contratacion] ?? '',
                    "<input type=\"button\" value=\"Elimina\" onclick=\"$evento_elimina\" />"
                ];
                $clase = 'contrataciones_' . $nuevo->id;
            }
        }

        return view('elements.renglon_clase', ['renglon' => $opcionArr, 'clase' => $clase]);
    }

    /**
     * experienciaDocentes (AJAX)
     */
    public function experienciaDocentes(Request $request)
    {
        $input = $request->input('ExperienciaDocente', []);
        $inputDoc = $request->input('DocentesPosgrado', []);

        $tipo = $input['tipo'] ?? null;
        $nivel = $input['id_nivel'] ?? null;
        $anio = $input['anios'] ?? 0;
        $id_docente = $inputDoc['id'] ?? 0;

        $opciones = [];
        $clase = '';

        if (!empty($tipo) && !empty($nivel) && !empty($anio) && $id_docente > 0) {
            $existente = ExperienciaDocente::where('id_docente', $id_docente)->where('id_nivel', $nivel)->first();

            if (!$existente) {
                $input['id_docente'] = $id_docente;
                $nuevo = ExperienciaDocente::create($input);
                $renglonInfo = NivelesEducacion::where('id_nivel', $tipo)->where('id', $nivel)->first();

                $urlElimina = route('alumnos_docentes_posgrado.eliminarDatosDocente');
                $evento_elimina = "eliminar('{$urlElimina}', 'experiencia', {$nuevo->id})";

                $opciones = [
                    $renglonInfo->nivel ?? '',
                    $renglonInfo->sub_nivel ?? '',
                    $anio,
                    "<input type=\"button\" value=\"Elimina\" onclick=\"$evento_elimina\" />"
                ];
                $clase = 'experiencia_' . $nuevo->id;
            }
        }

        return view('elements.renglon_clase', ['renglon' => $opciones, 'clase' => $clase]);
    }

    /**
     * ActualizaUltimoGrado
     */
    public function ActualizaUltimoGrado(Request $request)
    {
        $inputEstudios = $request->input('EstudiosRealizados');
        $inputPreguntas = $request->input('PreguntasPersona');

        if (!empty($inputEstudios)) {
            $estudio = EstudiosRealizados::where('id_persona', $inputEstudios['id_persona'])
                ->where('id_estudio', $inputEstudios['id_estudio'])
                ->first();
                
            if ($estudio) {
                $inputEstudios['fecha_titulacion'] = ($inputEstudios['anio'] ?? '') . '-' . ($inputEstudios['mes'] ?? '') . '-' . ($inputEstudios['dia'] ?? '');
                $estudio->update($inputEstudios);
            }

            if (!empty($inputPreguntas)) {
                foreach ($inputPreguntas as $preg) {
                    PreguntasPersona::updateOrCreate(
                        ['id_persona' => $preg['id_persona'], 'id_pregunta' => $preg['id_pregunta']],
                        ['confirmacion' => $preg['confirmacion'], 'respuesta' => $preg['respuesta']]
                    );
                }
            }
        }

        return redirect()->back();
    }

    /**
     * editaEstado
     */
    public function editaEstado(mixed $id = null)
    {
        if ($id) {
            $alumno = AlumnosPosgrado::where('id_persona', $id)->first();
            if ($alumno) {
                $nuevoStatus = ($alumno->status == 'x') ? 'a' : 'x';
                AlumnosPosgrado::where('id_persona', $id)->update(['status' => $nuevoStatus]);
            }
        }

        return redirect()->route('alumnos_docentes_posgrado.listas');
    }

    /**
     * editaEstadodoc
     */
    public function editaEstadodoc(mixed $id = null)
    {
        if ($id) {
            $docente = DocentesPosgrado::where('id_persona', $id)->first();
            if ($docente) {
                $nuevoStatus = ($docente->status == 'x') ? 'a' : 'x';
                DocentesPosgrado::where('id_persona', $id)->update(['status' => $nuevoStatus]);
            }
        }

        return redirect()->route('alumnos_docentes_posgrado.listasDocentes');
    }

    /**
     * bajaAlumno
     */
    public function bajaAlumno(Request $request, mixed $id_persona = null, mixed $id_posgrado = null, mixed $rol = null)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel = $ctx['nivel'] ?? null;

        if (!$id_persona || !$id_posgrado || !$rol) {
            return redirect()->route('siben.index');
        }

        $info_captada = Persona::where('id', $id_persona)
            ->whereHas('alumnosPosgrado', function($q) use ($id_escuela, $id_posgrado) {
                $q->where('id_escuela', $id_escuela)->where('id_posgrado', $id_posgrado);
            })->first();

        if (!$info_captada) {
            return redirect()->route('alumnos_docentes_posgrado.index');
        }

        $alumno = AlumnosPosgrado::where('id_persona', $id_persona)->first();
        
        $tipo_baja = ['1' => 'Abandono', '2' => 'Reprobación'];
        $motivo_baja = ['1' => 'Motivos Socioeconomicos', '2' => 'Motivos Laborales', '3' => 'Motivos de Salud', '4' => 'Motivos Familiares'];
        
        $num_periodosObj = Posgrado::where('id_escuela', $id_escuela)->where('id', $id_posgrado)->first();
        $periodos = [];
        if ($num_periodosObj) {
            for ($i = 1; $i <= $num_periodosObj->num_periodos; $i++) {
                $periodos[$i] = $i;
            }
        }

        $posgrado = Posgrado::where('id', $id_posgrado)->pluck('posgrado', 'id')->toArray();
        $entidades = Entidad::where('id', '<=', 33)->pluck('entidad', 'id')->toArray();
        $entidades_nacimiento = $entidades + ['33' => 'Nacido en el Extranjero'];
        $generaciones = GeneracionesPosgrado::where('id_posgrado', $id_posgrado)->pluck('generacion', 'generacion')->toArray();
        $periodo = $alumno ? [$alumno->periodo => $alumno->periodo] : [];

        $inputPersona = $request->input('Persona');
        $inputAlumnos = $request->input('AlumnosPosgrado');
        $inputBajas = $request->input('BajasPosgrado');

        if ($request->isMethod('post') && !empty($inputPersona)) {
            $datosCurp = Persona::datosCurp($inputPersona['curp']);
            $inputPersona['fecha_nacimiento'] = "{$datosCurp['year']}-{$datosCurp['mes']}-{$datosCurp['dia']}";

            if (!empty($inputAlumnos) && $rol == 'a') {
                if ($nivel == 4 && $alumno) {
                    $alumno->update(['status' => 'b']);
                }

                if (!empty($inputBajas)) {
                    if ($inputBajas['id_tipo'] == 2) {
                        $inputBajas['id_motivo'] = 0;
                    }
                    $inputBajas['id_persona'] = $id_persona;
                    $inputBajas['fecha'] = date('Y-m-d');
                    BajasPosgrado::create($inputBajas);
                }

                return redirect()->route('alumnos_docentes_posgrado.listas', ['opcion' => 'b']);
            }
        }

        return view('alumnos_docentes_posgrado.baja_alumno', compact(
            'tipo_baja', 'motivo_baja', 'id_persona', 'id_posgrado', 'rol', 
            'posgrado', 'periodo', 'entidades', 'entidades_nacimiento', 
            'generaciones', 'periodos', 'info_captada'
        ));
    }
}