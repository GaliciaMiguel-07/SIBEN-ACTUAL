<?php

namespace App\Http\Controllers;

use App\Models\ExtrangeraIndigena;
use App\Models\Posgrado;
use App\Models\AlumnosPosgrado;
use App\Models\EstudiosRealizadosAlumnos;
use App\Models\Escuela;
use App\Models\Entidad;
use App\Models\Estudios;
use Illuminate\Http\Request;

class AlumnosPosgradoController extends sibenController
{
    /**
     * index
     */
    public function index()
    {
        session()->forget('criteriosAlta');
        return view('alumnos_posgrado.index');
    }

    /**
     * opcionesAlta
     * Permite crear las opciones de alta de un alumno de posgrado.
     */
    public function opcionesAlta(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $posgrados = Posgrado::where('id_escuela', $id_escuela)
            ->pluck('posgrado', 'id');

        $anterior = '/alumnosPosgrado';

        $idPosgrado = $request->input('AlumnosPosgrado.id_posgrado');
        $periodo = $request->input('AlumnosPosgrado.periodo');

        if (!empty($idPosgrado) && !empty($periodo)) {
            session(['criteriosAlta' => $request->all()]);
            return redirect()->route('alumnosPosgrado.agregarAlumno');
        } else {
            session()->forget('criteriosAlta');
        }

        return view('alumnos_posgrado.opciones_alta', compact('posgrados', 'anterior'));
    }

    /**
     * agregarAlumno
     * Realiza la inserción de la información de los alumnos de posgrado
     */
    public function agregarAlumno(Request $request)
    {
        session()->forget('EstudiosElegidos');
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;

        $criteriosAlta = session('criteriosAlta');

        if (empty($criteriosAlta)) {
            return redirect()->route('alumnosPosgrado.opcionesAlta');
        }

        if ($request->isMethod('post') && !empty($request->all())) {
            $dataAlumnos = $request->input('AlumnosPosgrado', []);
            $curp = $dataAlumnos['curp'] ?? null;
            $idPosgrado = $dataAlumnos['id_posgrado'] ?? null;

            $posgradoRepetido = AlumnosPosgrado::where('id_escuela', $id_escuela)
                ->where('id_posgrado', $idPosgrado)
                ->where('curp', $curp)
                ->exists();

            $entidadNacRenapo = $dataAlumnos['renapo_nac'] ?? null;
            $entidadRadRenapo = $dataAlumnos['renapo_rad'] ?? null;

            $entidadNacimiento = Entidad::where('id_renapo', $entidadNacRenapo)->first();
            $entidadRadica = Entidad::where('id_renapo', $entidadRadRenapo)->first();

            $dataAlumnos['entidad_nacimiento'] = $entidadNacimiento->id ?? null;
            $dataAlumnos['entidad_radica'] = $entidadRadica->id ?? null;

            if (!$posgradoRepetido) {
                $dataAlumnos['id_escuela'] = $id_escuela;
                $nuevoAlumno = AlumnosPosgrado::create($dataAlumnos);
                return redirect()->route('alumnosPosgrado.alumnoEdita', ['id_alumno' => $nuevoAlumno->id]);
            } else {
                return redirect()->route('alumnosPosgrado.agregarAlumno');
            }
        }

        $idPosgradoCriterio = $criteriosAlta['AlumnosPosgrado']['id_posgrado'] ?? null;
        $posgrado = Posgrado::where('id', $idPosgradoCriterio)->pluck('posgrado', 'id');

        $periodoVal = $criteriosAlta['AlumnosPosgrado']['periodo'] ?? null;
        $periodo = $periodoVal ? [$periodoVal => $periodoVal] : [];

        $entidades = Entidad::pluck('entidad', 'id_renapo');

        return view('alumnos_posgrado.agregar_alumno', compact('posgrado', 'periodo', 'entidades'));
    }

    /**
     * alumnoDetalle
     * Captura información de los estudios realizados, lenguajes y otras preguntas
     */
    public function alumnoDetalle($id_alumno = null)
    {
        if (!$id_alumno) {
            return redirect()->route('alumnosPosgrado.index');
        }

        $estudios_realizados = EstudiosRealizadosAlumnos::where('id_alumno', $id_alumno)
            ->pluck('id_estudio');

        $estudios = Estudios::pluck('estudios', 'id');

        return view('alumnos_posgrado.alumno_detalle', compact('id_alumno', 'estudios_realizados', 'estudios'));
    }

    /**
     * peridosPosgrado
     * Función ejecutada vía AJAX para obtener los periodos del posgrado
     */
    public function peridosPosgrado(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $idPosgrado = $request->input('AlumnosPosgrado.id_posgrado');

        $posgrado = Posgrado::where('id_escuela', $id_escuela)
            ->where('id', $idPosgrado)
            ->first();

        $periodos = [];
        if ($posgrado && $posgrado->num_periodos) {
            for ($i = 1; $i <= $posgrado->num_periodos; $i++) {
                $periodos[$i] = $i;
            }
        }

        return view('elements.opciones', ['opciones' => $periodos, 'noelegido' => false]);
    }

    /**
     * estudiosAlumno
     * Función ejecutada vía AJAX para guardar los estudios realizados del alumno
     */
    public function estudiosAlumno(Request $request)
    {
        $dataEstudio = $request->input('EstudiosRealizadosAlumnos', []);
        $id_estudio = $dataEstudio['id_estudio'] ?? null;
        $id_alumno = $dataEstudio['id_alumno'] ?? null;

        $opciones = [];
        if (!empty($id_estudio)) {
            $capturado = EstudiosRealizadosAlumnos::where('id_alumno', $id_alumno)
                ->where('id_estudio', $id_estudio)
                ->exists();

            if (!$capturado) {
                EstudiosRealizadosAlumnos::create($dataEstudio);
                $opciones[0] = $id_estudio;
            }
        }

        return view('elements.renglonEstudios', ['renglon' => $opciones]);
    }
}