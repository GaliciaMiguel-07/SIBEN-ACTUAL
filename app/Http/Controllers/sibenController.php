<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Escuela;

class sibenController extends Controller
{
    public function index(){
        $captura          = session('Usuario.captura');
        $nivel            = session('Usuario.nivel');
        $cuenta_clave     = session('Usuario.cuenta_clave');
        $estructura       = session('Usuario.estructura');
	    $modulo           = session('Usuario.modulo');
	    $cuentaDirector   = session('Usuario.cuenta_director');
        $cuentaDomicilio  = session('Usuario.cuenta_domicilio');
	    $id_escuela       = session('Usuario.id_escuela');
        $id_usuario       = session('Usuario.id');

        $escuela = Escuela::select('telefono1')->find($id_escuela);
        $telefono = $escuela ? $escuela->telefono1 :'';

        $mensajeCaptura = null;

        if($nivel >=3 && $nivel<=4){
            if($captura == 0){
                $mensajeCaptura = 'La captura está finalizada';
            }elseif($captura == 2){
                $mensajeCaptura = 'Demasiadas conexiones, por favor intente más tarde';
            }elseif($estructura == 0 && $modulo == 1){
                $mensajeCaptura = 'Por favor, actualice los Datos Generales';
            }elseif($cuenta_clave>0){
                $mensajeCaptura = 'Por favor, ingrese sus claves de centro de trabajo';
            }elseif($cuentaDirector == 0 && $modulo == 'l'){
                $mensajeCaptura = 'Por favor agregue o active al director de la escuela';
            }elseif($telefono == '' && $modulo == 'l'){
                $mensajeCaptura = 'Por favor actualice la información adicional(telefono,correo)';
            }
        }
        
        $id_usuario = session('Usuario.id');
        $id_entidad = session('Usuario.id_entidad');

        $mensajes = array_merge(
            $this->getMensajes(['modulo' => $modulo, 'status' => 'a', 'id_tipo' => '1', 'id_destino' => $nivel]),
            $this->getMensajes(['modulo' => $modulo, 'status' => 'a', 'id_tipo' => '3', 'id_destino' => $id_usuario])  
        );
        if ($id_escuela){
            $mensajes = array_merge($mensajes. $this->getMensajes(['modulo' => $modulo, 'status' => 'a', 'id_tipo' => '2', 'id_destino' => $id_escuela]));
        }
        if ($id_entidad){
            $mensajes = array_merge($mensajes. $this->getMensajes(['modulo' => $modulo, 'status' => 'a', 'id_tipo' => '4', 'id_destino' => $id_entidad]));
        }
        $tipos_mensajes = [
            '1'=> 'Nivel',
            '2' => 'Escuela',
            '3' => 'Usuario',
            '4' => 'Entidad',
        ];

        $archivos = false;
        if (in_array($nivel,[2,3,4])){
            $archivos = true;
        }

        return view('siben.index', compact(
            'mensajeCaptura', 'mensajes', 'telefono', 'tipos_materiales',
            'nivel', 'estructura', 'cuentaDirector', ' cuentaDomicilio', 'archivos'
        ));
    }
    public function archivos(Request $request){
        $anterior = false;
        $clave = nuell;
        if ($request->filled('Escuela.escuela_key') || $request->filled('escuela_key')){
            $clave = $request->input('Escuela.escuela_key', $request->input('escuela_key'));
            $anterior = true;
        } else {
            $nivel = session('Usuario.nivel');
            switch ($nivel) {
                case 2:
                    $clave = session('Usuario.nivel');
                    break;
                case 3:
                case 4:


            }
        }
    }
}
