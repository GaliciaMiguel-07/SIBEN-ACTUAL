<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\sibenController;
use App\Http\Controllers\BusquedaEscuelasController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AlumnosController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\InformesController;

// ─── Rutas públicas ──────────────────────────────────────────────────────────

// Página principal pública (no requiere login)
Route::get('/', fn() => view('layouts.home'))->name('home');

// Login
Route::get ('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login',  [LoginController::class, 'login']);

// ─── 2FA (público con guardia interna) ───────────────────────────────────────
// El usuario aún NO está autenticado cuando llega aquí
Route::get ('/2fa/verificar', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verificar');
Route::post('/2fa/verificar', [TwoFactorController::class, 'verify'])->name('2fa.verificar.post');

// Configuración 2FA: accesible con sesión pendiente o sesión autenticada
// La guardia fina está en TwoFactorController@showSetup y @enable
Route::get ('/2fa/configurar', [TwoFactorController::class, 'showSetup'])->name('2fa.configurar');
Route::post('/2fa/configurar', [TwoFactorController::class, 'enable'])->name('2fa.configurar.post');


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

    Route::get('/busquedaEscuelas/resultado', [BusquedaEscuelasController::class, 'resultado'])
    ->name('escuelas.resultado');

Route::view('/pages/masinformacion', 'layouts.pages.masinformacion')
    ->name('pages.masinformacion');

Route::view('/pages/estadisticasrecientes', 'layouts.pages.estadisticasrecientes')
    ->name('pages.estadisticasrecientes');

// ─── Rutas protegidas (requieren sesión activa) ───────────────────────────────

Route::middleware(['auth', 'no.cache'])->group(function () {

    // Logout protegido por sesión + CSRF
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('siben.restricciones')->name('dashboard');

    // ── Núcleo SIBEN y módulos migrados ─────────────────────────────────────
    // El middleware reproduce las restricciones heredadas para Licenciaturas
    // niveles 3/4. Se limita a estas rutas; no afecta administración ni 2FA.
    Route::middleware('siben.restricciones')->group(function () {
        Route::prefix('siben')->name('siben.')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('index');
            Route::get('/archivos', [DashboardController::class, 'archivos'])->name('archivos');
            Route::get('/datos-generales', [DashboardController::class, 'menuDatos'])->name('menu_datos');
            Route::get('/listados', [DashboardController::class, 'listados'])->name('listados');
            Route::get('/parametros', [DashboardController::class, 'parametros'])->name('parametros');
            Route::get('/captura', [DashboardController::class, 'captura'])->name('captura');
            Route::get('/usuarios', [DashboardController::class, 'listaUsuarios'])->name('lista_usuarios');
            Route::get('/prueba-verhoeff', [DashboardController::class, 'pruebaVerhoeff'])->name('prueba_verhoeff');
            Route::get('/avance-alumnos', [DashboardController::class, 'avanceAlumnos'])->name('avance_alumnos');
            Route::get('/avance-personal', [DashboardController::class, 'avancePersonal'])->name('avance_personal');
            Route::get('/supervisores', [DashboardController::class, 'supervisores'])->name('supervisores');
            Route::get('/directores', [DashboardController::class, 'directores'])->name('directores');
            Route::get('/editar-usuario', [DashboardController::class, 'editaUsuario'])->name('edita_usuario');
        });

        Route::get('/alumnos', [AlumnosController::class, 'index'])->name('alumnos.index');
        Route::get('/personal', [PersonalController::class, 'menu'])->name('personal.menu');
        Route::get('/informes/estadisticas', [InformesController::class, 'estadisticas'])->name('informes.estadisticas');
        Route::get('/informes/informe-nacional', [InformesController::class, 'informeNacional'])->name('informes.informe_nacional');
        Route::get('/informes/directorio', [InformesController::class, 'directorio'])->name('informes.directorio');
    });

    // Desactivar 2FA (bloqueado por política, requiere sesión para mostrar mensaje en flujo normal)
    Route::post('/2fa/desactivar', [TwoFactorController::class, 'disable'])->name('2fa.desactivar');

    // ── Stop impersonating ────────────────────────────────────────────────────
    // FUERA del grupo nivel:1 porque la ejecuta el usuario suplantado (nivel 2, 3, 4...)
    // Solo requiere tener sesión activa. El controlador verifica internamente
    // que haya una suplantación activa antes de hacer cualquier cosa.
    Route::post('/admin/stop-impersonating', [TwoFactorController::class, 'stopImpersonating'])
         ->name('admin.stop.impersonating');

    // ── Admin (solo nivel 1) ──────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('nivel:1')->group(function () {

        // Panel de administración (nivel 1)
        Route::get   ('/',                          [AdminController::class, 'index'])
             ->name('index');
        Route::get('/alumnos/alta', [AdminController::class, 'agregaAlumno'])
             ->name('alumnos.alta');
        Route::get('/alumnos/escuelas', [AdminController::class, 'listaEscuelasAlumno'])
             ->name('alumnos.escuelas');
        Route::get('/alumnos/oferta', [AdminController::class, 'listaOfertaAlumno'])
             ->name('alumnos.oferta');
        Route::get   ('/personal/alta',             [AdminController::class, 'agregaPersonal'])
             ->name('personal.alta');
        Route::get   ('/personal/escuelas',         [AdminController::class, 'listaEscuelas'])
             ->name('personal.escuelas');
        Route::post  ('/personal/guardar',          [AdminController::class, 'guardaAgregacion'])
             ->name('personal.guardar');
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
        Route::post  ('/personal/guardar',          [AdminController::class, 'guardaAgregacion'])
             ->name('personal.guardar');
        Route::get   ('/personal/busqueda',      [AdminController::class, 'busquedaPersonal'])
             ->name('personal.busqueda');
        Route::get   ('/personal/lista',         [AdminController::class, 'listaPersonal'])
             ->name('personal.lista');
        Route::post  ('/personal/status',        [AdminController::class, 'cambiaStatus'])
             ->name('personal.status');
        Route::get   ('/personal/editar/{id}',   [AdminController::class, 'editaPersonal'])
             ->name('personal.editar');
        Route::post  ('/personal/actualizar',    [AdminController::class, 'guardaEdicion'])
             ->name('personal.actualizar');
        // Solo el admin puede INICIAR una suplantación
        Route::post('/impersonate/{usuarioId}', [TwoFactorController::class, 'impersonate'])
             ->name('impersonate');

    });

});