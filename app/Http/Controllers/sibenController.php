<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Escuela;

class sibenController extends Controller
{
    public function index(){
        $captura          = Session('Usuario.captura');
        $nivel            = Session('Usuario.nivel');
        $cuenta_clave     = Session('Usuario.cuenta_clave');
        $estructura       = Session('Usuario.estructura');
	    $modulo           = Session('Usuario.modulo');
	    $cuentaDirector   = Session('Usuario.cuenta_director');
        $cuentaDomicilio  = Session('Usuario.cuenta_domicilio');
	    $id_escuela       = Session('Usuario.id_escuela');
        $id_usuario       = Session('Usuario.id');

    }
}
