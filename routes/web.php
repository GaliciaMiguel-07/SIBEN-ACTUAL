<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// ─── Rutas públicas ──────────────────────────────────────────────────────────

// Página principal pública (no requiere login)
Route::get('/', fn() => view('layouts.home'))->name('home');

// Login / logout
Route::get ('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login',  [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Imágenes en resources/images/ (fuera de public/) — asset() no las alcanza
Route::get('/images/{filename}', function (string $filename) {
    $path = resource_path('images/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return Response::file($path);
})->where('filename', '[a-zA-Z0-9._\-]+')->name('imagen');

Route::view('/busquedaEscuelas/busqueda', 'layouts.busquedaEscuelas.busqueda')
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

