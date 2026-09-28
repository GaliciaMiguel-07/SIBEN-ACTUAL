<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckNivel
{
    public function handle(Request $request, Closure $next, int ...$nivelesPermitidos): Response
    {
        $usuario = Auth::user();

        if (!$usuario || !in_array($usuario->nivel, $nivelesPermitidos)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}