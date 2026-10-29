<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\MensajeDestino;
use App\Models\Escuela2;
use App\Models\Usuario;
use App\Models\Entidad;
use Illuminate\Http\Request;

class MensajesController extends sibenController
{

/**
     * index
     * Muestra la lista de mensajes activos y los destinatarios
     */
    public function index()
    {
        $request = request(); // Si necesitas el request aquí dentro
        
        $ids_mensajes = MensajeDestino::orderBy('modulo')
            ->orderBy('id_tipo')
            ->orderBy('fecha_alta')
            ->pluck('id_mensaje', 'id_mensaje');

        $mensajes = Mensaje::whereIn('id', $ids_mensajes)
            ->orderBy('id', 'desc')
            ->get();

        $tipos_mensajes = [
            '1' => 'Nivel',
            '2' => 'Escuela',
            '3' => 'Usuario',
            '4' => 'Entidad',
        ];

        $modulos = [
            'l' => 'Licenciatura',
            'p' => 'Posgrado'
        ];

        $url = route('mensajes.agrega');
        $parametros = "";
        $evento = "consultaAjax('$url', '$parametros', muestraOverlay, ocultaOcupado)";

        return view('mensajes.index', compact('mensajes', 'tipos_mensajes', 'modulos', 'evento'));
    }

    /**
     * agrega (alta)
     * Creamos mensajes y asignamos nivel
     */
    public function agrega(Request $request)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        if ($nivel != 1) {
            return redirect()->route('logout');
        }

        $tipos_mensajes = [
            '1' => 'Nivel',
            '2' => 'Escuela',
            '3' => 'Usuario',
            '4' => 'Entidad',
        ];

        return view('mensajes.alta', compact('tipos_mensajes'));
    }

    /**
     * guarda
     * Método complemento para guardar los mensajes a través de una llamada AJAX
     */
    public function guarda(Request $request)
    {
        $mensajeData = $request->input('Mensaje');
        $mensajeDestinoData = $request->input('MensajeDestino');

        $renglon = [];
        if (!empty($mensajeData)) {
            $mensaje = Mensaje::create($mensajeData);
            $id_mensaje = $mensaje->id;

            $mensajeDestinoData['id_mensaje'] = $id_mensaje;
            $mensajeDestinoData['status'] = 'a';
            MensajeDestino::create($mensajeDestinoData);

            $renglon = [
                $mensaje->id,
                $mensajeData['mensaje'] ?? '',
                $mensajeDestinoData['modulo'] ?? '',
                $mensajeDestinoData['id_tipo'] ?? '',
                $mensajeDestinoData['id_destino'] ?? '',
                $mensajeData['fecha_alta'] ?? now(),
                ''
            ];
        }

        return view('elements.renglon', compact('renglon'));
    }

    /**
     * baja
     * Para eliminar mensajes que ya no sean necesarios
     */
    public function baja(Request $request, $id_mensaje = null)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        if ($nivel != 1) {
            return redirect()->route('logout');
        }

        $mensaje = Mensaje::with('destinos')->find($id_mensaje);

        if ($mensaje) {
            foreach ($mensaje->destinos as $destino) {
                if ($destino->status === 'x') { // si es no activo, se elimina
                    $destino->delete();
                }
            }

            // Recargamos la relación para verificar si se eliminaron todos los destinos
            $mensaje->load('destinos');
            if ($mensaje->destinos->isEmpty()) {
                $mensaje->delete();
            }
        }

        return redirect()->route('mensajes.index');
    }

    /**
     * status
     * Activamos o desactivamos un mensaje
     */
    public function status(Request $request, $id_mensaje_destino = null, $status = null)
    {
        if (!$id_mensaje_destino) {
            return redirect()->back();
        }

        if (!$status) {
            $status = 'x';
        }

        $destino = MensajeDestino::find($id_mensaje_destino);
        if ($destino) {
            $destino->update(['status' => $status]);
        }

        return redirect()->back();
    }

    /**
     * destinos
     * Rellenamos la lista de destinos de acuerdo al tipo elegido (AJAX)
     */
    public function destinos(Request $request)
    {
        $modulo = 'l'; // por ahora sólo es para licenciaturas
        $id_tipo = $request->input('MensajeDestino.id_tipo');

        $opciones = [];

        switch ($id_tipo) {
            case 1: // por niveles
                $opciones = [
                    '1' => 'Admins',
                    '2' => 'Enlace',
                    '3' => 'Sup. Normales',
                    '4' => 'Capturista',
                    '5' => 'Consulta'
                ];
                break;
            case 2: // por escuela
                $opciones = Escuela2::whereIn('unica', ['0', '1'])
                    ->where('modulo', $modulo)
                    ->where('status', 'a')
                    ->orderBy('escuela_key')
                    ->pluck('escuela_key', 'id');
                break;
            case 3: // Por usuario
                $opciones = Usuario::where('modulo', $modulo)
                    ->where('status', 'a')
                    ->orderBy('nivel')
                    ->orderBy('id_usuario')
                    ->pluck('id_usuario', 'id');
                break;
            case 4: // Por entidad
                $opciones = Entidad::where('id', '<', 33)
                    ->orderBy('entidad')
                    ->pluck('entidad', 'id');
                break;
            default:
                break;
        }

        return view('elements.opciones', ['opciones' => $opciones]);
    }
}