<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Escuela;
use App\Models\Mensaje;
use App\Models\MensajeDestino;
use App\Models\EscuelaStatus;
use App\Models\DomicilioEscuela;
use App\Models\Municipio;
use App\Models\Localidad;
use App\Models\CaracteristicaEscuela;
use App\Models\Construccion;
use App\Models\EscuelaLicenciatura;
use App\Models\Personal;

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
            $mensajes = array_merge($mensajes, $this->getMensajes(['modulo' => $modulo, 'status' => 'a', 'id_tipo' => '2', 'id_destino' => $id_escuela]));
        }
        if ($id_entidad){
            $mensajes = array_merge($mensajes, $this->getMensajes(['modulo' => $modulo, 'status' => 'a', 'id_tipo' => '4', 'id_destino' => $id_entidad]));
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
            'mensajeCaptura', 'mensajes', 'telefono', 'tipos_mensajes',
            'nivel', 'estructura', 'cuentaDirector', 'cuentaDomicilio', 'archivos'
        ));
    }
    /* Método auxiliar para consultar los mensajes relacionándolos con la tabla pivot */
    private function getMensajes(array $params): array {
        return Mensaje::join('mensajes_destinos', 'mensajes.id', '=', 'mensajes_destinos.id_mensaje')
            ->where('mensajes_destinos.modulo', $params['modulo'])
            ->where('mensajes_destinos.status', $params['status'])
            ->where('mensajes_destinos.id_tipo', $params['id_tipo'])
            ->where('mensajes_destinos.id_destino', $params['id_destino'])
            ->select('mensajes.*')
            ->get()
            ->toArray();
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
    public function editaEscuela(Request $request){
        $id_escuela = session('Usuario.id_escuela');
        $id_entidad = session('Usuario.id_entidad');

        $escuelaIdInput = $request->input('data.Escuela.id', $request->input('id'));
        
        if ($id_escuela != $escuelaIdInput){
            return redirect('/siben');
        }
        $domicilioEscuela = DomicilioEscuela::where('id_escuela', $id_escuela)->first();
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

    public function editaEstructura(Request $request){
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
    
        $escuelaIdInput = $request->input('data.Escuela.id', $request->input('id'));

        if ($id_escuela != $escuelaIdInput) {
        return redirect('/siben');
        }

        $escuelaLicenciaturas = EscuelaLicenciatura::with(['licenciatura', 'personal'])->where('id_escuela', $id_escuela)->get()->unique('id_licenciatura');
           
        $licenciaturas = $escuelaLicenciaturas->mapWithKeys(function ($item) {
        $nombre = $item->licenciatura ? $item->licenciatura->licenciatura : 'Sin definir';
        return [$item->id_licenciatura => $nombre];
        });

        $elegidos = $escuelaLicenciaturas->mapWithKeys(function ($item) {
            $personalId = $item->personal ? $item->personal->id : $item->id_personal;
            return [$item->id_licenciatura => $personalId];
        });

        $coordinadoresData = Personal::where('id_escuela', $id_escuela)->where('id_funcion', 4)->orderBy('apellido_paterno')->orderBy('nombre')->get();
          

        $coordinadores = $coordinadoresData->mapWithKeys(function ($personal) {
            $nombreCompleto = trim("{$personal->nombre} {$personal->apellido_paterno} {$personal->apellido_materno}");
            return [$personal->id => $nombreCompleto];
        });

        return view('siben.edita_coordinadores', compact(
            'licenciaturas', 
            'elegidos', 
            'coordinadores'
        ));
    }

    public function guardaEscuela(Request $request){
        $dataEscuela = $request->input('data.Escuela', $request->input('Escuela'));
         if(!empty($dataEscuela)){
            $id_escuela = session('Usuario.id_escuela');
            $escuela = Escuela::find($id_escuela);
            if(!$escuela || !$escuela->update($dataEscuela)){
                return response('Error: no se guardo la información', 400);
            }
        }
        return view('elements.vacio');
    }

    public function guardaEstructura(Request $request){
        $dataEstructura = $request->input('data.CaracteristicaEscuela', $request->input('CaracteristicaEscuela'));
        if (!empty($dataEstructura)){
        $id_escuela = session('Usuario.id_escuela');
        $modulo = session('Usuario.modulo');
        $dataEstructura['id_escuela'] = $id_escuela;
        $id_Estructura = $dataEstructura['id'] ?? null;
        $parametros = [
            'internet_alumno'         =>$dataEstructura['internet_alumno'] ?? null,
            'computo_alumno'          =>$dataEstructura['computo_alumno'] ?? null,
            'internet_docente'        =>$dataEstructura['internet_docente'] ?? null,
            'computo_docente'         =>$dataEstructura['computo_docente'] ?? null,
            'internet_administrativo' =>$dataEstructura['internet_administrativo'] ?? null,
            'computo_administrativo'  =>$dataEstructura['computo_administrativo'] ?? null
        ];

        $CaracteristicaModel = new CaracteristicaEscuela();
        $errores = $CaracteristicaModel->revisaestructura($parametros);
        if (empty($errores)){
            CaracteristicaEscuela::updateOrCreate(
                ['id'=>$id_Estructura],
                $dataEstructura
            );
            EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->update(['estructura'=>1]);
        }else {
            $mensajes = "";
            for ($i = 1; $i <= 3; $i++){
                if (!empty($errores[$i])){
                    $mensajes .= $errores[$i] . "\n";
                }
            }
            return response($mensajes, 400);
        }
        }
        return view('elements.vacio');
    }

    public  function guardaCoordinadores (Request $request) {
        $escuelaLicenciaturas = $request->input('data.EscuelaLicenciatura', $request->input('EscuelaLicenciatura'));

        if(!empty($escuelaLicenciaturas)){
            $id_escuela = session('Usuario.id_escuela');
             foreach ($escuelaLicenciaturas as $id => $id_personal) {
                $lic = explode('-', $id);
                $id_licenciatura = $lic[1] ?? null;

                if($id_licenciatura){
                    EscuelaLicenciatura::where('id_escuela', $id_escuela)->where('id_licenciatura', $id_licenciatura)->update(['id_personal' => $id_personal]);
                }
             }
        }
        return view('elements.vacio');
    }

    public function editaDatos(Request $request){
        $nivel   = session('Usuario.nivel');
        $captura = session('Usuario.captura');

        if ($nivel != 3 || $captura != 1) {
            return redirect('/siben');
        }

        $nombreEscuela = session('Usuario.escuela');
        $id_escuela    = session('Usuario.id_escuela');
        $id_entidad    = session('Usuario.id_entidad');
        $modulo        = session('Usuario.modulo');

        $title = 'Edición Datos Generales, ' . $nombreEscuela;

        // 2. Procesamiento si el formulario fue enviado (POST / PUT)
        if ($request->isMethod('post') || $request->isMethod('put')) {
            $dataEscuela    = $request->input('Escuela', $request->input('data.Escuela'));
            $dataEstructura = $request->input('CaracteristicaEscuela', $request->input('data.CaracteristicaEscuela'));

            $escuelaModel = Escuela::findOrFail($id_escuela);
            $escuelaModel->update($dataEscuela);

            $dataEstructura['id_escuela'] = $id_escuela;
            CaracteristicaEscuela::updateOrCreate(
                ['id_escuela' => $id_escuela],
                $dataEstructura
            );

            EscuelaStatus::where('id_escuela', $id_escuela)
                ->where('modulo', $modulo)
                ->update(['estructura' => '1']);

            return redirect('/siben/datos');
        }

        $escuela = Escuela::with('caracteristicaEscuela')->find($id_escuela);

        $educacionesAdicionales = EducacionAdicional::where('id_escuela', $id_escuela)->get();

        $licenciaturas = EscuelaLicenciatura::where('id_escuela', $id_escuela)->select('id_licenciatura', \DB::raw('MIN(id) as id'))->groupBy('id_licenciatura')->get();
            
        $municipios = Municipio::where('id_entidad', $id_entidad) ->orderBy('municipio')->pluck('municipio', 'id');
           
        $turnos         = ['m' => 'Matutino', 'v' => 'Vespertino', 'd' => 'Discontinuo', 'n' => 'Nocturno'];
        $sostenimientos = ['p' => 'Público', 'q' => 'Particular'];
        $posgrados      = ['m' => 'Maestria', 'd' => 'Doctorado', 'e' => 'Especialidad', 'c' => 'Educacion Continua', 'i' => 'SEPA Inglés'];
        $bandaAncha     = ['s' => 'Banda Ancha', 'n' => '56Kbps'];
        $sino           = ['s' => 'Sí', 'n' => 'No'];

        $tiposPropiedad  = Construccion::where('id', '<', 20)->pluck('construccion', 'id');
        $tiposEstructura = Construccion::where('id', '>', 20)->pluck('construccion', 'id');

        $contador    = $this->contador(10);
        $contador20  = $this->contador(20);
        $contador30  = $this->contador(30);
        $contador40  = $this->contador(40);
        $contador50  = $this->contador(50);
        $contador100 = $this->contador(100);
        $contador150 = $this->contador(150);
        $contador250 = $this->contador(250);

        $anterior = '/siben/datosEscuela';

        return view('siben.edita_datos', compact(
            'title',
            'escuela',
            'educacionesAdicionales',
            'licenciaturas',
            'municipios',
            'turnos',
            'sostenimientos',
            'posgrados',
            'bandaAncha',
            'sino',
            'tiposPropiedad',
            'tiposEstructura',
            'contador',
            'contador20',
            'contador30',
            'contador40',
            'contador50',
            'contador100',
            'contador150',
            'contador250',
            'anterior'
        ));
    }
    /**
   * parametros
   *
   * Para editar los parámetros de la escuela (licenciaturas impartidas)
   */
    public function parametros (Request $request) {
        $nivel   = session('Usuario.nivel');
        $captura = session('Usuario.captura');
         if ($nivel != 3 || $captura != 1) {
            return redirect('/siben');
        }
        $escuela      = session('Usuario.escuela');
        $id_escuela   = session('Usuario.id_escuela');
        $modulo       = session('Usuario.modulo');
        $cuenta_clave = session('Usuario.cuenta_clave');

        $title = 'Edición Datos Generales, ' . $escuela;

        if ($request->isMethod('post') || $request->isMethod('put')){
            $status = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();
            if($status){
                $statusData = $request->input('EscuelaStatus', $request->input('data.EscuelaStatus', []));
                $status->update(Arr::only($statusData, ['cantidad_alumnos', 'cantidad_personal']));
            }
            $capturista = Usuario::where('id_escuela', $id_escuela)->where('modulo', $modulo)->where('nivel', 4)->first();
            if ($capturista){
                $usuarioData = $request->input('Usuario', $request->input('data.Usuario', []));
                $usuarioData['status'] = 'a';

                $capturista->update(Arr::only($usuarioData, ['nombre', 'correo_electronico', 'status']));
            }
        }
        $modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta'];
        $turnos      = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno','d'=>'Discontinuo'];

        $licenciaturas = EscuelaLicenciatura::where('id_escuela', $id_escuela)->orderBy('id_licenciatura')->orderBy('id_especialidad')->orderBy('modalidad')->orderBy('turno')->get();

        $capturista = Usuario::where('id_escuela', $id_escuela)->where('modulo', $modulo)->where('nivel', 4)->first();

        $status = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();

        $mensaje = ($cuenta_clave > 0) ? 'Por favor, ingrese una clave de centro de trabajoa cada entrada' : '';

        return view('siben.parametros', compact(
            'title',
            'modalidades',
            'turnos',
            'licenciaturas',
            'capturista',
            'status',
            'mensaje'
        ));
    }
    public function agregaLicenciatura (Request $request) {
        $escuela = session('Usuario.escuela');
        $id_escuela = session('Usuario.id_escuela');

        $title    = 'Agregar Licenciatura, ' . $escuela;
        $anterior = true;
        if ($request->isMethod('post')){
            $data = $request->input('EscuelaLicenciatura', $request->all());

            EscuelaLicenciatura::firstOrCreate([
                'id_escuela'      => $id_escuela,
                'id_licenciatura' => $data['id_licenciatura'] ?? null,
                'id_especialidad' => $data['id_especialidad'] ?? null,
                'modalidad'       => $data['modalidad'] ?? null,
                'turno'           => $data['turno'] ?? null

            ]);
            return redirect('/siben/parametros');
        }

        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');
        $especialidad  = [];
        $modalidad     = [];
        $turnos        = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno','d' => 'Discontinuo'];

        return view('siben.agregar_licenciatura', compact(
            'title',
            'anterior',
            'licenciaturas',
            'especialidad',
            'modalidades',
            'turnos'
        ));

    }

    public function editaLicenciatura (Request $request, $id = null) {
        if(!$id){
            return redirect()->back();
        }
        $escuela    = session('Usuario.escuela');
        $id_escuela = session('Usuario.id_escuela');

        $title    = 'Agregar Licenciatura, ' . $escuela;
        $anterior = true;

        if ($request->isMethod('post') || $request->isMethod('put')){
            $data = $request->input('EscuelaLicenciatura', $request->all());
            $data['id_escuela'] = $id_escuela;

            $escuelaLicenciaturas = EscuelaLicenciatura::findOrFail($id);
            $escuelaLicenciaturas->update($data);

            return redirect('/siben/parametros');
        }

        $escuelaLicenciaturas = EscuelaLicenciatura::findOrFail($id);

        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');

        $especialidad = Especialidad::where('id_licenciatura', $escuelaLicenciaturas->id_licenciatura)->pluck('especialidad', 'id')->toArray();

        if(empty($especialidad)){
            $especialidad[0] = 'n/a';
        }
        $modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta'];
        $turnos = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno','d' => 'Discontinuo'];

        return view('siben.edita_licenciatura', compact(
            'title',
            'anterior',
            'licenciaturas',
            'especialidad',
            'modalidades',
            'turnos',
            'id',
            'escuelaLicenciatura'
        ));
    }

    public function eliminaLicenciatura ($id_escuela_licenciatura = null) {
        if($id_escuela_licenciatura){
            $escuelaLicenciaturas = EscuelaLicenciatura::where('id', $id_escuela_licenciatura)->where('id_escuela', $id_escuela)->first();

            if($escuelaLicenciaturas){
                $escuelaLicenciaturas->delete();
            }
        }
        return redirect('/siben/parametros');
    }
  /**
   * supervisores
   * Para capturar y activar los nombres de los supervisores por
   * escuela por estado. Esta función está destinada sólo a nivel 1 y
   * 2
   */   
    public function supervisores (Request $request, $id_entidad = null) {
        $nivel = session('Usuario.nivel');
        if (in_array($nivel, [2, 6])) { // si el supervisor estatal o el otro supervisor sin privilegios
            $id_entidad = session('Usuario.id_entidad');
        }
        $modulo = session('Usuario.modulo');
        $escuelas_supervisores = Escuela2::select('id', 'escuela', 'escuela_key')->where('status', 'a')->where('unica', '<', 2)->where('id_entidad', $id_entidad)->where('modulo', $modulo)->with(['usuarios' => function ($query) use ($modulo){
            $query->where('modulo', $modulo)->where('nivel', 3);
        }])->get();
        return view('siben.supervisores', compact(
            'escuelas_supervisores',
            'nivel'
            ));

    }
    /**
   * activaSupervisor
   * Cambiamos el status de 'x' a 'a' en el supervisor
   * indicado. Además se actualiza el nombre en caso necesario
   */
  public function activaSupervisor (Request $request, $id = null) {
    if ($id && ($request->isMethod('post') || $request->isMethod('put'))){
        $supervisor = Usuario::find($id);
        if($supervisor && $supervisor->nivel == 3){
            $data = $request->input('Usuario', $request->all());
            $data['status'] = 'a';
            $supervisor->update($data);
        }
    }
    return redirect()->back();
  }

   /**
   * coordinadores
   * Creamos las relaciones entre coordinadores y licenciaturas
   */
  public function coordinadores (Request $request) {
    $id_escuela = session('Usuario.id_escuela');
    if($request->isMethod('post') || $request->isMethod('put')){
        $datosLicenciaturas =  $request->input('EscuelaLicenciatura', $request->input('data.EscuelaLicenciatura'));
        if(!empty($datosLicenciaturas)){
            foreach ($datosLicenciaturas as $id => $id_personal) {
                $lic = explode('-', $id);
                $id_licenciatura = $lic[1] ?? null;
                if($id_licenciatura){
                    EscuelaLicenciatura::where('id_escuela', $id_escuela)->where('id_licenciatura', $id_licenciatura)->update(['id_personal' => $id_personal]);
                }
            }
        }
        return redirect('/siben/editaDatos');
    }
    $anterior = true;

    $escuelaLicenciaturas = EscuelaLicenciatura::with(['licenciatura', 'personal'])->where('id_escuelas', $id_escuela)->get()->unique('id_licenciatura');
    $licenciaturas = $escuelaLicenciaturas->mapWithKeys(function ($item){
        $nombre = $item->licenciatura ? $item->licenciatura->licenciatura : 'sin definir';
        return [$item->id_licenciatura => $nombre];
    });

    $elegidos = $escuelaLicenciaturas->mapWithKeys(function ($item){
        $personalId = $item->personal ? $item->personal->id : $item->personal;
        return [$item->id_licenciatura => $personalId];
    });

    $coordinadoresData = personal::where('id_escuela', $id_escuela)->where('id_funcion', 4)->orderBy('id_funcion')->get();

    $coordinadores = $coordinadoresData->mapWithKeys(function ($personal){
        $nombreCompleto = trim("{$personal->nombre} {$personal->apellido_paterno} {$personal->apellido_materno}");
        return [$personal->id_licenciatura => $nombreCompleto];
    });
    return view('siben.coordinadores', compact(
        'anterior',
        'licenciaturas',
        'elegidos',
        'coordinadores'
    ));
  }
    /**
   * avancePersonal
   * Monitorear el avance en la captura del personal por escuela.
   * Si el id_entidad es nulo, se verifica que sea administrador (dgespe)
   */
  public function avancePersonal (Request $request, $id_entidad = null) {
    $nivel = session('Usuario.nivel');

    if(in_array($nivel, [2, 6])){
        $id_entidad = session('Usuario.id_entidad');
    }
    $modulo = session('Usuario.modulo');

    $escuelas = Escuela2::select('id', 'escuela')->where('id_entidad', $id_entidad)->where('status', 'a')->where('unica', [0,1])->where('modulo', $modulo)->orderBy('escuela')->get();

    $escuelas->transform(function ($escuela) use ($modulo){
        $status = EscuelaStatus::where('id_escuela', $escuela->id)->where('modulo', $modulo)->first();

        $cantidadPersonal = $status ? $status->cantidad_personal : 0;

        $cuenta = Personal::where('id_escuela', $escuela->id)->where('status', 'a')->distinct('curp')->count('curp');

        $porcentaje = ($cantidadPersonal == 0) ? 0 : round($cuenta / $cantidadPersonal, 2);

        $escuela->cantidad_personal = $cantidadPersonal;
        $escuela->captura_personal  = $cuenta;
        $escuela->porcentaje        = $porcentaje;
        return $escuela;
    });
    return view('siben.avance_personal');
  }

  public function avanceAlumnos ($id_entidad = null) {
    
  }

}