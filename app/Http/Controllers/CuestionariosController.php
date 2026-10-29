<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\EscuelaStatus;
use Illuminate\Http\Request;

class CuestionariosController extends sibenController
{
    /**
     * index
     * Mostramos un resumen de las respuestas del cuestionario
     */
    public function index($id_escuela = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        if ($nivel != 3) {
            return redirect()->route('siben.index');
        }

        if (!$id_escuela) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        }

        $preguntas = Pregunta::all();
        
        $respuestas = Respuesta::where('id_escuela', $id_escuela)
            ->orderBy('id_pregunta')
            ->get();

        $sino = ['11' => 'No', '10' => 'Si'];

        return view('cuestionarios.index', compact('preguntas', 'respuestas', 'sino'));
    }

    /**
     * cuestionario
     * Nos muestra el formulario mismo para ingresar las respuestas
     */
    public function cuestionario(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        $preguntas = Pregunta::all();
        $sino = ['10' => 'Si', '11' => 'No'];

        $status = EscuelaStatus::where('modulo', $modulo)
            ->where('id_escuela', $id_escuela)
            ->first();

        $cantidad_alumnos = $status->cantidad_alumnos ?? null;
        $cantidad_personal = $status->cantidad_personal ?? null;

        $data = [];

        if ($request->isMethod('post') && !empty($request->all())) {
            // Para poder editar, borramos las respuestas que antes existieran
            Respuesta::where('id_escuela', $id_escuela)->delete();

            // Luego de hacer el borrado, procedemos al guardado de nuevo
            $respuestasInput = $request->input('Respuesta', []);
            foreach ($respuestasInput as $id_pregunta => $valor_respuesta) {
                if (isset($valor_respuesta)) {
                    Respuesta::create([
                        'id_escuela'  => $id_escuela,
                        'id_pregunta' => $id_pregunta,
                        'respuesta'   => $valor_respuesta,
                    ]);
                }
            }

            // Actualizamos el valor de estructura
            if ($status) {
                $status->estructura = 1;
                $status->save();
            }

            return redirect('/cuestionarios');
        } else {
            // Si no es POST y existen respuestas en la base, las cargamos
            $data = $this->respuestas($id_escuela);

            // Los datos más recientes de los parámetros
            $data['Respuesta'][1] = $cantidad_alumnos;
            $data['Respuesta'][8] = $cantidad_personal;
        }

        // Uso esta "característica" para ocultar las secciones de ser necesario
        $argumentos = [
            ['Respuesta1', 'estudiante', '1', '1'],
            ['Respuesta9', 'profesor', '1', '1'],
        ];

        $ocultarDatos = true;

        return view('cuestionarios.cuestionario', compact('preguntas', 'sino', 'argumentos', 'ocultarDatos', 'data'));
    }

    /**
     * respuestas
     * Regresa en un arreglo las respuestas acomodadas
     * de manera que el formulario las cargue automágicamente
     */
    public function respuestas($id_escuela = null)
    {
        $ctx = $this->contexto();
        if (!$id_escuela) {
            $id_escuela = $ctx['id_escuela'] ?? null;
        }

        $respuestas = Respuesta::where('id_escuela', $id_escuela)->get();

        $arreglo_respuestas = [];
        foreach ($respuestas as $respuesta) {
            $id_pregunta = $respuesta->id_pregunta;
            $arreglo_respuestas['Respuesta'][$id_pregunta] = $respuesta->respuesta;
        }

        return $arreglo_respuestas;
    }
}