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
                ['Consulta histórico', 'consultas_historico.admin.form'],
                ['Consulta personal', 'consultas_personal.formulario'],
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
                ['Consulta personal', 'consultas_personal.formulario'],
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
                ['Consulta personal', null],
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

        $modulo     = $ctx['modulo'];
        $nivel      = $ctx['nivel'];
        $id_escuela = $ctx['id_escuela'];
        $nombreEscuela = $ctx['escuela'];

        if ($modulo !== 'l' || !in_array($nivel, [3, 4], true)) {
            abort(403, 'No tienes permiso para consultar esta información.');
        }

        if (!$id_escuela) {
            abort(403, 'No tienes una escuela asignada.');
        }

        $escuela = Escuela::findOrFail($id_escuela);

        $title = 'Datos adicionales, ' . $nombreEscuela;

        $turnos = [
            'm' => 'Matutino',
            'v' => 'Vespertino',
            'd' => 'Discontinuo',
            'n' => 'Nocturno',
        ];

        $sostenimientos = [
            'p' => 'Público',
            'q' => 'Particular',
        ];

        $investigacion = [
            's' => 'Sí',
            'n' => 'No',
        ];

        return view('siben.datos_escuela', compact(
            'title',
            'escuela',
            'nivel',
            'turnos',
            'sostenimientos',
            'investigacion'
        ));
    }


    public function editaEscuela(Request $request)
    {
        $ctx = $this->contexto();

        $id_escuela = $ctx['id_escuela'];
        $id_entidad = $ctx['id_entidad'];
        $modulo     = $ctx['modulo'];
        $nivel      = $ctx['nivel'];

        // Solo se permite trabajar en el módulo de licenciaturas.
        if ($modulo !== 'l') {
            return redirect()->route('siben.index');
        }

        // Solamente el supervisor estatal (nivel 3) puede editar.
        if ((int) $nivel !== 3) {
            abort(403, 'No tienes permiso para editar el domicilio de la escuela.');
        }

        // Verificar que la escuela solicitada corresponda al usuario.
        $escuelaIdInput = $request->input(
            'data.Escuela.id',
            $request->input('id')
        );

        if (
            empty($id_escuela) ||
            empty($escuelaIdInput) ||
            (string) $id_escuela !== (string) $escuelaIdInput
        ) {
            return redirect()->route('siben.index');
        }

        // Consultar información de la escuela y su domicilio.
        $domicilioEscuela = DomicilioEscuela::where('id_escuela', $id_escuela)->first();

        $escuela = Escuela::find($id_escuela);

        if (!$escuela) {
            return redirect()->route('siben.index');
        }

        $id_municipio = $domicilioEscuela
            ? $domicilioEscuela->cve_mun
            : null;

        // Municipios y localidades.
        $municipios = Municipio::where('id_entidad', $id_entidad)
            ->orderBy('municipio')
            ->pluck('municipio', 'id_municipio');

        $locs = Localidad::where('id_entidad', $id_entidad)
            ->where('id_municipio', $id_municipio)
            ->pluck('localidad', 'id_localidad');

        // Turnos.
        $turnos = [
            'm' => 'Matutino',
            'v' => 'Vespertino',
            'd' => 'Discontinuo',
            'n' => 'Nocturno'
        ];

        // Tipos de vialidad.
        $vialidad = [
            'AMPLIACION' => 'AMPLIACION',
            'ANDADOR' => 'ANDADOR',
            'AVENIDA' => 'AVENIDA',
            'BOULEVARD' => 'BOULEVARD',
            'CALLE' => 'CALLE',
            'CALLEJON' => 'CALLEJON',
            'CALZADA' => 'CALZADA',
            'CAMINO' => 'CAMINO',
            'CARRETERA' => 'CARRETERA',
            'CERRADA' => 'CERRADA',
            'CIRCUITO' => 'CIRCUITO',
            'CIRCUNVALACION' => 'CIRCUNVALACION',
            'CONTINUACION' => 'CONTINUACION',
            'CORREDOR' => 'CORREDOR',
            'DIAGONAL' => 'DIAGONAL',
            'EJE VIAL' => 'EJE VIAL',
            'PASAJE' => 'PASAJE',
            'PEATONAL' => 'PEATONAL',
            'PERIFERICO' => 'PERIFERICO',
            'PRIVADA' => 'PRIVADA',
            'PROLONGACION' => 'PROLONGACION',
            'RETORNO' => 'RETORNO',
            'VIADUCTO' => 'VIADUCTO',
        ];

        // Tipos de asentamiento.
        $asentamientos = [
            'AEROPUERTO' => 'AEROPUERTO',
            'AMPLIACION' => 'AMPLIACION',
            'BARRIO' => 'BARRIO',
            'CANTON' => 'CANTON',
            'CIUDAD' => 'CIUDAD',
            'CIUDAD INDUSTRIAL' => 'CIUDAD INDUSTRIAL',
            'COLONIA' => 'COLONIA',
            'CONDOMINIO' => 'CONDOMINIO',
            'CONJUNTO HABITACIONAL' => 'CONJUNTO HABITACIONAL',
            'CORREDOR INDUSTRIAL' => 'CORREDOR INDUSTRIAL',
            'COTO' => 'COTO',
            'CUARTEL' => 'CUARTEL',
            'EJIDO' => 'EJIDO',
            'EXHACIENDA' => 'EXHACIENDA',
            'FRACCION' => 'FRACCION',
            'FRACCIONAMIENTO' => 'FRACCIONAMIENTO',
            'GRANJA' => 'GRANJA',
            'HACIENDA' => 'HACIENDA',
            'INGENIO' => 'INGENIO',
            'MANZANA' => 'MANZANA',
            'PARAJE' => 'PARAJE',
            'PARQUE INDUSTRIAL' => 'PARQUE INDUSTRIAL',
            'PRIVADA' => 'PRIVADA',
            'PROLONGACION' => 'PROLONGACION',
            'PUEBLO' => 'PUEBLO',
            'PUERTO' => 'PUERTO',
            'RANCHERIA' => 'RANCHERIA',
            'RANCHO' => 'RANCHO',
            'REGION' => 'REGION',
            'RESIDENCIAL' => 'RESIDENCIAL',
            'RINCONADA' => 'RINCONADA',
            'SECCION' => 'SECCION',
            'SECTOR' => 'SECTOR',
            'SUPERMANZANA' => 'SUPERMANZANA',
            'UNIDAD' => 'UNIDAD',
            'UNIDAD HABITACIONAL' => 'UNIDAD HABITACIONAL',
            'VILLA' => 'VILLA',
            'ZONA FEDERAL' => 'ZONA FEDERAL',
            'ZONA INDUSTRIAL' => 'ZONA INDUSTRIAL',
            'ZONA MILITAR' => 'ZONA MILITAR',
        ];

        // Seguridad social.
        $seguridad_social = SeguridadSocial::pluck('regimen', 'id');

        // Opciones de administración.
        $admon = [
            'ESTATAL' => 'ESTATAL',
            'FEDERAL' => 'FEDERAL'
        ];

        $derecho = [
            'DE CUOTA' => 'DE CUOTA',
            'LIBRE' => 'LIBRE'
        ];

        $admon2 = [
            'BRECHA' => 'BRECHA',
            'CAMINO' => 'CAMINO',
            'TERRACERIA' => 'TERRACERIA',
            'VEREDA' => 'VEREDA'
        ];

        $margen = [
            'DERECHO' => 'DERECHO',
            'IZQUIERO' => 'IZQUIERDO'
        ];

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
        $ctx = $this->contexto();

        $modulo = $ctx['modulo'];
        $nivel = (int) $ctx['nivel'];
        $id_escuela = $ctx['id_escuela'];

        // Solo el nivel 3 del módulo de licenciatura puede editar.
        if ($modulo !== 'l' || $nivel !== 3) {
            abort(403, 'No tienes permiso para editar la infraestructura.');
        }

        if (!$id_escuela) {
            abort(403, 'No tienes una escuela asignada.');
        }

        // Buscar la infraestructura por la escuela del usuario.
        // Si todavía no existe, preparar un registro nuevo.
        $estructura = CaracteristicaEscuela::firstOrNew([
            'id_escuela' => $id_escuela,
        ]);

        $tiposPropiedad = Construccion::where('id', '<', 20)
            ->pluck('construccion', 'id');

        $tiposEstructura = Construccion::where('id', '>', 20)
            ->pluck('construccion', 'id');

        $bandaAncha = [
            's' => 'Banda ancha',
            'n' => '56 Kbps',
        ];

        $sino = [
            's' => 'Sí',
            'n' => 'No',
        ];

        $title = 'Editar infraestructura de la escuela';

        return view('siben.edita_estructura', compact(
            'estructura',
            'tiposPropiedad',
            'tiposEstructura',
            'bandaAncha',
            'sino',
            'title'
        ));
    }


    public function guardaEstructura(Request $request)
    {
        $ctx = $this->contexto();

        $modulo = $ctx['modulo'];
        $nivel = (int) $ctx['nivel'];
        $id_escuela = $ctx['id_escuela'];

        // Solo el nivel 3 del módulo de licenciatura puede guardar.
        if ($modulo !== 'l' || $nivel !== 3) {
            abort(403, 'No tienes permiso para guardar la infraestructura.');
        }

        if (!$id_escuela) {
            abort(403, 'No tienes una escuela asignada.');
        }

        $camposTinyInt = [
            'aulas',
            'bibliotecas',
            'aulas_computo',
            'auditorios',
            'idiomas',
            'albercas',
            'gimnasios',
            'canchas',
            'pistas',
            'salas_maestros',
            'cubiculos_maestros',
            'salas_multiples',
            'laboratorios',
            'talleres',
            'd_rampas',
            'd_elevadores',
            'd_computadoras',
            'd_banios',
            'd_libros',
        ];

        $camposNumericos = [
            'computo_alumnos',
            'computo_docentes',
            'computo_administrativo',
            'internet_alumnos',
            'internet_docentes',
            'internet_administrativo',
        ];

        $reglas = [
            'CaracteristicaEscuela.propiedad' => 'required|integer|in:11,12,13,14',
            'CaracteristicaEscuela.estructura' => 'required|integer|in:21,22',

            'CaracteristicaEscuela.internet' => 'required|in:s,n',
            'CaracteristicaEscuela.internet_banda_ancha' => 'required|in:s,n',
            'CaracteristicaEscuela.restriccion_internet' => 'required|in:s,n',
            'CaracteristicaEscuela.siden' => 'required|in:s,n',
            'CaracteristicaEscuela.edusat' => 'required|in:s,n',
            'CaracteristicaEscuela.d_software' => 'required|in:s,n',
            'CaracteristicaEscuela.d_guias' => 'required|in:s,n',
        ];

        foreach ($camposTinyInt as $campo) {
            $reglas["CaracteristicaEscuela.$campo"] = 'required|integer|min:0|max:255';
        }

        foreach ($camposNumericos as $campo) {
            $reglas["CaracteristicaEscuela.$campo"] = 'required|integer|min:0|max:100000';
        }

        // Validar únicamente los campos permitidos.
        $validado = $request->validate($reglas);

        $datos = $validado['CaracteristicaEscuela'];

        // Validar que los equipos con Internet no superen
        // el total de equipos de cómputo de cada grupo.
        $errores = [];

        if ($datos['internet_alumnos'] > $datos['computo_alumnos']) {
            $errores['CaracteristicaEscuela.internet_alumnos'] =
                'Las computadoras con Internet para alumnos no pueden superar el total de computadoras para alumnos.';
        }

        if ($datos['internet_docentes'] > $datos['computo_docentes']) {
            $errores['CaracteristicaEscuela.internet_docentes'] =
                'Las computadoras con Internet para docentes no pueden superar el total de computadoras para docentes.';
        }

        if ($datos['internet_administrativo'] > $datos['computo_administrativo']) {
            $errores['CaracteristicaEscuela.internet_administrativo'] =
                'Las computadoras con Internet para administrativos no pueden superar el total de computadoras para administrativos.';
        }

        if (!empty($errores)) {
            return back()
                ->withErrors($errores)
                ->withInput();
        }

        // Obtener o crear el registro exclusivamente para esta escuela.
        $estructura = CaracteristicaEscuela::firstOrNew([
            'id_escuela' => $id_escuela,
        ]);

        $estructura->id_escuela = $id_escuela;

        foreach ($datos as $campo => $valor) {
            $estructura->{$campo} = $valor;
        }

        $estructura->save();

        // Marcar la infraestructura como registrada, si existe
        // el registro de estado correspondiente.
        EscuelaStatus::where('id_escuela', $id_escuela)
            ->where('modulo', $modulo)
            ->update(['estructura' => 1]);

        return redirect()
            ->route('siben.estructuraEscuela')
            ->with('success', 'La infraestructura se guardó correctamente.');
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
        $ctx = $this->contexto();

        $modulo     = $ctx['modulo'];
        $nivel      = $ctx['nivel'];
        $id_escuela = $ctx['id_escuela'];

        // Validar permisos
        if ($modulo !== 'l' || $nivel !== 3) {
            abort(403, 'No tienes permiso para modificar esta información.');
        }

        if (!$id_escuela) {
            abort(403, 'No tienes una escuela asignada.');
        }

        // Obtener exclusivamente la escuela del usuario
        $escuela = Escuela::findOrFail($id_escuela);

        // Validar los campos permitidos
        $datos = $request->validate([
            'telefono1' => ['nullable', 'string', 'max:10'],
            'telefono2' => ['nullable', 'string', 'max:10'],
            'telefono3' => ['nullable', 'string', 'max:10'],
            'fax' => ['nullable', 'string', 'max:10'],
            'lada' => ['nullable', 'string', 'max:5'],
            'correo_electronico' => ['nullable', 'email', 'max:128'],
            'pagina_web' => ['nullable', 'string', 'max:128'],
            'turno' => ['required', 'in:m,v,d,n'],
            'sostenimiento' => ['required', 'in:p,q'],
            'investigacion' => ['required', 'in:s,n'],
            'fecha_autorizacion' => ['nullable', 'date_format:Y-m-d'],
            'fecha_inicio' => ['nullable', 'date_format:Y-m-d'],
        ]);

        // Evitar valores NULL en columnas que no los permiten
        foreach ([
            'telefono1',
            'telefono2',
            'telefono3',
            'fax',
            'lada',
            'correo_electronico',
            'pagina_web',
        ] as $campo) {
            $datos[$campo] = $datos[$campo] ?? '';
        }

        // La base de datos utiliza 0000-00-00 para fechas vacías
        foreach (['fecha_autorizacion', 'fecha_inicio'] as $campo) {
            $datos[$campo] = $datos[$campo] ?? '0000-00-00';
        }

        // Actualizar únicamente los campos autorizados
        $escuela->update($datos);

        return redirect()
            ->route('siben.datosEscuela')
            ->with('success', 'La información adicional se guardó correctamente.');
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
    $ctx = $this->contexto();

    $modulo = $ctx['modulo'];
    $nivel = (int) $ctx['nivel'];
    $id_escuela = $ctx['id_escuela'];

    // Solo nivel 4 del módulo de licenciatura.
    if ($modulo !== 'l' || $nivel !== 4) {
        abort(403, 'No tienes permiso para subir fotografías.');
    }

    if (!$id_escuela) {
        abort(403, 'No tienes una escuela asignada.');
    }

    $escuela = Escuela::findOrFail($id_escuela);
    $escuela_key = $escuela->escuela_key ?? '';

    if (!$escuela_key || !preg_match('/^[A-Za-z0-9_-]+$/', $escuela_key)) {
        abort(404, 'No se encontró la clave de la escuela para sus fotografías.');
    }

    $imagenes = [];
    $id = null;

    // Las escuelas pueden tener hasta seis fotografías.
    for ($i = 1; $i <= 6; $i++) {
        $rutaImagen = public_path(
            "img/escuelas/big/{$escuela_key}_{$i}.jpg"
        );

        if (file_exists($rutaImagen)) {
            $imagenes[$i] = $i;
        } else {
            $imagenes[$i] = 0;

            // Identificar el primer espacio disponible.
            if ($id === null) {
                $id = $i;
            }
        }
    }

    $subir = $id !== null;
    $anterior = 'estructuraEscuela';

    return view('siben.imagen_escuela', compact(
        'escuela',
        'escuela_key',
        'imagenes',
        'id',
        'subir',
        'anterior'
    ));
}


