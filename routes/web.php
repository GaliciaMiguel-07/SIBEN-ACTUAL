<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usuariosController;

Route::get('/', function () {
    return view('/layouts/home');

});

Route::view('/busquedaEscuelas/busqueda', 'layouts.busquedaEscuelas.busqueda')->name('escuelas.busqueda');
Route::view('/pages/masinformacion', 'layouts.pages.masinformacion')->name('pages.masinformacion');
Route::view('/usuarios/login', 'layouts.usuarios.login')->name('usuarios.login');
Route::view('/pages/estadisticasrecientes', 'layouts.pages.estadisticasrecientes')->name('pages.estadisticasrecientes');
Route::resource('usuarios', usuariosController::class);
