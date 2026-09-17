<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class usuariosController extends Controller
{
    /**
     * Mostrar formulario de inicio de sesión
     */
    public function login()
    {
        return view('layouts.usuarios.login');
    }

    /**
     * Proceso de inicio de sesión 
     */
    public function authenticate(Request $request)
    {
        $id_usuario = $request->input('id_usuario');
        $contrasenia = $request->input('contrasenia');

        $usuario = User::where('id_usuario', $id_usuario)->first();

        if (!$usuario) {
            return back()->with('error', 'Usuario o contraseña incorrectos.');
        }

        dd($usuario);
    }

    /**
     * Lista datos
     */
    public function index()
    {
        return view('layouts.usuarios.login');
    }

    /**
     * Muestra un formulario
     */
    public function create()
    {
        return view('layouts.usuarios.create');
    }

    /**
     * Recibe y guarda datos de formulario
     */
    public function store(Request $request)
    {
        return redirect()->route('usuarios.login');
    }

}
