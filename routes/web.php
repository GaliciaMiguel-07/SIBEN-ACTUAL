<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usuariosController;
use App\Http\Controllers\sibenController;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('/layouts/home');
});

Route::view('/busquedaEscuelas/busqueda', 'layouts.busquedaEscuelas.busqueda')
    ->name('escuelas.busqueda');

Route::view('/pages/masinformacion', 'layouts.pages.masinformacion')
    ->name('pages.masinformacion');

Route::get('/usuarios/login', [usuariosController::class, 'login'])
    ->name('usuarios.login');

Route::post('/usuarios/login', [usuariosController::class, 'authenticate'])
    ->name('usuarios.authenticate');

Route::view('/pages/estadisticasrecientes', 'layouts.pages.estadisticasrecientes')
    ->name('pages.estadisticasrecientes');

Route::get('/siben',[sibenController::class,'index'])->name('siben');

Route::resource('usuarios', usuariosController::class);

Route::get('/images/{filename}', function ($filename) {
    $path = resource_path('images/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return Response::file($path);
});