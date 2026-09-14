<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('/layouts/home');

});

Route::view('/busquedaEscuelas/busqueda', 'layouts.busquedaEscuelas.busqueda')->name('escuelas.busqueda');
Route::view('/pages/masinformacion', 'layouts.pages.masinformacion')->name('pages.masinformacion');
Route::view('/usuarios/login', 'layouts.usuarios.login')->name('usuarios.login');