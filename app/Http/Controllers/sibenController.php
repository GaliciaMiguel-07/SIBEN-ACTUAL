<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\CaracteristicaEscuela;
use App\Models\Construccion;
use App\Models\DomicilioEscuela;
use App\Models\EducacionAdicional;
use App\Models\Entidad;
use App\Models\Escuela;
use App\Models\Escuela2;
use App\Models\EscuelaLicenciatura;
use App\Models\EscuelaStatus;
use App\Models\Especialidad;
use App\Models\Licenciatura;
use App\Models\Localidad;
use App\Models\Mensaje;
use App\Models\Municipio;
use App\Models\Personal;
use App\Models\SeguridadSocial;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

abstract class sibenController
{
    /**
     * Ciclo de captura vigente (igual que en el sistema original).
     * Actualizar en cada ciclo: 20261, 20262, ...
     */
    protected string $captura_actual = '20252';

    /** Contexto del usuario autenticado, calculado una sola vez por petición. */
    protected ?array $contexto = null;

    /**
     * Reemplaza a session('Usuario.*') del sistema CakePHP.
     * Calcula los datos del usuario autenticado directamente desde la base
     * (auth()->user() + consultas), sin depender de un middleware ni de
     * claves de sesión previamente escritas.
     */
    protected function contexto(): array
    {
        if ($this->contexto !== null) {
            return $this->contexto;
        }

        $usuario   = auth()->user();
        $nivel     = $usuario?->nivel;
        $modulo    = $usuario?->modulo;
        $idEscuela = $usuario?->id_escuela;

        $ctx = [
            'id'               => $usuario?->id,
            'id_usuario'       => $usuario?->id_usuario,
            'nombre'           => $usuario?->nombre,
            'nivel'            => $nivel,
            'modulo'           => $modulo,
            'id_renapo'        => $usuario?->id_renapo,
            'id_escuela'       => $idEscuela,
            'escuela'          => null,
            'id_entidad'       => null,
            'entidad'          => null,
            'captura'          => null,
            'estructura'       => null,
            'status'           => $usuario?->status,
            'cuenta_clave'     => 0,
            'cuenta_director'  => 0,
            'cuenta_domicilio' => 0,
        ];

        // Nivel 1 (DGESPE): la captura siempre está abierta.
        if ($nivel == 1) {
            $ctx['captura'] = 1;
        }

        // Niveles 3 y 4: escuela + entidad a partir de usuarios.id_escuela.
        if (in_array($nivel, [3, 4]) && $idEscuela) {
            $escuela = Escuela::select('id', 'escuela', 'id_entidad')->find($idEscuela);
            if ($escuela) {
                $ctx['escuela']    = $escuela->escuela;
                $ctx['id_entidad'] = $escuela->id_entidad;
            }
        }

        // Niveles 2 y 6: entidad a partir de usuarios.id_renapo.
        if (in_array($nivel, [2, 6]) && $usuario?->id_renapo) {
            $entidad = Entidad::select('id', 'entidad')->where('id_renapo', $usuario->id_renapo)->first();
            if ($entidad) {
                $ctx['id_entidad'] = $entidad->id;
                $ctx['entidad']    = $entidad->entidad;
            }
        }

        // Si ya tenemos id_entidad pero no el nombre, lo buscamos.
        if ($ctx['id_entidad'] && !$ctx['entidad']) {
            $ctx['entidad'] = Entidad::where('id', $ctx['id_entidad'])->value('entidad');
        }

        // Estado de captura de la escuela (escuelas_status).
        if ($idEscuela && $modulo) {
            $status = EscuelaStatus::where('id_escuela', $idEscuela)->where('modulo', $modulo)->first();
            if ($status) {
                $ctx['status']     = $status->status;
                $ctx['captura']    = $status->captura;
                $ctx['estructura'] = $status->estructura;
            }
        }

        // Estas tres comprobaciones pertenecen al flujo de Licenciatura.
        // No aplicar la tabla EscuelaLicenciatura a usuarios de Posgrados.
        if ($idEscuela && $modulo === 'l') {
            $ctx['cuenta_clave'] = EscuelaLicenciatura::where('id_escuela', $idEscuela)
                ->where(function ($q) {
                    $q->whereNull('clave')->orWhere('clave', '');
                })->count();

            $ctx['cuenta_director'] = Personal::where('id_escuela', $idEscuela)
                ->where('id_funcion', 1)
                ->where('status', 'a')
                ->count();

            $ctx['cuenta_domicilio'] = DomicilioEscuela::where('id_escuela', $idEscuela)->count();
        }

        return $this->contexto = $ctx;
    }


    /**
     * Convierte la distribución histórica de AppController en opciones Laravel.
     * Cada opción usa el nombre de una ruta; las rutas aún no migradas se
     * muestran deshabilitadas en la vista y nunca apuntan a "#".
     */
    protected function menuItem(string $nombre, ?string $ruta = null, array $parametros = []): array
    {
        $disponible = $ruta !== null && \Illuminate\Support\Facades\Route::has($ruta);

        return [
            'nombre' => $nombre,
            'ruta' => $disponible ? route($ruta, $parametros) : null,
            'ruta_nombre' => $ruta,
            'disponible' => $disponible,
        ];
    }

    protected function menusLicenciatura(int $nivel, array $ctx = []): array
    {
        $status = $ctx['status'] ?? null;

        if ($nivel === 2 && $status === 'x') {
            return [
                $this->menuItem('Actualizar datos', 'siben.edita_usuario'),
            ];
        }

        $menus = [
            1 => [
                ['Edición', 'admin.index'],
                ['Mensajes', null],
                ['Directorio', 'directorio.gestion.index'],
                ['Estadísticas', 'informes.estadisticas'],
                ['Informe nacional', 'informes.informe_nacional'],
                ['Cambiar captura', 'siben.captura'],
                ['Lista usuarios', 'admin.usuarios.index'],
                ['Informes escuelas', null],
                ['Consulta histórico', null],
                ['Consulta docente', null],
                ['Prueba Verhoeff', 'siben.prueba_verhoeff'],
            ],
            2 => [
                ['Conflictos', null],
                ['Directorio', 'directorio.gestion.index'],
                ['Avance alumnos', 'siben.avance_alumnos'],
                ['Avance personal', 'siben.avance_personal'],
                ['Supervisores', 'siben.supervisores'],
                ['Directores', 'siben.directores'],
                ['Alumnos', 'alumnos.index'],
                ['Consulta personal', null],
                ['Listados', 'siben.listados'],
                ['Estadísticas', 'informes.estadisticas'],
            ],
            3 => [
                ['Configurar parámetros', 'siben.parametros'],
                ['Datos generales', 'siben.menu_datos'],
                ['Alumnos', 'alumnos.index'],
                ['Personal', 'personal.menu'],
                ['Listados', 'siben.listados'],
                ['Estadísticas', 'informes.estadisticas'],
            ],
            4 => [
                ['Datos generales', 'siben.menu_datos'],
                ['Alumnos', 'alumnos.index'],
                ['Personal', 'personal.menu'],
                ['Listados', 'siben.listados'],
                ['Estadísticas', 'informes.estadisticas'],
            ],
            5 => [
                ['Escuelas', null],
                ['Directorio', null],
                ['Estadísticas', 'informes.estadisticas'],
                ['Consulta alumno', null],
                ['Consulta docente', null],
            ],
            6 => [
                ['Directorio', 'informes.directorio'],
                ['Ver avance alumnos', 'siben.avance_alumnos'],
                ['Ver avance personal', 'siben.avance_personal'],
                ['Consulta alumnos', 'alumnos.index'],
                ['Consulta personal', null],
                ['Supervisores', 'siben.supervisores'],
                ['Listados', 'siben.listados'],
                ['Estadísticas', 'informes.estadisticas'],
            ],
            7 => [
                ['Consulta alumno', null],
                ['Consulta personal', null],
            ],
        ];

        return array_map(
            fn ($item) => $this->menuItem($item[0], $item[1]),
            $menus[$nivel] ?? []
        );
    }

    protected function menusPosgrado(int $nivel): array
    {
        $menus = [
            1 => [],
            2 => [
                ['Entidad Posgrados', null],
                ['Avance', null],
                ['Supervisores', 'siben.supervisores'],
                ['Listados', null],
            ],
            3 => [
                ['Parámetros', null],
                ['Posgrados', null],
                ['Corregir Alumnos', null],
                ['Corregir Docentes', null],
                ['Listados', null],
            ],
            4 => [
                ['Alumnos', null],
                ['Docentes', null],
                ['Listados', null],
            ],
        ];

        return array_map(
            fn ($item) => $this->menuItem($item[0], $item[1]),
            $menus[$nivel] ?? []
        );
    }

    protected function obtenerMenu(array $ctx): array
    {
        $nivel = (int) ($ctx['nivel'] ?? 0);
        $modulo = $ctx['modulo'] ?? null;

        return match ($modulo) {
            'l' => $this->menusLicenciatura($nivel, $ctx),
            'p' => $this->menusPosgrado($nivel),
            default => [],
        };
    }

    public function index()
    {
        $ctx = $this->contexto();
        $menu = $this->obtenerMenu($ctx);

        $nivel           = $ctx['nivel'];
        $captura         = $ctx['captura'];
        $cuenta_clave    = $ctx['cuenta_clave'];
        $estructura      = $ctx['estructura'];
        $modulo          = $ctx['modulo'];
        $cuentaDirector  = $ctx['cuenta_director'];
        $cuentaDomicilio = $ctx['cuenta_domicilio'];
        $id_escuela      = $ctx['id_escuela'];
        $id_usuario      = $ctx['id'];
        $id_entidad      = $ctx['id_entidad'];

        $escuela  = Escuela::select('telefono1')->find($id_escuela);
        $telefono = $escuela ? $escuela->telefono1 : '';

        $mensajeCaptura = null;

        if ($nivel >= 3 && $nivel <= 4) {
            if ($captura == 0) {
                $mensajeCaptura = 'La captura está finalizada';
            } elseif ($captura == 2) {
                $mensajeCaptura = 'Demasiadas conexiones, por favor intente más tarde';
            } elseif ($estructura == 0 && $modulo === 'l') {
                $mensajeCaptura = 'Por favor, actualice los Datos Generales';
            } elseif ($cuenta_clave > 0) {
                $mensajeCaptura = 'Por favor, ingrese sus claves de centro de trabajo';
            } elseif ($cuentaDirector == 0 && $modulo == 'l') {
                $mensajeCaptura = 'Por favor agregue o active al director de la escuela';
            } elseif ($telefono == '' && $modulo == 'l') {
                $mensajeCaptura = 'Por favor actualice la información adicional(telefono,correo)';
            }
        }

        $mensajes = array_merge(
            $this->getMensajes(['modulo' => $modulo, 'status' => 'a', 'id_tipo' => '1', 'id_destino' => $nivel]),
            $this->getMensajes(['modulo' => $modulo, 'status' => 'a', 'id_tipo' => '3', 'id_destino' => $id_usuario])
        );
        if ($id_escuela) {
            $mensajes = array_merge($mensajes, $this->getMensajes(['modulo' => $modulo, 'status' => 'a', 'id_tipo' => '2', 'id_destino' => $id_escuela]));
        }
        if ($id_entidad) {
            $mensajes = array_merge($mensajes, $this->getMensajes(['modulo' => $modulo, 'status' => 'a', 'id_tipo' => '4', 'id_destino' => $id_entidad]));
        }

        $tipos_mensajes = [
            '1' => 'Nivel',
            '2' => 'Escuela',
            '3' => 'Usuario',
            '4' => 'Entidad',
        ];

        $archivos = in_array($nivel, [2, 3, 4]);

        return view('siben.index', compact(
            'mensajeCaptura', 'mensajes', 'telefono', 'tipos_mensajes',
            'nivel', 'estructura', 'cuentaDirector', 'cuentaDomicilio', 'archivos', 'menu'
        ));
    }

