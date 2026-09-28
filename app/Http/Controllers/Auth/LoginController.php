<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('layouts.usuarios.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'El usuario es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $usuario = Usuario::where('id_usuario', $request->login)
                          ->where('status', 'a')
                          ->first();

        if (!$usuario) {
            return back()->withErrors([
                'login' => 'Usuario no encontrado o inactivo.',
            ])->withInput();
        }

        $passwordCorrecto = false;
        $hashAlmacenado   = $usuario->contrasenia;

        // Detectar si el hash guardado ya es bcrypt o sigue siendo SHA1
        // Bcrypt siempre empieza con $2y$ o $2b$
        // SHA1 es una cadena hexadecimal de 40 caracteres
        $esBcrypt = str_starts_with($hashAlmacenado, '$2y$')
                 || str_starts_with($hashAlmacenado, '$2b$')
                 || str_starts_with($hashAlmacenado, '$argon');

        if ($esBcrypt) {
            // Hash::check() solo es seguro con hashes bcrypt/argon
            $passwordCorrecto = Hash::check($request->password, $hashAlmacenado);

        } else {
            // Hash SHA1 del sistema original (CakePHP 1.2)
            // NO se llama Hash::check() aquí — lanzaría RuntimeException
            if (sha1($request->password) === $hashAlmacenado) {
                $passwordCorrecto = true;

                // Migración transparente: guarda la contraseña en bcrypt
                // para que en el próximo login ya entre por la rama de arriba
                $usuario->timestamps = false;
                $usuario->forceFill(['contrasenia' => Hash::make($request->password)])->save();
            }
        }

        if (!$passwordCorrecto) {
            return back()->withErrors([
                'login' => 'La contraseña es incorrecta.',
            ])->withInput();
        }

        Auth::login($usuario, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}