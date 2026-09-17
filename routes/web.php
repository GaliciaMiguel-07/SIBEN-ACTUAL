<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usuariosController;

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

Route::resource('usuarios', usuariosController::class);