<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Panel de administración (nivel 1).
     * Equivale a AdminController::index() del sistema viejo, que armaba
     * el arreglo $menu con las opciones de Alumnos y Personal.
     */
    public function index(): View
    {
        $menu = [
            'Alumnos' => [
                ['Alta',    'admin.alumnos.alta'],
                ['Edición', 'admin.alumnos.buscar'],
            ],
            'Personal' => [
                ['Alta',            'admin.personal.alta'],
                ['Edición/Activar', 'admin.personal.buscar'],
                ['Jubilar',         'admin.personal.jubilar'],
                ['Jubilados',       'admin.personal.jubilados'],
            ],
        ];

        return view('admin.index', compact('menu'));
    }
}