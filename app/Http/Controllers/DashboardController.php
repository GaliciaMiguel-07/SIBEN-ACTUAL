<?php
namespace App\Http\Controllers;

use App\Models\Mensaje;
use Illuminate\View\View;

class DashboardController extends sibenController
{
    public function index(): View
    {
        $usuario = auth()->user();
        $nivel   = (int) $usuario->nivel;

        // Obtener todos los mensajes activos del sistema.
        // La tabla `mensajes` tiene las columnas: id, mensaje, fecha_alta
        $mensajes = Mensaje::orderBy('fecha_alta', 'desc')->get();

        return view('siben.index', compact('mensajes', 'nivel'));
    }
}