    /* Método auxiliar para consultar los mensajes relacionándolos con la tabla pivot */
    protected function getMensajes(array $params): array
    {
        return Mensaje::join('mensajes_destinos', 'mensajes.id', '=', 'mensajes_destinos.id_mensaje')
            ->where('mensajes_destinos.modulo', $params['modulo'])
            ->where('mensajes_destinos.status', $params['status'])
            ->where('mensajes_destinos.id_tipo', $params['id_tipo'])
            ->where('mensajes_destinos.id_destino', $params['id_destino'])
            ->select('mensajes.*')
            ->get()
            ->toArray();
    }

    public function archivos(Request $request)
    {
        $anterior = false;
        $clave    = null;

        if ($request->filled('Escuela.escuela_key') || $request->filled('escuela_key')) {
            $clave    = $request->input('Escuela.escuela_key', $request->input('escuela_key'));
            $anterior = true;
        } else {
            $ctx = $this->contexto();
            switch ($ctx['nivel']) {
                case 2:
                    $clave = $ctx['id_renapo'];
                    break;
                case 3:
                case 4:
                    $escuela = Escuela::select('escuela_key')->find($ctx['id_escuela']);
                    $clave   = $escuela ? $escuela->escuela_key : null;
                    break;
            }
        }

        return view('siben.archivos', compact('clave', 'anterior'));
    }

