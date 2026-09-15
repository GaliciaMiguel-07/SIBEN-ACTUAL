<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class usuariosController extends Controller
{
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
