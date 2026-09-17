<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escuela;

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
            '1' => 'Nivel',
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
            'nivel', 'estructura', 'cuentaDirector', 'cuentaDomicilio', 'archivos'
        ));
    }
    public function archivos(Request $request){
        $anterior = false;
        $clave = null;
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
                    $id_escuela = session('Usuario.id_escuela');
                    $escuela = Escuela::select('escuela_key')->find($id_escuela);
                    $clave = $escuela ? $escuela->escuela_key : null;
                    break;
            }
        }
        return view('siben.archivos', compact('clave', 'anterior'));
    }
    public function listados(){
        $id_escuela = session('Usuario.id_escuela');
        $modulo     = session('Usuario.modulo');
        $escuelaStatus = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();
        $oficializar = $escuelaStatus ? $escuelaStatus->oficializar : 0;

        $nivel         = session('Usuario.nivel');
        $captura       = session('Usuario.captura');
        $nombreEntidad = session('Usuario.id_renapo');

        $opcionesMenu  = [];
        $opcionesMenu2 = [];

        if ($nivel == 3){
            $opcionesMenu[] = ['siben', 'Ver Avance', 'avanceEscuela', 0];
        }

        $opcionesMenu[] = ['informes', 'Listado Alumnos', 'listadoAlumnos', 1];
        $opcionesMenu[] = ['informes', 'Estadística Alumnos', 'estadisticaAlumnos', 1];
        $opcionesMenu[] = ['informes', 'Listado Docentes', 'listadoDocentes', 1];
        $opcionesMenu[] = ['informes', 'Estadística Docentes', 'estadisticaDocentes', 1];
        $opcionesMenu[] = ['informes', 'Listado Personal', 'listadoPersonal', 1];
        $opcionesMenu[] = ['informes', 'Estadística Personal', 'estadisticaPersonal', 1];

        $opcionesMenu2[] = ['informes', 'Datos Escuela', 'datosEscuela', 1];
        $opcionesMenu2[] = ['informes', 'Contrataciones Docente', 'listadoContra', 1];
        $opcionesMenu2[] = ['informes', 'Asignaturas Docente', 'listadoAsignatura', 1];
        $opcionesMenu2[] = ['informes', 'Alumnos No egresados', 'listadoEgresados', 1];

        if ($nivel == 3 && $nombreEntidad == 'GT'){
            $opcionesMenu[] = ['siben', 'Finalizar Captura', 'finalizaCaptura', 0];
        }
        if ($nivel == 3 && $captura){
            $opcionesMenu[] = ['siben', 'Finalizar Captura', 'finalizaCaptura', 0];
        }
        if ($nivel == 3 && $captura == 0){
            $opcionesMenu[] = ['siben', 'Oficializar Informaci&oacute;n', 'oficializarInformacion', 0, null ,$oficializar];
        }
        return view('siben.listados', compact('opcionesMenu', 'opcionesMenu2'));
    }
    public function datosEscuela(){
        $modulo        = session('Usuario.modulo');
        $nivel         = session('Usuario.nivel');
        $nombreEscuela = session('Usuario.escuela');
        $id_escuela    = session('Usuario.id_escuela');

        
        if ($modulo != 'l') {
        return redirect('/siben');
        }

        $escuela  = Escuela::find($id_escuela);
        $telefono = $escuela ? $escuela->telefono1 : '';

        $title          = 'Datos Adicionales, ' . $nombreEscuela;
        $turnos         = ['m' => 'Matutino', 'v' => 'Vespertino', 'd' => 'Discontinuo', 'n' => 'Nocturno'];
        $sostenimientos = ['p' => 'Público', 'q' => 'Particular'];

        $urls['escuelas'] = url('/siben/editaAdicionales');
        $textos['escuela']           = 'Editar';
        $parametros['escuela']       = "data[Escuela][id]={$id_escuela}";
        $eventos['escuela']          = "consultaAjax('{$urls['escuela']}', '{$parametros['escuela']}', muestraOverlay, muestraOcupado)";
	    $evento_domicilio = "locacion(\'".url('/siben')."')";
        $botones;
        switch ($nivel){
            case 3:
                $botones['escuela'] = "<input id='e_{$id_escuela}' type='button' value='{$textos['escuela']}' onclick=\"{$eventos['escuela']}\" />";
                $botones['terminar'] = "<input type='button' value='Terminar' onclick=\"{$eventos_domicilio}\" />";
                break;
        }
        $anterior = true;
        return view('siben.datos_escuela', compact(
            'title',
            'escuela',
            'telefono',
            'turnos',
            'sostenimientos',
            'botones',
            'anterior'
        ));
    }
    public function editaEscuela(){
        
    }
}