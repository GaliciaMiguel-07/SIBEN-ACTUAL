<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escuela;
use App\Models\Mensaje;
use App\Models\MensajeDestino;

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
        $id_escuela = session('Usuario.id_escuela');
        $id_entidad = session('Usuario.id_entidad');

        $escuelaIdInput = $request->input('data.Escuela.id', $request->input('id'));
        
        if ($id_escuela != $escuelaIdInput){
            return redirect('/siben');
        }
        $domicilioEscuela = DomicilioEscuela::wehere('id_escuela', $id_escuela)->first();
        $escuela          = Escuela::find($id_escuela);
        $id_municipio     = $domicilioEscuela ? $domicilioEscuela->cve_mun : null;

        $municipios = Municipio::where('id_entidad', $id_entidad)->orderBy('municipio')->pluck('municipio', 'id_municipio');

        $locs = Localidad:: where('id_entidad', $id_entidad)->where('id_municipio', $id_municipio)->pluck('localidad', 'id_localidad');
        $turnos = ['m'=> 'Matutino', 'v' => 'Vespertino', 'd' => 'Discontinuo', 'n' => 'Nocturno'];

        $vialidad      = ['AMPLIACION'=>'AMPLIACION','ANDADOR'=>'ANDADOR','AVENIDA'=>'AVENIDA',
                          'BOULEVARD'=>'BOULEVARD','CALLE'=>'CALLE','CALLEJON'=>'CALLEJON',
                          'CALZADA'=>'CALZADA','CAMINO'=>'CAMINO','CARRETERA'=>'CARRETERA',
                          'CERRADA'=>'CERRADA','CIRCUITO'=>'CIRCUITO','CIRCUNVALACION'=>'CIRCUNVALACION',
                          'CONTINUACION'=>'CONTINUACION','CORREDOR'=>'CORREDOR','DIAGONAL'=>'DIAGONAL',
                          'EJE VIAL'=>'EJE VIAL','PASAJE'=>'PASAJE','PEATONAL'=>'PEATONAL',
                          'PERIFERICO'=>'PERIFERICO','PRIVADA'=>'PRIVADA','PROLONGACION'=>'PROLONGACION',
                          'RETORNO'=>'RETORNO','VIADUCTO'=>'VIADUCTO'];

        $asentamientos = [ 'AEROPUERTO'=>'AEROPUERTO', 'AMPLIACION'=>'AMPLIACION', 'BARRIO'=>'BARRIO',
                            'CANTON'=>'CANTON', 'CIUDAD'=>'CIUDAD', 'CIUDAD INDUSTRIAL'=>'CIUDAD INDUSTRIAL',
                            'COLONIA'=>'COLONIA', 'CONDOMINIO'=>'CONDOMINIO', 'CONJUNTO HABITACIONAL'=>'CONJUNTO HABITACIONAL',
                           'CORREDOR INDUSTRIAL'=>'CORREDOR INDUSTRIAL', 'COTO'=>'COTO', 'CUARTEL'=>'CUARTEL',
                            'EJIDO'=>'EJIDO', 'EXHACIENDA'=>'EXHACIENDA', 'FRACCION'=>'FRACCION',
                            'FRACCIONAMIENTO'=>'FRACCIONAMIENTO', 'GRANJA'=>'GRANJA', 'HACIENDA'=>'HACIENDA',
                            'INGENIO'=>'INGENIO', 'MANZANA'=>'MANZANA', 'PARAJE'=>'PARAJE', 'PARQUE INDUSTRIAL'=>'PARQUE INDUSTRIAL',
                            'PRIVADA'=>'PRIVADA', 'PROLONGACION'=>'PROLONGACION', 'PUEBLO'=>'PUEBLO', 'PUERTO'=>'PUERTO',
                            'RANCHERIA'=>'RANCHERIA', 'RANCHO'=>'RANCHO', 'REGION'=>'REGION', 'RESIDENCIAL'=>'RESIDENCIAL',
                            'RINCONADA'=>'RINCONADA', 'SECCION'=>'SECCION', 'SECTOR'=>'SECTOR', 'SUPERMANZANA'=>'SUPERMANZANA',
                            'UNIDAD'=>'UNIDAD', 'UNIDAD HABITACIONAL'=>'UNIDAD HABITACIONAL', 'VILLA'=>'VILLA',
                            'ZONA FEDERAL'=>'ZONA FEDERAL', 'ZONA INDUSTRIAL'=>'ZONA INDUSTRIAL', 'ZONA MILITAR'=>'ZONA MILITAR'];
        return view('siben.edita_escuela', compact(
            'escuela',
            'domicilioEscuela',
            'turnos',
            'municipios',
            'asentamientos',
            'locs',
            'vialidad',
            'seguridad_social',
            'admon',
            'admon2',
            'derecho',
            'margen'
        ));
    }

    public function editaEstructura(){
        $id_estructura = $request->input('data.CaracteristicaEscuela.id',$request->input('id'));

        $estructura = CaracteristicaEscuela::find($id_estructura);
        $tiposPropiedad  = construccion::where('id', '<', 20)->pluck('construccion', 'id');
        $tiposEstructura = construccion::where('id', '>', 20)->pluck('construccion', 'id');

        $bandaAncha = ['s' => 'Banda Ancha', 'n' => '56kbps'];
        $sino = ['s' => 'Sí', 'n' => 'No'];

        $contador    = $this->contador(10);
        $contador20  = $this->contador(20);
        $contador30  = $this->contador(30);
        $contador40  = $this->contador(40);
        $contador50  = $this->contador(50);
        $contador100 = $this->contador(100);
        $contador150 = $this->contador(150);
        $contador250 = $this->contador(250);

        return view('siben.edita_estructura', compact(
                'estructura',
                'tiposPropiedad',
                'tiposEstructura',
                'bandaAncha',
                'sino',
                'contador',
                'contador20',
                'contador30',
                'contador40',
                'contador50',
                'contador100',
                'contador150',
                'contador250',
        ));
    }
    /* Metodo auxiliar privado para generar arreglos de opciones nuemriacas */
    private function contador(int $max): array{
        return array_combine(range(0, $max),range(0, $max));
    }

    public function editaCoordinadores(Request $request){
        $id_escuela = session('Usuario.id_escuela');

        $escuelaIdInput = $request->input('data.Escuela.id',$request->input('id'));
    }

}