    public function listados()
    {
        $ctx = $this->contexto();

        $id_escuela    = $ctx['id_escuela'];
        $modulo        = $ctx['modulo'];
        $nivel         = $ctx['nivel'];
        $captura       = $ctx['captura'];
        $nombreEntidad = $ctx['id_renapo'];

        $escuelaStatus = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();
        $oficializar   = $escuelaStatus ? $escuelaStatus->oficializar : 0;

        $opcionesMenu  = [];
        $opcionesMenu2 = [];

        if ($nivel == 3) {
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

        if ($nivel == 3 && $nombreEntidad == 'GT') {
            $opcionesMenu[] = ['siben', 'Finalizar Captura', 'finalizaCaptura', 0];
        }
        if ($nivel == 3 && $captura) {
            $opcionesMenu[] = ['siben', 'Finalizar Captura', 'finalizaCaptura', 0];
        }
        if ($nivel == 3 && $captura == 0) {
            $opcionesMenu[] = ['siben', 'Oficializar Informaci&oacute;n', 'oficializarInformacion', 0, null, $oficializar];
        }

        return view('siben.listados', compact('opcionesMenu', 'opcionesMenu2'));
    }

    public function datosEscuela()
    {
        $ctx = $this->contexto();

        $modulo        = $ctx['modulo'];
        $nivel         = $ctx['nivel'];
        $nombreEscuela = $ctx['escuela'];
        $id_escuela    = $ctx['id_escuela'];

        if ($modulo != 'l') {
            return redirect()->route('siben.index');
        }

        $escuela  = Escuela::find($id_escuela);
        $telefono = $escuela ? $escuela->telefono1 : '';

        $title          = 'Datos Adicionales, ' . $nombreEscuela;
        $turnos         = ['m' => 'Matutino', 'v' => 'Vespertino', 'd' => 'Discontinuo', 'n' => 'Nocturno'];
        $sostenimientos = ['p' => 'Público', 'q' => 'Particular'];

        $urls['escuela']       = url('/siben/editaAdicionales');
        $textos['escuela']     = 'Editar';
        $parametros['escuela'] = "data[Escuela][id]={$id_escuela}";
        $eventos['escuela']    = "consultaAjax('{$urls['escuela']}', '{$parametros['escuela']}', muestraOverlay, muestraOcupado)";
        $evento_domicilio      = "locacion('" . url('/siben') . "')";

        $botones = [];
        switch ($nivel) {
            case 3:
                $botones['escuela']  = "<input id='e_{$id_escuela}' type='button' value='{$textos['escuela']}' onclick=\"{$eventos['escuela']}\" class='boton__primario' style='padding: 6px 14px; cursor: pointer;' />";
                $botones['terminar'] = "<input type='button' value='Terminar' onclick=\"{$evento_domicilio}\" class='boton__secundario' style='padding: 6px 14px; cursor: pointer;' />";
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

    public function editaEscuela(Request $request)
    {
        $ctx = $this->contexto();

        $id_escuela = $ctx['id_escuela'];
        $id_entidad = $ctx['id_entidad'];

        $escuelaIdInput = $request->input('data.Escuela.id', $request->input('id'));

        if ($id_escuela != $escuelaIdInput) {
            return redirect()->route('siben.index');
        }

        $domicilioEscuela = DomicilioEscuela::where('id_escuela', $id_escuela)->first();
        $escuela          = Escuela::find($id_escuela);
        $id_municipio     = $domicilioEscuela ? $domicilioEscuela->cve_mun : null;

        $municipios = Municipio::where('id_entidad', $id_entidad)->orderBy('municipio')->pluck('municipio', 'id_municipio');

        $locs = Localidad::where('id_entidad', $id_entidad)->where('id_municipio', $id_municipio)->pluck('localidad', 'id_localidad');

        $turnos = ['m' => 'Matutino', 'v' => 'Vespertino', 'd' => 'Discontinuo', 'n' => 'Nocturno'];

        $vialidad = [
            'AMPLIACION' => 'AMPLIACION', 'ANDADOR' => 'ANDADOR', 'AVENIDA' => 'AVENIDA',
            'BOULEVARD' => 'BOULEVARD', 'CALLE' => 'CALLE', 'CALLEJON' => 'CALLEJON',
            'CALZADA' => 'CALZADA', 'CAMINO' => 'CAMINO', 'CARRETERA' => 'CARRETERA',
            'CERRADA' => 'CERRADA', 'CIRCUITO' => 'CIRCUITO', 'CIRCUNVALACION' => 'CIRCUNVALACION',
            'CONTINUACION' => 'CONTINUACION', 'CORREDOR' => 'CORREDOR', 'DIAGONAL' => 'DIAGONAL',
            'EJE VIAL' => 'EJE VIAL', 'PASAJE' => 'PASAJE', 'PEATONAL' => 'PEATONAL',
            'PERIFERICO' => 'PERIFERICO', 'PRIVADA' => 'PRIVADA', 'PROLONGACION' => 'PROLONGACION',
            'RETORNO' => 'RETORNO', 'VIADUCTO' => 'VIADUCTO',
        ];

        $asentamientos = [
            'AEROPUERTO' => 'AEROPUERTO', 'AMPLIACION' => 'AMPLIACION', 'BARRIO' => 'BARRIO',
            'CANTON' => 'CANTON', 'CIUDAD' => 'CIUDAD', 'CIUDAD INDUSTRIAL' => 'CIUDAD INDUSTRIAL',
            'COLONIA' => 'COLONIA', 'CONDOMINIO' => 'CONDOMINIO', 'CONJUNTO HABITACIONAL' => 'CONJUNTO HABITACIONAL',
            'CORREDOR INDUSTRIAL' => 'CORREDOR INDUSTRIAL', 'COTO' => 'COTO', 'CUARTEL' => 'CUARTEL',
            'EJIDO' => 'EJIDO', 'EXHACIENDA' => 'EXHACIENDA', 'FRACCION' => 'FRACCION',
            'FRACCIONAMIENTO' => 'FRACCIONAMIENTO', 'GRANJA' => 'GRANJA', 'HACIENDA' => 'HACIENDA',
            'INGENIO' => 'INGENIO', 'MANZANA' => 'MANZANA', 'PARAJE' => 'PARAJE', 'PARQUE INDUSTRIAL' => 'PARQUE INDUSTRIAL',
            'PRIVADA' => 'PRIVADA', 'PROLONGACION' => 'PROLONGACION', 'PUEBLO' => 'PUEBLO', 'PUERTO' => 'PUERTO',
            'RANCHERIA' => 'RANCHERIA', 'RANCHO' => 'RANCHO', 'REGION' => 'REGION', 'RESIDENCIAL' => 'RESIDENCIAL',
            'RINCONADA' => 'RINCONADA', 'SECCION' => 'SECCION', 'SECTOR' => 'SECTOR', 'SUPERMANZANA' => 'SUPERMANZANA',
            'UNIDAD' => 'UNIDAD', 'UNIDAD HABITACIONAL' => 'UNIDAD HABITACIONAL', 'VILLA' => 'VILLA',
            'ZONA FEDERAL' => 'ZONA FEDERAL', 'ZONA INDUSTRIAL' => 'ZONA INDUSTRIAL', 'ZONA MILITAR' => 'ZONA MILITAR',
        ];

        $seguridad_social = SeguridadSocial::pluck('regimen', 'id');
        $admon            = ['ESTATAL' => 'ESTATAL', 'FEDERAL' => 'FEDERAL'];
        $derecho          = ['DE CUOTA' => 'DE CUOTA', 'LIBRE' => 'LIBRE'];
        $admon2           = ['BRECHA' => 'BRECHA', 'CAMINO' => 'CAMINO', 'TERRACERIA' => 'TERRACERIA', 'VEREDA' => 'VEREDA'];
        $margen           = ['DERECHO' => 'DERECHO', 'IZQUIERO' => 'IZQUIERDO'];

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

    public function editaEstructura(Request $request)
    {
        $id_estructura = $request->input('data.CaracteristicaEscuela.id', $request->input('id'));

        $estructura      = CaracteristicaEscuela::find($id_estructura);
        $tiposPropiedad  = Construccion::where('id', '<', 20)->pluck('construccion', 'id');
        $tiposEstructura = Construccion::where('id', '>', 20)->pluck('construccion', 'id');

        $bandaAncha = ['s' => 'Banda Ancha', 'n' => '56kbps'];
        $sino       = ['s' => 'Sí', 'n' => 'No'];

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

    /* Método auxiliar protegido para generar arreglos de opciones numéricas */
    protected function contador(int $max): array
    {
        return array_combine(range(0, $max), range(0, $max));
    }

    public function editaCoordinadores(Request $request)
    {
        $id_escuela = $this->contexto()['id_escuela'];

        $escuelaIdInput = $request->input('data.Escuela.id', $request->input('id'));

        if ($id_escuela != $escuelaIdInput) {
            return redirect()->route('siben.index');
        }

        $escuelaLicenciaturas = EscuelaLicenciatura::with(['licenciatura', 'personal'])
            ->where('id_escuela', $id_escuela)
            ->get()
            ->unique('id_licenciatura');

        $licenciaturas = $escuelaLicenciaturas->mapWithKeys(function ($item) {
            $nombre = $item->licenciatura ? $item->licenciatura->licenciatura : 'Sin definir';
            return [$item->id_licenciatura => $nombre];
        });

        $elegidos = $escuelaLicenciaturas->mapWithKeys(function ($item) {
            $personalId = $item->personal ? $item->personal->id : $item->id_personal;
            return [$item->id_licenciatura => $personalId];
        });

        $coordinadoresData = Personal::where('id_escuela', $id_escuela)
            ->where('id_funcion', 4)
            ->orderBy('apellido_paterno')
            ->orderBy('nombre')
            ->get();

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

    public function guardaEscuela(Request $request)
    {
        $dataEscuela = $request->input('data.Escuela', $request->input('Escuela'));

        if (!empty($dataEscuela)) {
            $id_escuela = $this->contexto()['id_escuela'];
            $escuela    = Escuela::find($id_escuela);

            if (!$escuela || !$escuela->update($dataEscuela)) {
                return response('Error: no se guardo la información', 400);
            }
        }

        return view('elements.vacio');
    }

    public function guardaEstructura(Request $request)
    {
        $dataEstructura = $request->input('data.CaracteristicaEscuela', $request->input('CaracteristicaEscuela'));

        if (!empty($dataEstructura)) {
            $ctx = $this->contexto();

            $id_escuela = $ctx['id_escuela'];
            $modulo     = $ctx['modulo'];

            $dataEstructura['id_escuela'] = $id_escuela;
            $idEstructura                 = $dataEstructura['id'] ?? null;

            $parametros = [
                'internet_alumnos'        => $dataEstructura['internet_alumnos'] ?? null,
                'computo_alumnos'         => $dataEstructura['computo_alumnos'] ?? null,
                'internet_docentes'       => $dataEstructura['internet_docentes'] ?? null,
                'computo_docentes'        => $dataEstructura['computo_docentes'] ?? null,
                'internet_administrativo' => $dataEstructura['internet_administrativo'] ?? null,
                'computo_administrativo'  => $dataEstructura['computo_administrativo'] ?? null,
            ];

            // Validación equivalente a CaracteristicaEscuela::revisaestructura()
            $errores = [];
            if (($parametros['internet_alumnos'] ?? 0) > ($parametros['computo_alumnos'] ?? 0)) {
                $errores[1] = 'Error 1: El número de Computadoras con Internet para Alumnos no debe ser mayor al número de computadoras para Alumnos.';
            }
            if (($parametros['internet_docentes'] ?? 0) > ($parametros['computo_docentes'] ?? 0)) {
                $errores[2] = 'Error 2: El número de Computadoras con Internet para Docentes no debe ser mayor al número de computadoras para Docentes.';
            }
            if (($parametros['internet_administrativo'] ?? 0) > ($parametros['computo_administrativo'] ?? 0)) {
                $errores[3] = 'Error 3: El número de Computadoras con Internet para Administrativos no debe ser mayor al número de computadoras para Administrativos.';
            }

            if (empty($errores)) {
                CaracteristicaEscuela::updateOrCreate(['id' => $idEstructura], $dataEstructura);
                EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->update(['estructura' => 1]);
            } else {
                $mensajes = '';
                for ($i = 1; $i <= 3; $i++) {
                    if (!empty($errores[$i])) {
                        $mensajes .= $errores[$i] . "\n";
                    }
                }
                return response($mensajes, 400);
            }
        }

        return view('elements.vacio');
    }

    public function guardaCoordinadores(Request $request)
    {
        $escuelaLicenciaturas = $request->input('data.EscuelaLicenciatura', $request->input('EscuelaLicenciatura'));

        if (!empty($escuelaLicenciaturas)) {
            $id_escuela = $this->contexto()['id_escuela'];

            foreach ($escuelaLicenciaturas as $id => $id_personal) {
                $lic             = explode('-', $id);
                $id_licenciatura = $lic[1] ?? null;

                if ($id_licenciatura) {
                    EscuelaLicenciatura::where('id_escuela', $id_escuela)
                        ->where('id_licenciatura', $id_licenciatura)
                        ->update(['id_personal' => $id_personal]);
                }
            }
        }

        return view('elements.vacio');
    }

    public function editaDatos(Request $request)
    {
        $ctx = $this->contexto();

        $nivel   = $ctx['nivel'];
        $captura = $ctx['captura'];

        if ($nivel != 3 || $captura != 1) {
            return redirect()->route('siben.index');
        }

        $nombreEscuela = $ctx['escuela'];
        $id_escuela    = $ctx['id_escuela'];
        $id_entidad    = $ctx['id_entidad'];
        $modulo        = $ctx['modulo'];

        $title = 'Edición Datos Generales, ' . $nombreEscuela;

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

            return redirect()->route('siben.datosEscuela');
        }

        $escuela = Escuela::with('caracteristicaEscuela')->find($id_escuela);

        $educacionesAdicionales = EducacionAdicional::where('id_escuela', $id_escuela)->get();

        $licenciaturas = EscuelaLicenciatura::where('id_escuela', $id_escuela)
            ->select('id_licenciatura', DB::raw('MIN(id) as id'))
            ->groupBy('id_licenciatura')
            ->get();

        $municipios = Municipio::where('id_entidad', $id_entidad)->orderBy('municipio')->pluck('municipio', 'id_municipio');

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

    public function parametros(Request $request)
    {
        $ctx = $this->contexto();

        $nivel   = $ctx['nivel'];
        $captura = $ctx['captura'];

        if ($nivel != 3 || $captura != 1) {
            return redirect()->route('siben.index');
        }

        $escuela      = $ctx['escuela'];
        $id_escuela   = $ctx['id_escuela'];
        $modulo       = $ctx['modulo'];
        $cuenta_clave = $ctx['cuenta_clave'];

        $title = 'Edición Datos Generales, ' . $escuela;

        if ($request->isMethod('post') || $request->isMethod('put')) {
            $status = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();
            if ($status) {
                $statusData = $request->input('EscuelaStatus', $request->input('data.EscuelaStatus', []));
                $status->update(Arr::only($statusData, ['cantidad_alumnos', 'cantidad_personal']));
            }

            $capturista = Usuario::where('id_escuela', $id_escuela)->where('modulo', $modulo)->where('nivel', 4)->first();
            if ($capturista) {
                $usuarioData = $request->input('Usuario', $request->input('data.Usuario', []));
                $usuarioData['status'] = 'a';

                $capturista->update(Arr::only($usuarioData, ['nombre', 'correo_electronico', 'status']));
            }
        }

        $modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta'];
        $turnos      = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];

        $licenciaturas = EscuelaLicenciatura::where('id_escuela', $id_escuela)
            ->orderBy('id_licenciatura')
            ->orderBy('id_especialidad')
            ->orderBy('modalidad')
            ->orderBy('turno')
            ->get();

        $capturista = Usuario::where('id_escuela', $id_escuela)->where('modulo', $modulo)->where('nivel', 4)->first();

        $status = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();

        $mensaje = ($cuenta_clave > 0) ? 'Por favor, ingrese una clave de centro de trabajo a cada entrada' : '';

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

    public function agregaLicenciatura(Request $request)
    {
        $ctx = $this->contexto();

        $escuela    = $ctx['escuela'];
        $id_escuela = $ctx['id_escuela'];

        $title    = 'Agregar Licenciatura, ' . $escuela;
        $anterior = true;

        if ($request->isMethod('post')) {
            $data = $request->input('EscuelaLicenciatura', $request->except('_token'));

            EscuelaLicenciatura::firstOrCreate([
                'id_escuela'      => $id_escuela,
                'id_licenciatura' => $data['id_licenciatura'] ?? null,
                'id_especialidad' => $data['id_especialidad'] ?? null,
                'modalidad'       => $data['modalidad'] ?? null,
                'turno'           => $data['turno'] ?? null,
            ]);

            return redirect()->route('siben.parametros');
        }

        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');
        $especialidad  = [];
        $modalidades   = ['e' => 'Escolarizada', 'm' => 'Mixta'];
        $turnos        = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];

        return view('siben.agregar_licenciatura', compact(
            'title',
            'anterior',
            'licenciaturas',
            'especialidad',
            'modalidades',
            'turnos'
        ));
    }

    public function editaLicenciatura(Request $request, $id = null)
    {
        if (!$id) {
            return redirect()->back();
        }

        $ctx = $this->contexto();

        $escuela    = $ctx['escuela'];
        $id_escuela = $ctx['id_escuela'];

        $title    = 'Agregar Licenciatura, ' . $escuela;
        $anterior = true;

        if ($request->isMethod('post') || $request->isMethod('put')) {
            $data = $request->input('EscuelaLicenciatura', $request->except('_token'));
            $data['id_escuela'] = $id_escuela;

            $escuelaLicenciaturas = EscuelaLicenciatura::findOrFail($id);
            $escuelaLicenciaturas->update($data);

            return redirect()->route('siben.parametros');
        }

        $escuelaLicenciaturas = EscuelaLicenciatura::findOrFail($id);

        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');

        $especialidad = Especialidad::where('id_licenciatura', $escuelaLicenciaturas->id_licenciatura)
            ->pluck('especialidad', 'id')
            ->toArray();

        if (empty($especialidad)) {
            $especialidad[0] = 'n/a';
        }

        $modalidades = ['e' => 'Escolarizada', 'm' => 'Mixta'];
        $turnos      = ['m' => 'Matutino', 'v' => 'Vespertino', 'n' => 'Nocturno', 'd' => 'Discontinuo'];

        return view('siben.edita_licenciatura', compact(
            'title',
            'anterior',
            'licenciaturas',
            'especialidad',
            'modalidades',
            'turnos',
            'id',
            'escuelaLicenciaturas'
        ));
    }

    public function eliminaLicenciatura($id_escuela_licenciatura = null)
    {
        if ($id_escuela_licenciatura) {
            $id_escuela = $this->contexto()['id_escuela'];

            $escuelaLicenciaturas = EscuelaLicenciatura::where('id', $id_escuela_licenciatura)
                ->where('id_escuela', $id_escuela)
                ->first();

            if ($escuelaLicenciaturas) {
                $escuelaLicenciaturas->delete();
            }
        }

        return redirect()->route('siben.parametros');
    }

    public function supervisores(Request $request, $id_entidad = null)
    {
        $ctx = $this->contexto();

        $nivel = $ctx['nivel'];
        if (in_array($nivel, [2, 6])) {
            $id_entidad = $ctx['id_entidad'];
        }

        $modulo = $ctx['modulo'];

        $escuelas_supervisores = Escuela2::select('id', 'escuela', 'escuela_key')
            ->where('status', 'a')
            ->where('unica', '<', 2)
            ->where('id_entidad', $id_entidad)
            ->where('modulo', $modulo)
            ->with(['usuarios' => function ($query) use ($modulo) {
                $query->where('modulo', $modulo)->where('nivel', 3);
            }])
            ->get();

        return view('siben.supervisores', compact(
            'escuelas_supervisores',
            'nivel'
        ));
    }

    public function activaSupervisor(Request $request, $id = null)
    {
        if ($id && ($request->isMethod('post') || $request->isMethod('put'))) {
            $supervisor = Usuario::find($id);
            if ($supervisor && $supervisor->nivel == 3) {
                $data = $request->input('Usuario', $request->all());
                $data['status'] = 'a';
                $supervisor->update($data);
            }
        }

        return redirect()->back();
    }

    public function coordinadores(Request $request)
    {
        $id_escuela = $this->contexto()['id_escuela'];

        if ($request->isMethod('post') || $request->isMethod('put')) {
            $datosLicenciaturas = $request->input('EscuelaLicenciatura', $request->input('data.EscuelaLicenciatura'));

            if (!empty($datosLicenciaturas)) {
                foreach ($datosLicenciaturas as $id => $id_personal) {
                    $lic             = explode('-', $id);
                    $id_licenciatura = $lic[1] ?? null;

                    if ($id_licenciatura) {
                        EscuelaLicenciatura::where('id_escuela', $id_escuela)
                            ->where('id_licenciatura', $id_licenciatura)
                            ->update(['id_personal' => $id_personal]);
                    }
                }
            }

            return redirect()->route('siben.editaDatos');
        }

        $anterior = true;

        $escuelaLicenciaturas = EscuelaLicenciatura::with(['licenciatura', 'personal'])
            ->where('id_escuela', $id_escuela)
            ->get()
            ->unique('id_licenciatura');

        $licenciaturas = $escuelaLicenciaturas->mapWithKeys(function ($item) {
            $nombre = $item->licenciatura ? $item->licenciatura->licenciatura : 'sin definir';
            return [$item->id_licenciatura => $nombre];
        });

        $elegidos = $escuelaLicenciaturas->mapWithKeys(function ($item) {
            $personalId = $item->personal ? $item->personal->id : $item->id_personal;
            return [$item->id_licenciatura => $personalId];
        });

        $coordinadoresData = Personal::where('id_escuela', $id_escuela)
            ->where('id_funcion', 4)
            ->orderBy('id_funcion')
            ->get();

        $coordinadores = $coordinadoresData->mapWithKeys(function ($personal) {
            $nombreCompleto = trim("{$personal->nombre} {$personal->apellido_paterno} {$personal->apellido_materno}");
            return [$personal->id => $nombreCompleto];
        });

        return view('siben.coordinadores', compact(
            'anterior',
            'licenciaturas',
            'elegidos',
            'coordinadores'
        ));
    }

    public function avancePersonal(Request $request, $id_entidad = null)
    {
        $ctx = $this->contexto();

        $nivel = $ctx['nivel'];
        if (in_array($nivel, [2, 6])) {
            $id_entidad = $ctx['id_entidad'];
        }

        $modulo = $ctx['modulo'];

        $escuelas = Escuela2::select('id', 'escuela')
            ->where('id_entidad', $id_entidad)
            ->where('status', 'a')
            ->whereIn('unica', [0, 1])
            ->where('modulo', $modulo)
            ->orderBy('escuela')
            ->get();

        $escuelas->transform(function ($escuela) use ($modulo) {
            $status = EscuelaStatus::where('id_escuela', $escuela->id)
                ->where('modulo', $modulo)
                ->first();

            $cantidadPersonal = $status ? $status->cantidad_personal : 0;

            $cuenta = Personal::where('id_escuela', $escuela->id)
                ->where('status', 'a')
                ->distinct('curp')
                ->count('curp');

            $porcentaje = ($cantidadPersonal == 0) ? 0 : round($cuenta / $cantidadPersonal, 2);

            $escuela->cantidad_personal = $cantidadPersonal;
            $escuela->captura_personal  = $cuenta;
            $escuela->porcentaje        = $porcentaje * 100;

            return $escuela;
        });

        return view('siben.avance_personal', compact('escuelas'));
    }

    public function avanceAlumnos(Request $request, $id_entidad = null)
    {
        $ctx = $this->contexto();

        $nivel = $ctx['nivel'];
        if (in_array($nivel, [2, 6])) {
            $id_entidad = $ctx['id_entidad'];
        }

        $modulo = $ctx['modulo'];

        $escuelas = Escuela2::select('id', 'escuela')
            ->where('id_entidad', $id_entidad)
            ->where('status', 'a')
            ->whereIn('unica', [0, 1])
            ->where('modulo', $modulo)
            ->orderBy('escuela')
            ->get();

        $escuelas->transform(function ($escuela) use ($modulo) {
            $status = EscuelaStatus::where('id_escuela', $escuela->id)
                ->where('modulo', $modulo)
                ->first();

            $cantidadAlumnos = $status ? $status->cantidad_alumnos : 0;

            $cuenta = Alumno::where('id_escuela', $escuela->id)
                ->where('status', 'i')
                ->where('captura', $this->captura_actual)
                ->distinct('curp')
                ->count('curp');

            $porcentaje = ($cantidadAlumnos == 0) ? 0 : round($cuenta / $cantidadAlumnos, 2);

            $escuela->cantidad_alumnos = $cantidadAlumnos;
            $escuela->captura_alumnos  = $cuenta;
            $escuela->porcentaje       = $porcentaje * 100;

            return $escuela;
        });

        return view('siben.avance_alumnos', compact('escuelas'));
    }

    public function avanceEscuela(Request $request)
    {
        $ctx = $this->contexto();

        $id_escuela = $ctx['id_escuela'];
        $modulo     = $ctx['modulo'];

        $escuela = Escuela2::find($id_escuela);

        if (!$escuela) {
            return redirect()->back();
        }

        $status = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();

        $cantidadAlumnos = $status ? $status->cantidad_alumnos : 0;

        $cuentaAlumnos = Alumno::where('id_escuela', $id_escuela)
            ->where('status', 'i')
            ->where('captura', $this->captura_actual)
            ->distinct('curp')
            ->count('curp');

        $porcentajeAlumnos = ($cantidadAlumnos == 0) ? 0 : round($cuentaAlumnos / $cantidadAlumnos, 2);

        $escuela->cantidad_alumnos   = $cantidadAlumnos;
        $escuela->captura_alumnos    = $cuentaAlumnos;
        $escuela->porcentaje_alumnos = $porcentajeAlumnos * 100;

        $cantidadPersonal = $status ? $status->cantidad_personal : 0;

        $cuentaPersonal = Personal::where('id_escuela', $id_escuela)
            ->where('status', 'a')
            ->distinct('curp')
            ->count('curp');

        $porcentajePersonal = ($cantidadPersonal == 0) ? 0 : round($cuentaPersonal / $cantidadPersonal, 2);

        $escuela->cantidad_personal   = $cantidadPersonal;
        $escuela->captura_personal    = $cuentaPersonal;
        $escuela->porcentaje_personal = $porcentajePersonal * 100;

        $anterior = true;

        return view('siben.avance_escuela', compact('escuela', 'anterior'));
    }

    public function finalizaCaptura(Request $request)
    {
        $ctx = $this->contexto();

        $nivel = $ctx['nivel'];
        if ($nivel != 3) {
            return redirect()->back();
        }

        $id_escuela = $ctx['id_escuela'];
        $modulo     = $ctx['modulo'];

        $escuelaStatus = EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->first();

        if ($escuelaStatus) {
            $escuelaStatus->update(['captura' => '0']);
        }

        return redirect()->route('siben.listados');
    }

    public function oficializarInformacion(Request $request)
    {
        $ctx = $this->contexto();

        $nivel = $ctx['nivel'];
        if ($nivel != 3) {
            return redirect()->back();
        }

        $id_escuela = $ctx['id_escuela'];
        $modulo     = $ctx['modulo'];

        EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->update([
                'oficializar'       => 1,
                'oficializar_fecha' => now()->format('Y-m-d'),
            ]);

        return redirect()->route('siben.listados');
    }

    public function especialidades(Request $request)
    {
        $id_licenciatura = $request->input('EscuelaLicenciatura.id_licenciatura', $request->input('id_licenciatura'));

        $opciones = Especialidad::where('id_licenciatura', $id_licenciatura)
            ->pluck('especialidad', 'id');

        return view('elements.opciones', compact('opciones'));
    }

    public function modalidades(Request $request)
    {
        $id_licenciatura = $request->input('EscuelaLicenciatura.id_licenciatura', $request->input('id_licenciatura'));

        $opciones = ['e' => 'Escolarizada'];

        if (in_array($id_licenciatura, [6, 11, 14])) {
            $opciones['m'] = 'mixta';
        }

        return view('elements.opciones', compact('opciones'));
    }

    public function statusUsuario(Request $request, $id_usuario = null, $status = null)
    {
        $nivel = $this->contexto()['nivel'];
        if ($nivel != 1) {
            return redirect()->route('login');
        }

        if ($id_usuario) {
            $usuario = Usuario::find($id_usuario);

            if ($usuario) {
                if (!$status) {
                    $status = ($usuario->status === 'a') ? 'x' : 'a';
                }
                $usuario->update(['status' => $status]);
            }
        }

        return redirect()->back();
    }

    public function nombreUsuario(Request $request)
    {
        $nivel = $this->contexto()['nivel'];
        if ($nivel != 1) {
            return redirect()->route('login');
        }

        if ($request->isMethod('post') || $request->isMethod('put')) {
            $id     = $request->input('Usuario.id', $request->input('id'));
            $nombre = $request->input('Usuario.nombre', $request->input('nombre'));

            if ($id && $nombre) {
                $usuario = Usuario::find($id);
                if ($usuario) {
                    $usuario->update([
                        'nombre'             => $nombre,
                        'correo_electronico' => $request->input('Usuario.correo_electronico', $request->input('correo_electronico')),
                    ]);
                }
            }
        }

        return redirect()->back();
    }

    public function listaUsuarios(Request $request, $id_renapo = 'DF')
    {
        $nivel = $this->contexto()['nivel'];
        if ($nivel != 1) {
            return redirect()->route('login');
        }

        $modulo = $this->contexto()['modulo'];

        $entidades = Entidad::pluck('entidad', 'id_renapo');

        $query = Usuario::where('modulo', $modulo)->orderBy('id_usuario');

        if ($id_renapo === 'NE') {
            $query->whereIn('nivel', [2]);
        } else {
            $query->whereIn('nivel', [2, 3, 4])->where('id_renapo', $id_renapo);
        }

        $usuarios = $query->get();

        return view('siben.lista_usuarios', compact('usuarios', 'entidades'));
    }

    public function cambiaCaptura(Request $request)
    {
        $data = $request->validate([
            'id_escuela' => ['required', 'integer'],
            'id_entidad' => ['required', 'integer'],
            'modulo'     => ['required', 'in:l,p'],
        ]);

        $escuela = Escuela2::query()
            ->select('id', 'captura')
            ->where('id', $data['id_escuela'])
            ->where('id_entidad', $data['id_entidad'])
            ->where('modulo', $data['modulo'])
            ->whereIn('unica', [0, 1])
            ->where('status', 'a')
            ->first();

        if (!$escuela) {
            return redirect()
                ->route('siben.captura', ['id_entidad' => $data['id_entidad'], 'modulo' => $data['modulo']])
                ->with('error', 'No se encontró la escuela solicitada para el módulo y entidad seleccionados.');
        }

        $status = EscuelaStatus::query()
            ->where('id_escuela', $escuela->id)
            ->where('modulo', $data['modulo'])
            ->first();

        if (!$status) {
            return redirect()
                ->route('siben.captura', ['id_entidad' => $data['id_entidad'], 'modulo' => $data['modulo']])
                ->with('error', 'No existe registro de estado para la escuela seleccionada.');
        }

        $nuevaCaptura = ((string) $status->captura === '1') ? '0' : '1';
        $status->captura = $nuevaCaptura;
        $status->save();

        $accion = $nuevaCaptura === '1' ? 'abierta' : 'cerrada';

        return redirect()
            ->route('siben.captura', ['id_entidad' => $data['id_entidad'], 'modulo' => $data['modulo']])
            ->with('ok', "Captura {$accion} correctamente.");
    }

    public function captura(Request $request, $id_entidad = null, $modulo = null)
    {
        $nivel = $this->contexto()['nivel'];
        if ($nivel != 1) {
            return redirect()->route('login');
        }

        $id_entidad = $id_entidad ?? $request->integer('id_entidad');
        $modulo = $modulo ?? $request->input('modulo');

        if (!in_array($modulo, ['l', 'p'], true)) {
            $modulo = null;
        }

        $entidades = Entidad::query()
            ->where('id', '<', 33)
            ->orderBy('id')
            ->pluck('entidad', 'id');

        $escuelas = collect();

        if ($id_entidad && $modulo) {
            $escuelas = Escuela2::query()
                ->select('id', 'escuela_key', 'escuela', 'captura', 'modulo', 'id_entidad')
                ->where('id_entidad', $id_entidad)
                ->whereIn('unica', [0, 1])
                ->where('modulo', $modulo)
                ->where('status', 'a')
                ->orderBy('escuela')
                ->get();
        }

        return view('siben.captura', compact(
            'escuelas',
            'entidades',
            'id_entidad',
            'modulo'
        ));
    }

    public function imagenEscuela(Request $request)
    {
        $nivel = $this->contexto()['nivel'];
        if ($nivel != 4) {
            return redirect()->route('siben.index');
        }

        $id_escuela  = $this->contexto()['id_escuela'];
        $escuela_key = Escuela2::where('id', $id_escuela)->value('escuela_key');

        $imagenes = [];
        $indice   = 1;
        $id       = 1;

        for ($i = 6; $i >= 1; $i--) {
            $rutaImagen = public_path("img/escuelas/big/{$escuela_key}_{$i}.jpg");

            if (file_exists($rutaImagen)) {
                $imagenes[$i] = $i;
                $indice++;
            } else {
                $imagenes[$i] = 0;
                $id = $i;
            }
        }

        $subir    = ($indice <= 6);
        $anterior = 'datosEscuela';

        ksort($imagenes);

        return view('siben.imagen_escuela', compact(
            'subir',
            'escuela_key',
            'anterior',
            'imagenes',
            'id'
        ));
    }

    public function subeImagen(Request $request, $id)
    {
        if ($request->hasFile('imagen') || $request->isMethod('post')) {
            $id_escuela  = $this->contexto()['id_escuela'];
            $escuela_key = Escuela2::where('id', $id_escuela)->value('escuela_key');

            if ($request->hasFile('imagen') && $escuela_key) {
                $file          = $request->file('imagen');
                $nombreArchivo = "{$escuela_key}_{$id}.jpg";

                $file->move(public_path('img/escuelas/big'), $nombreArchivo);
            }
        }

        return redirect()->route('siben.imagenEscuela');
    }

    public function borraImagen(Request $request, $id)
    {
        $id_escuela  = $this->contexto()['id_escuela'];
        $escuela_key = Escuela2::where('id', $id_escuela)->value('escuela_key');

        if ($escuela_key) {
            $rutaImagenBig = public_path("img/escuelas/big/{$escuela_key}_{$id}.jpg");
            if (File::exists($rutaImagenBig)) {
                File::delete($rutaImagenBig);
            }
        }

        return redirect()->route('siben.imagenEscuela');
    }

    public function curpAlumno(Request $request, $id_alumno = null)
    {
        $id_escuela = $this->contexto()['id_escuela'];

        $alumno = Alumno::select('id', 'nombre', 'apellido_paterno', 'apellido_materno', 'curp')
            ->where('id', $id_alumno)
            ->firstOrFail();

        if ($request->isMethod('post') || $request->isMethod('put')) {
            $request->validate(['curp' => 'required|string|size:18']);
            $alumno->update(['curp' => strtoupper($request->input('curp'))]);

            return redirect()->back()->with('success', 'La CURP ha sido actualizada correctamente.');
        }

        return view('alumnos.curp_emergencia', compact('alumno'));
    }

    public function listaAlumnos(Request $request, $semestre = null)
    {
        $id_escuela = $this->contexto()['id_escuela'];

        $alumnos = collect();

        if ($semestre) {
            $alumnos = Alumno::select([
                'id',
                'curp',
                'nombre',
                'apellido_paterno',
                'apellido_materno',
                'semestre',
                'id_licenciatura',
                'status',
            ])
                ->where('id_escuela', $id_escuela)
                ->where('status', '!=', 'e')
                ->where('semestre', $semestre)
                ->orderBy('semestre', 'asc')
                ->orderBy('apellido_paterno', 'asc')
                ->orderBy('apellido_materno', 'asc')
                ->orderBy('nombre', 'asc')
                ->get();
        }

        $semestres = ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11'];

        return view('alumnos.lista', compact(
            'alumnos',
            'semestres',
            'semestre'
        ));
    }

    public function pruebaVerhoeff(Request $request)
    {
        $idCalculado = null;
        $valido      = null;

        if ($request->has('Alumno.id')) {
            $id       = $request->input('Alumno.id');
            $longitud = strlen($id);

            if ($longitud == 7) {
                $idCalculado = $id . Alumno::calculaVerhoeff($id);
            } elseif ($longitud == 8) {
                $valido = Alumno::validoVerhoeff($id);
            }
        }

        return view('alumnos.prueba_verhoeff', compact(
            'idCalculado',
            'valido'
        ));
    }

    public function editaAlumno()
    {
        $nivel = $this->contexto()['nivel'];

        if ($nivel != 1) {
            return redirect()->route('siben.index');
        }

        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');

        $semestres = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12, 13 => 13];

        return view('alumnos.edita_alumno', compact(
            'semestres',
            'entidades'
        ));
    }

    public function escuelas(Request $request)
    {
        $id_entidad = $request->input('Escuela.id_entidad', $request->input('id_entidad'));

        $modulo = $this->contexto()['modulo'];

        $opciones = Escuela2::where('status', 'a')->where('unica', '<', 2)->where('id_entidad', $id_entidad)->where('modulo', $modulo)->pluck('escuela', 'id');

        return view('elements.opciones', compact('opciones'));
    }

    public function localidades(Request $request)
    {
        $id_municipio = $request->input('Escuela.id_municipio', $request->input('id_municipio'));

        $opciones = Localidad::where('id_municipio', $id_municipio)->orderBy('localidad')->pluck('localidad', 'id');

        return view('elements.opciones', compact('opciones'));
    }

    public function alumnos(Request $request)
    {
        $inputAlumnos = $request->input('Alumno', $request->input('data.Alumno', []));
        $condiciones  = [];
        foreach ($inputAlumnos as $clave => $entrada) {
            if (!empty($entrada)) {
                $condiciones[$clave] = $entrada;
            }
        }
        $listaAlumnos = [];
        $cabeceras    = ['No.', 'ID', 'Nombre', 'Primer Apellido', 'Segundo Apellido', 'Curp', 'Acción'];
        if (!empty($condiciones)) {
            $condiciones['status'] = 'i';

            $alumnos = Alumno::select('id', 'nombre', 'apellido_paterno', 'apellido_materno', 'curp')->where($condiciones)->get();

            $detalleUrl = route('consultasHistoricos.detalleAlumno');

            foreach ($alumnos as $alumno) {
                $parametros = "data[Alumno2][nid]=" . $alumno->id;

                $evento = "consultaAjax('{$detalleUrl}', '{$parametros}', muestraOverlay)";

                $boton = '<input id="' . $alumno->id . '" type="button" value="Detalle" onclick="' . $evento . '" class="boton__primario" style="padding: 4px 10px; cursor: pointer;" />';

                $listaAlumnos[] = [
                    'id'               => $alumno->id,
                    'nombre'           => $alumno->nombre,
                    'apellido_paterno' => $alumno->apellido_paterno,
                    'apellido_materno' => $alumno->apellido_materno,
                    'curp'             => $alumno->curp,
                    'accion'           => $boton,
                ];
            }
        }
        return view('elements.tablaAjax', compact(
            'listaAlumnos',
            'cabeceras'
        ));
    }

    public function editaDomicilio()
    {
        $ctx = $this->contexto();

        $id_escuela = $ctx['id_escuela'];
        $id_entidad = $ctx['id_entidad'];

        $domicilioEscuela = DomicilioEscuela::where('id_escuela', $id_escuela)->first();
        $id_municipio     = $domicilioEscuela ? $domicilioEscuela->cve_mun : null;

        $vialidad = [
            'AMPLIACION' => 'AMPLIACION', 'ANDADOR' => 'ANDADOR', 'AVENIDA' => 'AVENIDA',
            'BOULEVARD' => 'BOULEVARD', 'CALLE' => 'CALLE', 'CALLEJON' => 'CALLEJON',
            'CALZADA' => 'CALZADA', 'CAMINO' => 'CAMINO', 'CARRETERA' => 'CARRETERA',
            'CERRADA' => 'CERRADA', 'CIRCUITO' => 'CIRCUITO', 'CIRCUNVALACION' => 'CIRCUNVALACION',
            'CONTINUACION' => 'CONTINUACION', 'CORREDOR' => 'CORREDOR', 'DIAGONAL' => 'DIAGONAL',
            'EJE VIAL' => 'EJE VIAL', 'PASAJE' => 'PASAJE', 'PEATONAL' => 'PEATONAL',
            'PERIFERICO' => 'PERIFERICO', 'PRIVADA' => 'PRIVADA', 'PROLONGACION' => 'PROLONGACION',
            'RETORNO' => 'RETORNO', 'VIADUCTO' => 'VIADUCTO',
        ];

        $asentamientos = [
            'AEROPUERTO' => 'AEROPUERTO', 'AMPLIACION' => 'AMPLIACION', 'BARRIO' => 'BARRIO',
            'CANTON' => 'CANTON', 'CIUDAD' => 'CIUDAD', 'CIUDAD INDUSTRIAL' => 'CIUDAD INDUSTRIAL',
            'COLONIA' => 'COLONIA', 'CONDOMINIO' => 'CONDOMINIO', 'CONJUNTO HABITACIONAL' => 'CONJUNTO HABITACIONAL',
            'CORREDOR INDUSTRIAL' => 'CORREDOR INDUSTRIAL', 'COTO' => 'COTO', 'CUARTEL' => 'CUARTEL',
            'EJIDO' => 'EJIDO', 'EXHACIENDA' => 'EXHACIENDA', 'FRACCION' => 'FRACCION',
            'FRACCIONAMIENTO' => 'FRACCIONAMIENTO', 'GRANJA' => 'GRANJA', 'HACIENDA' => 'HACIENDA',
            'INGENIO' => 'INGENIO', 'MANZANA' => 'MANZANA', 'PARAJE' => 'PARAJE', 'PARQUE INDUSTRIAL' => 'PARQUE INDUSTRIAL',
            'PRIVADA' => 'PRIVADA', 'PROLONGACION' => 'PROLONGACION', 'PUEBLO' => 'PUEBLO', 'PUERTO' => 'PUERTO',
            'RANCHERIA' => 'RANCHERIA', 'RANCHO' => 'RANCHO', 'REGION' => 'REGION', 'RESIDENCIAL' => 'RESIDENCIAL',
            'RINCONADA' => 'RINCONADA', 'SECCION' => 'SECCION', 'SECTOR' => 'SECTOR', 'SUPERMANZANA' => 'SUPERMANZANA',
            'UNIDAD' => 'UNIDAD', 'UNIDAD HABITACIONAL' => 'UNIDAD HABITACIONAL', 'VILLA' => 'VILLA',
            'ZONA FEDERAL' => 'ZONA FEDERAL', 'ZONA INDUSTRIAL' => 'ZONA INDUSTRIAL', 'ZONA MILITAR' => 'ZONA MILITAR',
        ];

        $admon   = ['ESTATAL' => 'ESTATAL', 'FEDERAL' => 'FEDERAL'];
        $derecho = ['DE CUOTA' => 'DE CUOTA', 'LIBRE' => 'LIBRE'];
        $admon2  = ['BRECHA' => 'BRECHA', 'CAMINO' => 'CAMINO', 'TERRACERIA' => 'TERRACERIA', 'VEREDA' => 'VEREDA'];
        $margen  = ['DERECHO' => 'DERECHO', 'IZQUIERDO' => 'IZQUIERDO'];

        $municipios = Municipio::where('id_entidad', $id_entidad)
            ->orderBy('municipio')
            ->pluck('municipio', 'id_municipio');

        $locs = collect();
        if ($id_municipio) {
            $locs = Localidad::where('id_entidad', $id_entidad)
                ->where('id_municipio', $id_municipio)
                ->orderBy('localidad')
                ->pluck('localidad', 'id_localidad');
        }

        $seguridad_social = SeguridadSocial::pluck('regimen', 'id');

        return view('siben.edita_domicilio', compact(
            'domicilioEscuela',
            'municipios',
            'asentamientos',
            'locs',
            'vialidad',
            'admon',
            'admon2',
            'derecho',
            'margen',
            'id_entidad',
            'seguridad_social'
        ));
    }

    public function domicilioEscuela()
    {
        $ctx = $this->contexto();

        $modulo     = $ctx['modulo'];
        $nivel      = $ctx['nivel'];
        $id_escuela = $ctx['id_escuela'];

        if ($modulo != 'l') {
            return redirect()->route('siben.index');
        }

        $title = 'Domicilio Geográfico';

        $domicilioEscuela = DomicilioEscuela::where('id_escuela', $id_escuela)->first();

        $localidad        = null;
        $seguridad_social = null;

        if ($domicilioEscuela) {
            $localidad = Localidad::where('id_localidad', $domicilioEscuela->cve_loc)
                ->where('id_municipio', $domicilioEscuela->cve_mun)
                ->where('id_entidad', $domicilioEscuela->cve_ent)
                ->first();

            $seguridad_social = SeguridadSocial::find($domicilioEscuela->id_seguridad_social);
        }
        $botones           = [];
        $urlEscuela        = route('siben.editaEscuela');
        $parametrosEscuela = "data[Escuela][id]={$id_escuela}";
        $eventosEscuela    = "consultaAjax('{$urlEscuela}', '{$parametrosEscuela}', muestraOverlay, muestraOcupado)";

        switch ($nivel) {
            case 3:
                $botones['escuela'] = "<input id='e_{$id_escuela}' type='button' value='Editar' onclick=\"{$eventosEscuela}\" class='boton__primario' style='padding: 6px 14px; cursor: pointer;' />";
                break;
            case 4:
                $botones['escuela'] = '';
                break;
        }

        $anterior = true;

        return view('siben.domicilio_escuela', compact(
            'title',
            'domicilioEscuela',
            'localidad',
            'seguridad_social',
            'botones',
            'anterior'
        ));
    }

    public function loc(Request $request)
    {
        $id_entidad   = $this->contexto()['id_entidad'];
        $id_municipio = $request->input('DomicilioEscuela.cve_mun', $request->input('cve_mun'));

        $opciones = Localidad::where('id_entidad', $id_entidad)->where('id_municipio', $id_municipio)->orderBy('localidad')->pluck('localidad', 'id_localidad');

        return view('elements.opciones2', compact('opciones'));
    }

    public function guardaDomicilio(Request $request)
    {
        $ctx = $this->contexto();

        $id_escuela = $ctx['id_escuela'];
        $id_entidad = $ctx['id_entidad'];
        $modulo     = $ctx['modulo'];

        $data = $request->input('DomicilioEscuela', $request->input('data.DomicilioEscuela', []));

        $data['id_escuela'] = $id_escuela;
        $data['cve_ent']    = $id_entidad;

        EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->update(['domicilio' => 1]);

        $vialidad = $data['tipovial'] ?? null;

        if ($vialidad === 'CAMINO') {
            $admon  = $data['admon'] ?? '';
            $derecho = $data['derecho'] ?? '';
            $codigo = $data['codigo'] ?? '';
            $tramo  = $data['tramo'] ?? '';
            $cadena = $data['cadena'] ?? '';

            if ($codigo != '' && $tramo != '' && $cadena != '') {
                $data['nomvial'] = trim("{$admon} {$derecho} {$codigo} TRAMO {$tramo} KILOMETRO {$cadena}");
            }
        } elseif ($vialidad === 'CAMINO') {
            $admon2  = $data['admon2'] ?? '';
            $tramo2  = $data['tramo2'] ?? '';
            $margen  = $data['margen'] ?? '';
            $cadena2 = $data['cadena2'] ?? '';
            if ($tramo2 != '' && $cadena2 != '') {
                $data['nomvial'] = trim("{$admon2} {$tramo2} MARGEN {$margen} KILOMETRO {$cadena2}");
            }
        }
        try {
            DomicilioEscuela::updateOrCreate(
                ['id_escuela' => $id_escuela],
                $data
            );
        } catch (\Exception $e) {
            return response('Error: no se guardo la informacion', 400);
        }
        return redirect()->route('siben.editaDomicilio');
    }

    public function eligeTipo()
    {
        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');

        $modulo = [
            'l' => 'licenciatura',
            'p' => 'posgrado',
        ];
        $anterior = true;

        return view('siben.elige_tipo', compact(
            'modulo',
            'entidades',
            'anterior'
        ));
    }

    public function listaEscuelas(Request $request)
    {
        $ctx   = $this->contexto();
        $nivel = $ctx['nivel'];

        if ($nivel != 1) {
            return redirect()->route('logout');
        }

        $id_entidad = $request->input('Escuela.id_entidad', $request->input('id_entidad'));
        $modulo     = $request->input('Escuela.modulo', $request->input('modulo'));

        $escuelas = Escuela2::select('id', 'escuela', 'escuela_key', 'modulo', 'captura')
            ->where('id_entidad', $id_entidad)
            ->whereIn('unica', ['0', '1'])
            ->where('modulo', $modulo)
            ->where('status', 'a')
            ->orderBy('id')
            ->orderBy('escuela')
            ->get();
        $cabeceras = ['No.', 'Escuela', 'Clave', 'modulo', 'Status'];
        $renglones = [];

        foreach ($escuelas as $escuela) {
            $titulo = ($escuela->captura == '1') ? 'Cerrar' : 'Abrir';

            $urlCambiaCaptura = route('siben.cambiaCaptura', [
                'id'      => $escuela->id,
                'captura' => $escuela->captura,
                'modulo'  => $escuela->modulo,
            ]);
            $evento = "locacion('{$urlCambiaCaptura}')";
            $boton  = '<input type="button" id="b_' . $escuela->id . '" onclick="' . $evento . '" value="' . $titulo . '" class="boton__primario" style="padding: 4px 10px; cursor: pointer;" />';

            $renglones[$escuela->id] = [
                'escuela'     => $escuela->escuela,
                'escuela_key' => $escuela->escuela_key,
                'modulo'      => $escuela->modulo,
                'captura'     => $boton,
            ];
        }
        return view('elements.tablaAjax', compact(
            'cabeceras',
            'renglones'
        ));
    }

    public function directoresEscuela(Request $request, $opc = null)
    {
        $ctx = $this->contexto();

        $modulo        = $ctx['modulo'];
        $nivel         = $ctx['nivel'];
        $id_escuela    = $ctx['id_escuela'];
        $nombreEscuela = session('Usuario.escuela');

        if ($modulo != 'l') {
            return redirect()->route('siben.index');
        }

        $title = 'Datos Generales, ' . $nombreEscuela;

        $cuentaDirector = $ctx['cuenta_director'] ?? 0;
        $mensaje        = '';
        if ($cuentaDirector >= 2) {
            $mensaje = '.:: Solo debe haber un director por escuela, elimine los directores no activos ::.';
        }

        $escuela = Escuela::find($id_escuela);

        $directivos = Personal::with('funcion:id,funcion')
            ->where('id_escuela', $id_escuela)
            ->where('id_funcion', 1)
            ->where('status', 'a')
            ->orderBy('id_funcion')
            ->get(['id', 'nombre', 'apellido_paterno', 'apellido_materno', 'correo_electronico', 'id_funcion']);

        $licenciaturas = EscuelaLicenciatura::with([
            'licenciatura:id,licenciatura',
            'personal:id,nombre,apellido_paterno,apellido_materno,correo_electronico',
        ])
            ->where('id_escuela', $id_escuela)
            ->groupBy('id_licenciatura')
            ->get();

        $urlCoordinadores = route('siben.editaCoordinadores');
        $urlDirector      = route('siben.busquedaDirector');

        $parametrosCoordinadores = "data[Escuela][id]={$id_escuela}";
        $eventoCoordinadores     = "consultaAjax('{$urlCoordinadores}', '{$parametrosCoordinadores}', muestraOverlay, muestraOcupado)";

        $botones = [];
        switch ($nivel) {
            case 3:
                $botones['coordinadores'] = "<input id='c_{$id_escuela}' type='button' value='Editar' onclick=\"{$eventoCoordinadores}\" class='boton__primario' style='padding: 6px 14px; cursor: pointer;' />";
                $botones['director']      = "<input id='c_{$id_escuela}' type='button' value='Activar director' onclick=\"window.location.href='{$urlDirector}'\" class='boton__secundario' style='padding: 6px 14px; cursor: pointer;' />";
                break;
            case 4:
                $botones['coordinadores'] = '';
                $botones['director']      = '';
                break;
        }

        $anterior = false;
        if ($opc == 'e') {
            $eventoAtras    = "locacion('" . route('siben.matricula') . "')";
            $eventoAdelante = "locacion('" . route('siben.editaDomicilio') . "')";

            $botones['atras']    = "<input type='button' value='Atras' onclick=\"{$eventoAtras}\" class='boton__secundario' style='padding: 6px 14px; cursor: pointer;' />";
            $botones['adelante'] = "<input type='button' value='Siguiente' onclick=\"{$eventoAdelante}\" class='boton__primario' style='padding: 6px 14px; cursor: pointer;' />";
        } else {
            $botones['adelante'] = '';
            $botones['atras']    = '';
            $anterior            = true;
        }

        return view('siben.directores_escuela', compact(
            'title',
            'escuela',
            'mensaje',
            'directivos',
            'licenciaturas',
            'botones',
            'anterior'
        ));
    }

    public function matricula(Request $request)
    {
        $ctx = $this->contexto();

        $nivel   = $ctx['nivel'];
        $captura = $ctx['captura'];

        if ($nivel != 3 || $captura != 1) {
            return redirect()->route('siben.index');
        }
        $id_escuela  = $ctx['id_escuela'];
        $modulo      = $ctx['modulo'];
        $escuela     = session('Usuario.escuela');
        $cuentaClave = session('Usuario.cuenta_clave', 0);

        $title = 'Edición Datos Generales, ' . $escuela;

        if ($request->isMethod('post') || $request->isMethod('put')) {
            $capturista = Usuario::where('id_escuela', $id_escuela)->where('modulo', $modulo)->where('nivel', '4')->first();

            $status = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();

            if ($status) {
                $datosStatus  = $request->input('EscuelaStatus', $request->input('data.EscuelaStatus', []));
                $statusUpdate = Arr::only($datosStatus, ['cantidad_alumnos', 'cantidad_personal']);

                if (!empty($statusUpdate)) {
                    $status->update($statusUpdate);
                }
            }
        }
        $capturista = Usuario::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();
        $status     = EscuelaStatus::where('id_escuela', $id_escuela)->where('modulo', $modulo)->first();

        $mensaje = ($cuentaClave > 0) ? 'Por favor, ingrese una clave de centro de trabajo a cada entrada' : '';

        return view('siben.matricula', compact(
            'title',
            'capturista',
            'status',
            'mensaje'
        ));
    }

    public function eliminaDirectivo($id = null)
    {
        if ($id) {
            $id_escuela = $this->contexto()['id_escuela'];

            $personal = Personal::where('id', $id)
                ->where('id_escuela', $id_escuela)
                ->first();

            if ($personal) {
                try {
                    $personal->update(['status' => 'x']);
                } catch (\Exception $e) {
                    return response('Error: Inténtelo de nuevo, por favor', 400);
                }
            }
        }

        return redirect()->route('siben.directoresEscuela', ['opc' => 'e']);
    }

    public function estructuraEscuela(Request $request, $opc = null)
    {
        $ctx = $this->contexto();

        $modulo            = $ctx['modulo'];
        $nivel             = $ctx['nivel'];
        $id_escuela        = $ctx['id_escuela'];
        $nombreEscuela     = session('Usuario.escuela');
        $estado_estructura = session('Usuario.estructura');

        if ($modulo != 'l') {
            return redirect()->route('siben.index');
        }

        $title = 'Datos Generales, ' . $nombreEscuela;

        $escuela       = Escuela::with('caracteristicaEscuela')->find($id_escuela);
        $escuela_key   = $escuela ? $escuela->escuela_key : '';
        $estructura    = $escuela ? $escuela->caracteristicaEscuela : null;
        $id_estructura = $estructura ? $estructura->id : null;

        $mensaje = '';

        $licenciaturas = EscuelaLicenciatura::with([
            'licenciatura:id,licenciatura',
            'personal:id,nombre,apellido_paterno,apellido_materno,correo_electronico',
        ])
            ->where('id_escuela', $id_escuela)
            ->groupBy('id_licenciatura')
            ->get();

        $turnos         = ['m' => 'Matutino', 'v' => 'Vespertino', 'd' => 'Discontinuo', 'n' => 'Nocturno'];
        $sostenimientos = ['p' => 'Público', 'q' => 'Particular'];
        $posgrados      = ['m' => 'Maestría', 'd' => 'Doctorado', 'e' => 'Especialidad', 'c' => 'Educación Continua', 'i' => 'SEPA Inglés'];
        $bandaAncha     = ['s' => 'Banda Ancha', 'n' => '56Kbps'];
        $sino           = ['s' => 'Sí', 'n' => 'No'];

        $tiposPropiedad  = Construccion::where('id', '<', 20)->pluck('construccion', 'id');
        $tiposEstructura = Construccion::where('id', '>', 20)->pluck('construccion', 'id');
        $localidad       = null;
        if ($escuela && $escuela->id_localidad) {
            $localidad = Localidad::find($escuela->id_localidad);
        }

        $imagenes = [];
        for ($i = 1; $i <= 6; $i++) {
            $pathImagen   = public_path("img/escuelas/big/{$escuela_key}_{$i}.jpg");
            $imagenes[$i] = file_exists($pathImagen) ? $i : 0;
        }

        $urlEscuela    = route('siben.editaEscuela');
        $urlEstructura = route('siben.editaEstructura');
        $urlImagenes   = route('siben.imagenEscuela');

        $paramEscuela    = "data[Escuela][id]={$id_escuela}";
        $paramEstructura = "data[CaracteristicaEscuela][id]={$id_estructura}";

        $eventoEscuela    = "consultaAjax('{$urlEscuela}', '{$paramEscuela}', muestraOverlay, muestraOcupado)";
        $eventoEstructura = "consultaAjax('{$urlEstructura}', '{$paramEstructura}', muestraOverlay, muestraOcupado)";

        $botones = [];
        switch ($nivel) {
            case 3:
                $botones['escuela']    = "<input id='e_{$id_escuela}' type='button' value='Editar' onclick=\"{$eventoEscuela}\" class='boton__primario' style='padding: 6px 14px; cursor: pointer;' />";
                $botones['estructura'] = "<input id='s_{$id_estructura}' type='button' value='Editar' onclick=\"{$eventoEstructura}\" class='boton__primario' style='padding: 6px 14px; cursor: pointer;' />";
                $botones['imagenes']   = '';
                break;
            case 4:
                $botones['escuela']    = '';
                $botones['estructura'] = '';
                $botones['imagenes']   = "<input id='f_{$id_escuela}' type='button' value='Subir fotos' onclick=\"locacion('{$urlImagenes}')\" class='boton__primario' style='padding: 6px 14px; cursor: pointer;' />";
                break;
        }

        $anterior = false;
        if ($opc == 'e') {
            $eventoAtras     = "locacion('" . route('siben.editaDomicilio') . "')";
            $eventoSiguiente = "locacion('" . route('siben.datosEscuela') . "')";
            $eventoDomicilio = "locacion('" . route('siben.index') . "')";

            $botones['atras']     = "<input type='button' value='Anterior' onclick=\"{$eventoAtras}\" class='boton__secundario' style='padding: 6px 14px; cursor: pointer;' />";
            $botones['siguiente'] = "<input type='button' value='Siguiente' onclick=\"{$eventoSiguiente}\" class='boton__primario' style='padding: 6px 14px; cursor: pointer;' />";
            $botones['terminar']  = "<input type='button' value='Terminar' onclick=\"{$eventoDomicilio}\" class='boton__secundario' style='padding: 6px 14px; cursor: pointer;' />";
        } else {
            $botones['terminar']  = '';
            $botones['atras']     = '';
            $botones['siguiente'] = '';
            $anterior             = true;
        }

        return view('siben.estructura_escuela', compact(
            'title',
            'escuela',
            'escuela_key',
            'estado_estructura',
            'estructura',
            'mensaje',
            'localidad',
            'licenciaturas',
            'turnos',
            'sostenimientos',
            'posgrados',
            'bandaAncha',
            'sino',
            'tiposPropiedad',
            'tiposEstructura',
            'imagenes',
            'botones',
            'anterior'
        ));
    }

    public function busquedaDirector()
    {
        $ctx        = $this->contexto();
        $id_entidad = $ctx['id_entidad'];

        $escuelaEstado = Escuela2::where('status', 'a')
            ->where('unica', '<', 2)
            ->where('id_entidad', $id_entidad)
            ->where('captura', '1')
            ->pluck('escuela', 'id');

        $tituloTabla = [
            'd' => 'Para la baja definitivas',
            't' => 'Para las bajas temporales',
        ];
        $tituloPag = [
            'd' => 'DEFINITIVAS',
            't' => 'TEMPORALES',
        ];

        $url      = route('siben.listasDirector');
        $anterior = true;

        return view('siben.busqueda_director', compact(
            'url',
            'tituloTabla',
            'tituloPag',
            'escuelaEstado',
            'anterior'
        ));
    }

    public function listasDirector(Request $request)
    {
        $ctx   = $this->contexto();
        $nivel = $ctx['nivel'];

        $id_escuela = ($nivel == 3) ? $ctx['id_escuela'] : $request->input('Busca.id_escuela', $ctx['id_escuela']);

        $busca = $request->input('Busca', $request->input('data.Busca', []));

        $contadorVacios = 0;
        foreach ($busca as $valor) {
            if (empty($valor)) {
                $contadorVacios++;
            }
        }

        $renglones = [];
        $cabeceras = ['No.', 'Primer Apellido', 'Segundo Apellido', 'Nombre', 'CURP', 'Estado', 'Acción'];

        if (!empty($busca) && $contadorVacios < 5) {
            $query = Personal::select('id', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp', 'status');

            if ($id_escuela) {
                $query->where('id_escuela', $id_escuela);
            }

            foreach ($busca as $campo => $valor) {
                if (!empty($valor)) {
                    $query->where($campo, $valor);
                }
            }

            $personal = $query->orderBy('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre')
                ->get();

            foreach ($personal as $persona) {
                $urlGuardaDirector = route('siben.guardaDirector', ['id' => $persona->id]);
                $parametros        = "data[Personal][id]={$persona->id}";
                $evento            = "locacion('{$urlGuardaDirector}', '{$parametros}', muestraOverlay, muestraOcupado);";

                $boton = '<input id="b_' . $persona->id . '" type="button" value="Activar" onclick="' . $evento . '" class="boton__primario" style="padding: 4px 10px; cursor: pointer;" />';

                $estilo = '';
                $estado = '';
                switch ($persona->status) {
                    case 'x':
                        $estilo = 'text-align:center; background-color: var(--error, #dc3545); color: #fff; font-weight: bold; padding: 4px; border-radius: 4px;';
                        $estado = 'Inactivo';
                        break;
                    case 'a':
                        $estilo = 'text-align:center; background-color: var(--exito, #28a745); color: #fff; font-weight: bold; padding: 4px; border-radius: 4px;';
                        $estado = 'Activo';
                        break;
                }

                $statusHtml = "<div style='{$estilo}'>{$estado}</div>";

                $renglones[$persona->id] = [
                    'apellido_paterno' => $persona->apellido_paterno,
                    'apellido_materno' => $persona->apellido_materno,
                    'nombre'           => $persona->nombre,
                    'curp'             => $persona->curp,
                    'status'           => $statusHtml,
                    'accion'           => $boton,
                ];
            }
        }

        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }

    public function guardaDirector(Request $request, $id = null)
    {
        if ($id) {
            $id_escuela = $this->contexto()['id_escuela'];

            $personal = Personal::where('id', $id)->where('id_escuela', $id_escuela)->first();

            if ($personal) {
                try {
                    $datosActualizar = [
                        'status'     => 'a',
                        'id_funcion' => '1',
                    ];
                    $datosFormulario = $request->input('Personal', $request->input('data.Personal', []));
                    if (!empty($datosFormulario)) {
                        $datosActualizar = array_merge($datosFormulario, $datosActualizar);
                    }
                    $personal->update($datosActualizar);
                } catch (\Exception $e) {
                    return response('Error: Inténtelo de nuevo, por favor', 400);
                }
            }
        }
        return redirect()->route('siben.directoresEscuela', ['opc' => 'e']);
    }

    public function editaUsuario()
    {
        $id_usuario = session('Usuario.id');
        $usuario    = Usuario::find($id_usuario);
        $title      = 'Editar Información personal';

        return view('siben.edita_usuario', compact('title', 'usuario'));
    }

    public function guardaEnlace(Request $request)
    {
        $id_usuario = session('Usuario.id');
        $usuario    = Usuario::find($id_usuario);

        if (!$usuario) {
            return response('Error: no se guardó la información', 400);
        }

        $datos           = $request->input('Usuario', $request->input('data.Usuario', []));
        $datos['status'] = 'a';

        $datosUpdate = Arr::only($datos, ['nombre', 'correo_electronico', 'status']);

        try {
            $usuario->update($datosUpdate);
        } catch (\Exception $e) {
            return response('Error: no se guardó la información', 400);
        }

        return redirect()->to('/directorios/entidad');
    }

    public function menuDatos()
    {
        $ctx            = $this->contexto();
        $nivel          = $ctx['nivel'];
        $captura        = $ctx['captura'];
        $cuentaDirector = $ctx['cuenta_director'] ?? 0;

        if (!$captura) {
            $mensaje = 'La captura está finalizada';
        } else {
            $mensaje = ($cuentaDirector == 0) ? 'Por favor agregue o active al director de la escuela' : '';
        }

        if (in_array($nivel, [3, 4])) {
            $opcionesMenu = [
                ['siben', 'Domicilio', 'domicilioEscuela', 0],
                ['siben', 'Informacion adicional', 'datosEscuela', 0],
                ['siben', 'Infraestructura', 'estructuraEscuela/v', 0],
                ['siben', 'Directivos', 'directoresEscuela/v', 0],
            ];
        } else {
            return redirect('/');
        }

        return view('siben.menu_datos', compact('opcionesMenu', 'mensaje'));
    }

    public function editaAdicionales(Request $request)
    {
        $ctx                = $this->contexto();
        $id_escuela_session = $ctx['id_escuela'];
        $id_entidad         = $ctx['id_entidad'];

        $id_escuela_req = $request->input('Escuela.id', $request->input('data.Escuela.id'));

        if ($id_escuela_session != $id_escuela_req) {
            return redirect()->route('siben.index');
        }

        $escuela = Escuela::find($id_escuela_session);
        $turnos  = ['m' => 'Matutino', 'v' => 'Vespertino', 'd' => 'Discontinuo', 'n' => 'Nocturno'];

        $municipios = Municipio::where('id_entidad', $id_entidad)
            ->orderBy('municipio')
            ->pluck('municipio', 'id');

        $localidades = Localidad::where('id_municipio2', $escuela ? $escuela->id_municipio : null)
            ->orderBy('localidad')
            ->pluck('localidad', 'id');

        return view('siben.edita_adicionales', compact('escuela', 'turnos', 'municipios', 'localidades'));
    }

    public function directores()
    {
        $ctx   = $this->contexto();
        $nivel = $ctx['nivel'];

        $id_entidad = in_array($nivel, [2, 6]) ? $ctx['id_entidad'] : null;

        $escuelas_directores = DB::select("
            SELECT directores.id_escuela, escuelas2.escuela, personal2.nombre, 
                   personal2.apellido_paterno, personal2.apellido_materno, directores.curp
            FROM escuelas2 
            JOIN directores ON (escuelas2.id = directores.id_escuela)
            LEFT JOIN personal2 ON (directores.id_personal = personal2.id)
            WHERE escuelas2.unica < 2
              AND escuelas2.status = 'a'
              AND escuelas2.id_entidad = :id_entidad
            GROUP BY directores.id_escuela, escuelas2.escuela, personal2.nombre, 
                     personal2.apellido_paterno, personal2.apellido_materno, directores.curp
            ORDER BY directores.id_escuela
        ", ['id_entidad' => $id_entidad]);

        return view('siben.directores', compact('escuelas_directores', 'nivel'));
    }

    public function buscaDirector($id_escuela = null)
    {
        $id_entidad = $this->contexto()['id_entidad'];

        $escuelasEstado = Escuela2::where('status', 'a')
            ->where('unica', '<', 2)
            ->where('id_entidad', $id_entidad)
            ->where('id', $id_escuela)
            ->pluck('escuela', 'id');

        $url = route('siben.listasDirector2', ['id_escuela' => $id_escuela]);

        return view('siben.busca_director', compact('url', 'escuelasEstado'));
    }

    public function listasDirector2(Request $request, $id_escuela = null)
    {
        $busca = $request->input('Busca', $request->input('data.Busca', []));

        $contadorVacios = 0;
        foreach ($busca as $valor) {
            if (empty($valor)) {
                $contadorVacios++;
            }
        }

        $renglones = [];
        $cabeceras = ['No.', 'Primer Apellido', 'Segundo Apellido', 'Nombre', 'CURP', 'Estado', 'Acción'];

        if (!empty($busca) && $contadorVacios < 5) {
            $query = Personal::select('id', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp', 'status');

            foreach ($busca as $campo => $valor) {
                if (!empty($valor)) {
                    $query->where($campo, $valor);
                }
            }

            $personal = $query->orderBy('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre')
                ->get();

            foreach ($personal as $persona) {
                $urlGuarda = route('siben.guardaDirector2', [
                    'id'         => $persona->id,
                    'id_escuela' => $id_escuela,
                    'curp'       => $persona->curp,
                ]);
                $parametros = "data[Personal][id]={$persona->id}";
                $evento     = "locacion('{$urlGuarda}', '{$parametros}', muestraOverlay, muestraOcupado);";
                $boton      = '<input id="b_' . $persona->id . '" type="button" value="Activar" onclick="' . $evento . '" class="boton__primario" style="padding: 4px 10px; cursor: pointer;" />';

                $estilo = '';
                $estado = '';
                switch ($persona->status) {
                    case 'x':
                        $estilo = 'text-align:center; background-color: var(--error, #dc3545); color: #fff; font-weight: bold; padding: 4px; border-radius: 4px;';
                        $estado = 'Inactivo';
                        break;
                    case 'a':
                        $estilo = 'text-align:center; background-color: var(--exito, #28a745); color: #fff; font-weight: bold; padding: 4px; border-radius: 4px;';
                        $estado = 'Activo';
                        break;
                }

                $statusHtml = "<div style='{$estilo}'>{$estado}</div>";

                $renglones[$persona->id] = [
                    'apellido_paterno' => $persona->apellido_paterno,
                    'apellido_materno' => $persona->apellido_materno,
                    'nombre'           => $persona->nombre,
                    'curp'             => $persona->curp,
                    'status'           => $statusHtml,
                    'accion'           => $boton,
                ];
            }
        }

        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }

    public function guardaDirector2(Request $request, $id = null, $id_escuela = null, $curp = null)
    {
        if ($id && $id_escuela) {
            $personal = Personal::find($id);

            if ($personal) {
                try {
                    DB::table('directores')
                        ->where('id_escuela', $id_escuela)
                        ->update([
                            'id_personal' => $id,
                            'curp'        => $curp,
                        ]);

                    $datosPersonal = $request->input('Personal', $request->input('data.Personal', []));
                    if (!empty($datosPersonal)) {
                        $personal->update($datosPersonal);
                    }
                } catch (\Exception $e) {
                    return response('Error: Inténtelo de nuevo, por favor', 400);
                }
            }
        }

        return redirect()->route('siben.directores');
    }
}