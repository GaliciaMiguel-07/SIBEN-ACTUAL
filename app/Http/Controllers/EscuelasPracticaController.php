<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Entidad;
use App\Models\EscuelaPractica;
use Illuminate\Http\Request;

class EscuelasPracticaController extends sibenController
{
    /**
     * Método auxiliar que reemplaza el comportamiento del beforeFilter original
     */
    private function verificarCaptura()
    {
        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;
        
        // Obtenemos la acción actual de la ruta
        $action = request()->route() ? request()->route()->getActionMethod() : '';
        $accionesPermitidas = ['index'];

        if (!$captura && !in_array($action, $accionesPermitidas)) {
            return redirect('/alumnos');
        }
        
        return null;
    }

    /**
     * index
     */
    public function index()
    {
        return view('escuelas_practica.index');
    }

    /**
     * buscarEscuelas
     */
    public function buscarEscuelas()
    {
        if ($redirect = $this->verificarCaptura()) {
            return $redirect;
        }

        $ctx = $this->contexto();
        $id_entidad = $ctx['id_entidad'] ?? null;

        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');
        $municipios = Municipio::where('id_entidad', $id_entidad)->pluck('municipio', 'id');

        $anterior = '/alumnos';

        return view('escuelas_practica.buscar_escuelas', compact('entidades', 'municipios', 'id_entidad', 'anterior'));
    }

    /**
     * listaEscuelas (Respuesta AJAX)
     */
    public function listaEscuelas(Request $request)
    {
        if ($redirect = $this->verificarCaptura()) {
            return $redirect;
        }

        $buscar = $request->input('Buscar', []);
        $condiciones = array_filter($buscar, fn($val) => !empty($val));

        $escuelasPractica = EscuelaPractica::where($condiciones)
            ->select('id', 'clave_ct', 'escuela_practica', 'id_entidad')
            ->get();

        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');

        $url = route('escuelasPractica.actualizarEscuela');
        $infoEscuela = [];

        foreach ($escuelasPractica as $escuela) {
            $parametros = "data[Buscar][id]=" . $escuela->id;
            $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado);";
            $boton = '<input id="b_' . $escuela->id . '" type="button" value="Editar" onclick="' . $evento . '" />';

            $entidadNombre = $entidades[$escuela->id_entidad] ?? '';

            $infoEscuela[$escuela->id] = [
                'clave_ct'         => $escuela->clave_ct,
                'escuela_practica' => $escuela->escuela_practica,
                'entidad'          => $entidadNombre,
                'accion'           => $boton,
            ];
        }

        $cabeceras = ['No', 'Clave CT', 'Escuela', 'Entidad', 'Acción'];

        return view('elements.tablaAjax', [
            'renglones' => $infoEscuela,
            'cabeceras' => $cabeceras
        ]);
    }

    /**
     * agregarEscuelas
     */
    public function agregarEscuelas(Request $request)
    {
        if ($redirect = $this->verificarCaptura()) {
            return $redirect;
        }

        $ctx = $this->contexto();
        $id_entidad = $ctx['id_entidad'] ?? null;
        $nivel = $ctx['nivel'] ?? null;

        if ($nivel != 4) {
            return redirect('/alumno');
        }

        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');
        $municipios = Municipio::where('id_entidad', $id_entidad)->pluck('municipio', 'id');

        $escuelaInput = $request->input('EscuelaPractica');

        if ($request->isMethod('post') && !empty($escuelaInput)) {
            $claveCt = $escuelaInput['clave_ct'] ?? null;
            $escuelaExiste = EscuelaPractica::where('clave_ct', $claveCt)->exists();

            if ($escuelaExiste) {
                return response('La escuela de práctica ya existe.', 400);
            } else {
                EscuelaPractica::create($escuelaInput);
            }
        }

        $anterior = '/alumnos';

        return view('escuelas_practica.agregar_escuelas', compact('entidades', 'municipios', 'id_entidad', 'anterior'));
    }

    /**
     * actualizarEscuela
     */
    public function actualizarEscuela(Request $request)
    {
        if ($redirect = $this->verificarCaptura()) {
            return $redirect;
        }

        $ctx = $this->contexto();
        $id_entidad = $ctx['id_entidad'] ?? null;

        $id_escuela_practica = $request->input('Buscar.id');

        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');
        $municipios = Municipio::where('id_entidad', $id_entidad)->pluck('municipio', 'id');

        $escPractica = EscuelaPractica::find($id_escuela_practica);

        $escuelaInput = $request->input('EscuelaPractica');

        if ($request->isMethod('post') && !empty($escuelaInput)) {
            $id = $escuelaInput['id'] ?? null;
            $claveCt = $escuelaInput['clave_ct'] ?? null;

            $escuelaExiste = EscuelaPractica::where('clave_ct', $claveCt)
                ->where('id', '!=', $id)
                ->exists();

            if ($escuelaExiste) {
                return response('La escuela de práctica ya existe.', 400);
            } else {
                if ($id) {
                    $escuelaObj = EscuelaPractica::find($id);
                    if ($escuelaObj) {
                        $escuelaObj->update($escuelaInput);
                    }
                }
            }
        }

        return view('escuelas_practica.actualizar_escuela', compact('entidades', 'municipios', 'escPractica'));
    }

    /**
     * municipios (Para peticiones AJAX)
     */
    public function municipios(Request $request)
    {
        if ($redirect = $this->verificarCaptura()) {
            return $redirect;
        }

        $id_entidad = $request->input('Buscar.id_entidad');

        $municipios = Municipio::where('id_entidad', $id_entidad)
            ->orderBy('municipio')
            ->pluck('municipio', 'id');

        return view('elements.opciones', ['opciones' => $municipios]);
    }
}