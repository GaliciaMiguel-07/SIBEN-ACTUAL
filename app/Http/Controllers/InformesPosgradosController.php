<?php
namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Persona;
use App\Models\AlumnosPosgrado;
use App\Models\Posgrado;
use App\Models\Escuela;
use App\Models\EscuelaStatus;
use App\Models\Usuario;
use App\Models\DocentesPosgrado;
use App\Models\Estudios;
use App\Models\PosgradosDocentes;
use App\Models\Entidad;
use Illuminate\Http\Request;

class InformesPosgradosController extends sibenController {
    public function index (){
        return view('informes_posgrado.index');
    }
    /**
     * Listados para niveles 3 y 4
     */
    public function listados(){
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        
    }
}