<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('/layouts/home');

});

Route::view('/busquedaEscuelas/busqueda', 'layouts.busquedaEscuelas.busqueda')->name('escuelas.busqueda');