public function subeImagen(Request $request, $id)
{
    $ctx = $this->contexto();

    $modulo = $ctx['modulo'];
    $nivel = (int) $ctx['nivel'];
    $id_escuela = $ctx['id_escuela'];

    // Solo nivel 4 del módulo de licenciatura.
    if ($modulo !== 'l' || $nivel !== 4) {
        abort(403, 'No tienes permiso para subir fotografías.');
    }

    if (!$id_escuela) {
        abort(403, 'No tienes una escuela asignada.');
    }

    // Solo se permiten los seis espacios de fotografía.
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if (!$id || $id < 1 || $id > 6) {
        abort(404, 'El espacio de fotografía no es válido.');
    }

    $escuela = Escuela::findOrFail($id_escuela);
    $escuela_key = $escuela->escuela_key ?? '';

    if (!$escuela_key || !preg_match('/^[A-Za-z0-9_-]+$/', $escuela_key)) {
        abort(404, 'No se encontró la clave de la escuela.');
    }

    // Validar que se haya seleccionado una imagen JPG/JPEG.
    $request->validate([
        'imagen' => 'required|image|mimes:jpg,jpeg|max:5120',
    ], [
        'imagen.required' => 'Selecciona una fotografía.',
        'imagen.image' => 'El archivo debe ser una imagen.',
        'imagen.mimes' => 'La fotografía debe estar en formato JPG o JPEG.',
        'imagen.max' => 'La fotografía no debe superar los 5 MB.',
    ]);

    $directorio = public_path('img/escuelas/big');

    if (!is_dir($directorio)) {
        mkdir($directorio, 0755, true);
    }

    $nombreArchivo = "{$escuela_key}_{$id}.jpg";
    $rutaDestino = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo;

    // No reemplazar una fotografía que ya existe.
    if (file_exists($rutaDestino)) {
        return redirect()
            ->route('siben.imagenEscuela')
            ->with('error', 'Ese espacio ya tiene una fotografía.');
    }

    $request->file('imagen')->move($directorio, $nombreArchivo);

    return redirect()
        ->route('siben.imagenEscuela')
        ->with('success', 'La fotografía se subió correctamente.');
}


