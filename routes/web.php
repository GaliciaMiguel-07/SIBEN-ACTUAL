<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\sibenController;
use App\Http\Controllers\BusquedaEscuelasController;

// ─── Rutas públicas ──────────────────────────────────────────────────────────

// Página principal pública (no requiere login)
Route::get('/', fn() => view('layouts.home'))->name('home');

// Login / logout
Route::get ('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login',  [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── 2FA ─────────────────────────────────────────────────────────────────────
// Verificación del código (usuario aún NO autenticado)
Route::get ('/2fa/verificar', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verificar');
Route::post('/2fa/verificar', [TwoFactorController::class, 'verify'])->name('2fa.verificar.post');

// Configuración (requiere sesión activa)
Route::middleware('auth')->group(function () {
    Route::get ('/2fa/configurar', [TwoFactorController::class, 'showSetup'])->name('2fa.configurar');
    Route::post('/2fa/configurar', [TwoFactorController::class, 'enable'])->name('2fa.configurar.post');
    Route::post('/2fa/desactivar', [TwoFactorController::class, 'disable'])->name('2fa.desactivar');
});


// ─── Rutas Públicas (Sin inicio de sesión) ───────────────────────────────────
Route::prefix('siben/busqueda-escuelas')->group(function () {
    Route::get('/', [BusquedaEscuelasController::class, 'index'])->name('siben.busqueda_escuelas.index');
    Route::get('/busqueda', [BusquedaEscuelasController::class, 'busqueda'])->name('siben.busqueda_escuelas.busqueda');
    Route::match(['get', 'post'], '/resultado', [BusquedaEscuelasController::class, 'resultadoBusqueda'])->name('siben.busqueda_escuelas.resultado');
    Route::match(['get', 'post'], '/detalles/{id_escuela?}', [BusquedaEscuelasController::class, 'detallesEscuela'])->name('siben.busqueda_escuelas.detalles');
});


// Imágenes en resources/images/ (fuera de public/) — asset() no las alcanza
Route::get('/images/{filename}', function (string $filename) {
    $path = resource_path('images/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return Response::file($path);
})->where('filename', '[a-zA-Z0-9._\-]+')->name('imagen');

Route::get('/busquedaEscuelas/busqueda', [BusquedaEscuelasController::class, 'busqueda'])
    ->name('escuelas.busqueda');

Route::view('/pages/masinformacion', 'layouts.pages.masinformacion')
    ->name('pages.masinformacion');
    
Route::view('/pages/estadisticasrecientes', 'layouts.pages.estadisticasrecientes')
    ->name('pages.estadisticasrecientes');

// ─── Rutas protegidas (requieren sesión activa) ───────────────────────────────

Route::middleware('auth')->group(function () {

    // Dashboard principal — carga siben.index con mensajes del sistema
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin — gestión de usuarios (solo nivel 1; el middleware lo verifica el controller)
    Route::prefix('admin')->name('admin.')->middleware('nivel:1')->group(function () {

        Route::get   ('/usuarios',                  [UsuarioController::class, 'index'])
             ->name('usuarios.index');
        Route::get   ('/usuarios/crear',            [UsuarioController::class, 'create'])
             ->name('usuarios.create');
        Route::post  ('/usuarios',                  [UsuarioController::class, 'store'])
             ->name('usuarios.store');
        Route::get   ('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])
             ->name('usuarios.edit');
        Route::put   ('/usuarios/{usuario}',        [UsuarioController::class, 'update'])
             ->name('usuarios.update');
        Route::patch ('/usuarios/{usuario}/status', [UsuarioController::class, 'toggleStatus'])
             ->name('usuarios.toggle');

    });

});

