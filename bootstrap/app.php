<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerificarSesion;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verificar.sesion' => VerificarSesion::class,
            'no.cache'         => NoCacheHeaders::class,
            'nivel'            => \App\Http\Middleware\CheckNivel::class,
            // CheckModulo y CheckCaptura no existen aún — comentados hasta implementarlos
            // 'modulo'  => \App\Http\Middleware\CheckModulo::class,
            // 'captura' => \App\Http\Middleware\CheckCaptura::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