// La eliminación queda bloqueada hasta definir el permiso correspondiente.
public function borraImagen(Request $request, $id)
{
    abort(403, 'La eliminación de fotografías no está habilitada.');
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
        $nombre_entidad = Entidad::where('id', $id_entidad)->value('entidad');
        $modulo     = $ctx['modulo'];
        $nivel      = (int) $ctx['nivel'];

        // Validar módulo y niveles autorizados.
        if ($modulo !== 'l' || $nivel !== 3) {
            abort(403, 'No tienes permiso para consultar este domicilio.');
        }

        if (!$id_escuela || !$id_entidad) {
            abort(403, 'No se pudo identificar la escuela del usuario.');
        }

        // Obtener exclusivamente el domicilio de la escuela del usuario.
        $domicilioEscuela = DomicilioEscuela::where('id_escuela', $id_escuela)
            ->first();

        $id_municipio = $domicilioEscuela
            ? $domicilioEscuela->cve_mun
            : null;

        $vialidad = [
            'AMPLIACION' => 'AMPLIACION',
            'ANDADOR' => 'ANDADOR',
            'AVENIDA' => 'AVENIDA',
            'BOULEVARD' => 'BOULEVARD',
            'CALLE' => 'CALLE',
            'CALLEJON' => 'CALLEJON',
            'CALZADA' => 'CALZADA',
            'CAMINO' => 'CAMINO',
            'CARRETERA' => 'CARRETERA',
            'CERRADA' => 'CERRADA',
            'CIRCUITO' => 'CIRCUITO',
            'CIRCUNVALACION' => 'CIRCUNVALACION',
            'CONTINUACION' => 'CONTINUACION',
            'CORREDOR' => 'CORREDOR',
            'DIAGONAL' => 'DIAGONAL',
            'EJE VIAL' => 'EJE VIAL',
            'PASAJE' => 'PASAJE',
            'PEATONAL' => 'PEATONAL',
            'PERIFERICO' => 'PERIFERICO',
            'PRIVADA' => 'PRIVADA',
            'PROLONGACION' => 'PROLONGACION',
            'RETORNO' => 'RETORNO',
            'VIADUCTO' => 'VIADUCTO',
        ];

        $asentamientos = [
            'AEROPUERTO' => 'AEROPUERTO',
            'AMPLIACION' => 'AMPLIACION',
            'BARRIO' => 'BARRIO',
            'CANTON' => 'CANTON',
            'CIUDAD' => 'CIUDAD',
            'CIUDAD INDUSTRIAL' => 'CIUDAD INDUSTRIAL',
            'COLONIA' => 'COLONIA',
            'CONDOMINIO' => 'CONDOMINIO',
            'CONJUNTO HABITACIONAL' => 'CONJUNTO HABITACIONAL',
            'CORREDOR INDUSTRIAL' => 'CORREDOR INDUSTRIAL',
            'COTO' => 'COTO',
            'CUARTEL' => 'CUARTEL',
            'EJIDO' => 'EJIDO',
            'EXHACIENDA' => 'EXHACIENDA',
            'FRACCION' => 'FRACCION',
            'FRACCIONAMIENTO' => 'FRACCIONAMIENTO',
            'GRANJA' => 'GRANJA',
            'HACIENDA' => 'HACIENDA',
            'INGENIO' => 'INGENIO',
            'MANZANA' => 'MANZANA',
            'PARAJE' => 'PARAJE',
            'PARQUE INDUSTRIAL' => 'PARQUE INDUSTRIAL',
            'PRIVADA' => 'PRIVADA',
            'PROLONGACION' => 'PROLONGACION',
            'PUEBLO' => 'PUEBLO',
            'PUERTO' => 'PUERTO',
            'RANCHERIA' => 'RANCHERIA',
            'RANCHO' => 'RANCHO',
            'REGION' => 'REGION',
            'RESIDENCIAL' => 'RESIDENCIAL',
            'RINCONADA' => 'RINCONADA',
            'SECCION' => 'SECCION',
            'SECTOR' => 'SECTOR',
            'SUPERMANZANA' => 'SUPERMANZANA',
            'UNIDAD' => 'UNIDAD',
            'UNIDAD HABITACIONAL' => 'UNIDAD HABITACIONAL',
            'VILLA' => 'VILLA',
            'ZONA FEDERAL' => 'ZONA FEDERAL',
            'ZONA INDUSTRIAL' => 'ZONA INDUSTRIAL',
            'ZONA MILITAR' => 'ZONA MILITAR',
        ];

        $admon = [
            'ESTATAL' => 'ESTATAL',
            'FEDERAL' => 'FEDERAL',
        ];

        $derecho = [
            'DE CUOTA' => 'DE CUOTA',
            'LIBRE' => 'LIBRE',
        ];

        $admon2 = [
            'BRECHA' => 'BRECHA',
            'CAMINO' => 'CAMINO',
            'TERRACERIA' => 'TERRACERIA',
            'VEREDA' => 'VEREDA',
        ];

        $margen = [
            'DERECHO' => 'DERECHO',
            'IZQUIERDO' => 'IZQUIERDO',
        ];

        // Municipios de la entidad del usuario.
        $municipios = Municipio::where('id_entidad', $id_entidad)
            ->orderBy('municipio')
            ->pluck('municipio', 'id_municipio');

        // Localidades correspondientes al municipio del domicilio.
        $locs = collect();

        if ($id_municipio) {
            $locs = Localidad::where('id_entidad', $id_entidad)
                ->where('id_municipio', $id_municipio)
                ->orderBy('localidad')
                ->pluck('localidad', 'id_localidad');
        }

        $seguridad_social = SeguridadSocial::orderBy('regimen')
            ->pluck('regimen', 'id');

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
            'nombre_entidad',
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
        $urlEscuela = route('siben.editaDomicilio');

        switch ($nivel) {
            case 3:
                $botones['escuela'] = "<a href='{$urlEscuela}' class='boton__primario' style='padding: 6px 14px; text-decoration: none;'>Editar</a>";
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
        $ctx = $this->contexto();

        $id_entidad = $ctx['id_entidad'];
        $modulo = $ctx['modulo'];
        $nivel = (int) $ctx['nivel'];

        // Solo niveles 3 y 4 del módulo de licenciaturas.
        if ($modulo !== 'l' || !in_array($nivel, [3, 4], true)) {
            abort(403, 'No tienes permiso para consultar localidades.');
        }

        $id_municipio = $request->input(
            'DomicilioEscuela.cve_mun',
            $request->input('cve_mun')
        );

        if (!$id_municipio || !$id_entidad) {
            return view('elements.opciones2', [
                'opciones' => collect()
            ]);
        }

        // Las opciones guardan la clave geográfica id_localidad,
        // no la llave primaria interna id.
        $opciones = Localidad::where('id_entidad', $id_entidad)
            ->where('id_municipio', $id_municipio)
            ->orderBy('localidad')
            ->pluck('localidad', 'id_localidad');

        return view('elements.opciones2', compact('opciones'));
    }


        
    public function guardaDomicilio(Request $request)
    {
        $ctx = $this->contexto();

        $id_escuela = $ctx['id_escuela'];
        $id_entidad = $ctx['id_entidad'];
        $modulo     = $ctx['modulo'];
        $nivel      = (int) $ctx['nivel'];

        // Solo nivel 3 del módulo de licenciaturas puede guardar.
        if ($modulo !== 'l' || $nivel !== 3) {
            abort(403, 'No tienes permiso para modificar el domicilio.');
        }

        if (!$id_escuela || !$id_entidad) {
            abort(403, 'No se pudo identificar la escuela del usuario.');
        }

        $datos = $request->input('DomicilioEscuela', []);

        if (!is_array($datos)) {
            return back()->withErrors([
                'domicilio' => 'Los datos del domicilio no son válidos.'
            ])->withInput();
        }

        // Lista blanca: solo se guardan los campos permitidos.
        $campos = [
            'tipovial',
            'nomvial',
            'carretera',
            'camino',
            'numextnum1',
            'numextnum2',
            'numextalf1',
            'numintnum',
            'numintalf',
            'tipoasen',
            'nomasen',
            'cp',
            'tiporef1',
            'nomref1',
            'tiporef2',
            'nomref2',
            'tiporef3',
            'nomref3',
            'descrubic',
            'latitud',
            'longitud',
            'id_seguridad_social',
        ];

        $datosGuardar = [];

        foreach ($campos as $campo) {
            if (array_key_exists($campo, $datos)) {
                $datosGuardar[$campo] = $datos[$campo] === ''
                    ? null
                    : $datos[$campo];
            }
        }

        // Validación de los datos recibidos.
        $validador = \Validator::make($datosGuardar, [
            'tipovial' => 'nullable|string|max:255',
            'nomvial' => 'nullable|string|max:255',
            'carretera' => 'nullable|string|max:255',
            'camino' => 'nullable|string|max:255',
            'numextnum1' => 'nullable|string|max:255',
            'numextnum2' => 'nullable|string|max:255',
            'numextalf1' => 'nullable|string|max:35',
            'numintnum' => 'nullable|string|max:5',
            'numintalf' => 'nullable|string|max:35',
            'tipoasen' => 'nullable|string|max:255',
            'nomasen' => 'nullable|string|max:255',
            'cp' => 'nullable|string|size:5',
            'tiporef1' => 'nullable|string|max:255',
            'nomref1' => 'nullable|string|max:255',
            'tiporef2' => 'nullable|string|max:255',
            'nomref2' => 'nullable|string|max:255',
            'tiporef3' => 'nullable|string|max:255',
            'nomref3' => 'nullable|string|max:255',
            'descrubic' => 'nullable|string|max:255',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'id_seguridad_social' => 'required|integer|exists:cat_seguridad_social,id',
        ]);

        if ($validador->fails()) {
            return back()
                ->withErrors($validador)
                ->withInput();
        }

        // El municipio debe pertenecer a la entidad de la escuela.
        $id_municipio = $request->input('DomicilioEscuela.cve_mun');

        $municipio = Municipio::where('id_entidad', $id_entidad)
            ->where('id_municipio', $id_municipio)
            ->first();

        if (!$municipio) {
            return back()->withErrors([
                'municipio' => 'El municipio seleccionado no corresponde a la entidad de la escuela.'
            ])->withInput();
        }

        // La localidad debe pertenecer al municipio seleccionado.
        $id_localidad = $request->input('DomicilioEscuela.cve_loc');

        $localidad = Localidad::where('id_entidad', $id_entidad)
            ->where('id_municipio', $id_municipio)
            ->where('id_localidad', $id_localidad)
            ->first();

        if (!$localidad) {
            return back()->withErrors([
                'localidad' => 'La localidad seleccionada no corresponde al municipio.'
            ])->withInput();
        }

        // Los nombres geográficos se obtienen del catálogo.
        $entidad = Entidad::find($id_entidad);

        if (!$entidad) {
            abort(403, 'No se encontró la entidad de la escuela.');
        }

        $datosGuardar['id_escuela'] = $id_escuela;
        $datosGuardar['cve_ent'] = $id_entidad;
        $datosGuardar['nom_ent'] = $entidad->entidad;

        $datosGuardar['cve_mun'] = $municipio->id_municipio;
        $datosGuardar['nom_mun'] = $municipio->municipio;

        $datosGuardar['cve_loc'] = $localidad->id_localidad;
        $datosGuardar['nom_loc'] = $localidad->localidad;

        try {
            \DB::transaction(function () use (
                $id_escuela,
                $modulo,
                $datosGuardar
            ) {
                DomicilioEscuela::updateOrCreate(
                    ['id_escuela' => $id_escuela],
                    $datosGuardar
                );

            });

        } catch (\Throwable $e) {
            \Log::error('Error al guardar domicilio de escuela', [
                'id_escuela' => $id_escuela,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'domicilio' => 'Ocurrió un error al guardar el domicilio.'
            ])->withInput();
        }

        return redirect()
            ->route('siben.domicilioEscuela')
            ->with('success', 'El domicilio de la escuela se guardó correctamente.');
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

        $modulo = $ctx['modulo'];
        $nivel = (int) $ctx['nivel'];
        $id_escuela = $ctx['id_escuela'];

        // Solo módulo de licenciatura y niveles 3 y 4.
        if ($modulo !== 'l' || !in_array($nivel, [3, 4], true)) {
            abort(403, 'No tienes permiso para consultar esta infraestructura.');
        }

        if (!$id_escuela) {
            abort(403, 'No tienes una escuela asignada.');
        }

        // La escuela siempre se obtiene desde el contexto del usuario.
        $escuela = Escuela::with('caracteristicaEscuela')
            ->findOrFail($id_escuela);

        $nombreEscuela = $escuela->escuela;
        $title = 'Infraestructura, ' . $nombreEscuela;

        $estado_estructura = session('Usuario.estructura');
        $estructura = $escuela->caracteristicaEscuela;
        $escuela_key = $escuela->escuela_key ?? '';

        $mensaje = '';

        // Obtener las licenciaturas sin utilizar GROUP BY.
        // DISTINCT evita repetir una misma licenciatura.
        $idsLicenciaturas = EscuelaLicenciatura::where('id_escuela', $id_escuela)
            ->select('id_licenciatura')
            ->distinct()
            ->pluck('id_licenciatura');

        $licenciaturas = EscuelaLicenciatura::with([
            'licenciatura:id,licenciatura',
            'personal:id,nombre,apellido_paterno,apellido_materno,correo_electronico',
        ])
            ->where('id_escuela', $id_escuela)
            ->whereIn('id_licenciatura', $idsLicenciaturas)
            ->get()
            ->unique('id_licenciatura')
            ->values();

        $turnos = [
            'm' => 'Matutino',
            'v' => 'Vespertino',
            'd' => 'Discontinuo',
            'n' => 'Nocturno',
        ];

        $sostenimientos = [
            'p' => 'Público',
            'q' => 'Particular',
        ];

        $posgrados = [
            'm' => 'Maestría',
            'd' => 'Doctorado',
            'e' => 'Especialidad',
            'c' => 'Educación Continua',
            'i' => 'SEPA Inglés',
        ];

        $bandaAncha = [
            's' => 'Banda Ancha',
            'n' => '56Kbps',
        ];

        $sino = [
            's' => 'Sí',
            'n' => 'No',
        ];

        $tiposPropiedad = Construccion::where('id', '<', 20)
            ->pluck('construccion', 'id');

        $tiposEstructura = Construccion::where('id', '>', 20)
            ->pluck('construccion', 'id');

        $localidad = null;

        if ($escuela->id_localidad) {
            $localidad = Localidad::find($escuela->id_localidad);
        }

        // Comprobar las fotografías existentes.
        $imagenes = [];

        for ($i = 1; $i <= 6; $i++) {
            $pathImagen = public_path(
                "img/escuelas/big/{$escuela_key}_{$i}.jpg"
            );

            $imagenes[$i] = file_exists($pathImagen) ? $i : 0;
        }

        // Permisos para la vista.
        $puedeEditar = $nivel === 3;
        $puedeSubirFotos = $nivel === 4;

        $anterior = $opc !== 'e';

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
            'puedeEditar',
            'puedeSubirFotos',
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
        $ctx = $this->contexto();

        $modulo     = $ctx['modulo'];
        $nivel      = $ctx['nivel'];
        $id_escuela = $ctx['id_escuela'];

        // Validar módulo y nivel de acceso
        if ($modulo !== 'l' || $nivel !== 3) {
            abort(403, 'No tienes permiso para editar esta información.');
        }

        if (!$id_escuela) {
            abort(403, 'No tienes una escuela asignada.');
        }

        $escuela = Escuela::findOrFail($id_escuela);

        $turnos = [
            'm' => 'Matutino',
            'v' => 'Vespertino',
            'd' => 'Discontinuo',
            'n' => 'Nocturno',
        ];

        $sostenimientos = [
            'p' => 'Público',
            'q' => 'Particular',
        ];

        $investigacion = [
            's' => 'Sí',
            'n' => 'No',
        ];

        return view('siben.edita_adicionales', compact(
            'escuela',
            'turnos',
            'sostenimientos',
            'investigacion'
        ));
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