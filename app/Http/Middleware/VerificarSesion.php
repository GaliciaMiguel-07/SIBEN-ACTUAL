<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarSesion
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('Usuario')) {
            return redirect()->route('usuarios.login');
        }

        return $next($request);
    }
}