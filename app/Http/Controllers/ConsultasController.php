<?php
namespace App\Http\Controllers;

use App\Models\Escuela2;
use App\Models\Entidad;
use App\Models\Alumno;
use Illuminate\Http\Request;

class ConsultasController extends sibenController
{
    /**
     * listaEscuelas
     * muestra la lista de escuelas deacuerdo a la entidad
     */
    public function listaEscuelas(Request $request = null)
    {
        // Si el router inyecta el request, lo usamos; si no, recurrimos al helper global
        $request   = $request ?? request();
        $ctx       = $this->contexto();
        $nivel     = $ctx['nivel'] ?? null;
        $modulo    = $ctx['modulo'] ?? null;
        $id_renapo = $ctx['id_renapo'] ?? null;

        $id_entidad = $request->input('id_entidad') ?? $ctx['id_entidad'] ?? null;

        if (!$id_entidad) {
            $id_entidad = 9; // por defecto
        }

        $dgespe    = false;
        $entidades = [];

        switch ($nivel) {
            case 1:
            case 5:
                $dgespe = true;
                $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');
                break;
            case 2:
            case 6:
                $dgespe = false;
                $entidad = Entidad::where('id_renapo', $id_renapo)->first();
                if ($entidad) {
                    $id_entidad = $entidad->id;
                }
                break;
            case 3:
            case 4:
                return redirect()->route('siben.index');
        }

        $escuelas = Escuelas2::where('id_entidad', $id_entidad)
            ->where('status', 'a')
            ->where('unica', '<', 2)
            ->where('modulo', $modulo)
            ->get();

        return view('consultas.lista_escuelas', compact('escuelas', 'dgespe', 'entidades'));
    }


    /**
     * listados
     * Muestra el menú de opciones e informes de la escuela seleccionada
     */
    public function listados($id_escuela = null)
    {
        $request = request(); // Obtenemos el request de forma global
        $id_escuela = $id_escuela ?? $request->route('id_escuela'); // O lo capturamos de la ruta

        if (!$id_escuela) {
            return redirect()->route('siben.index');
        }

        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $modulo = $ctx['modulo'] ?? null;

        // Cargamos el id_escuela en la sesión de forma segura
        session(['Usuario.id_escuela' => $id_escuela]);

        $escuela = Escuela2::where('id', $id_escuela)
            ->where('modulo', $modulo)
            ->select('id_entidad')
            ->first();

        $id_entidad = $escuela ? $escuela->id_entidad : null;

        $opcionesMenu = [
            1 => ['informes', 'Listado Alumnos', 'listadoAlumnos', 1],
            2 => ['informes', 'Estadística Alumnos', 'estadisticaAlumnos', 1],
            3 => ['informes', 'Listado Docentes', 'listadoDocentes', 1],
            4 => ['informes', 'Estadística Docentes', 'estadisticaDocentes', 1],
            5 => ['informes', 'Listado Personal', 'listadoPersonal', 1],
            6 => ['informes', 'Estadística Personal', 'estadisticaPersonal', 1],
            7 => ['informes', 'Datos Escuela', 'datosEscuela', 1],
            9 => ['informes', 'Lista Alumnos NO Curp', 'listaCurp', 1],
            10 => ['informes', 'Lista Personal NO Curp', 'listaCurpPer', 1],
            11 => ['informes', 'Contrataciones Docente', 'listadoContra', 1],
            12 => ['informes', 'Asignaturas Docente', 'listadoAsignatura', 1],
            13 => ['informes', 'Alumnos No egresados', 'listadoEgresados', 1],
        ];

        if ($nivel == 1) {
            $opcionesMenu[8] = ['informes', 'Listado Becas', 'listadoBecas', 1, $id_escuela];
        }

        $anterior = true;

        return view('consultas.listados', compact('opcionesMenu', 'anterior'));
    }

    /**
     * buscaAlumnos
     * Permite la búsqueda de alumnos (exclusivo para nivel 5)
     */
    public function buscaAlumnos(Request $request)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        if ($nivel != 5) {
            return redirect()->route('siben.index');
        }

        $alumnos = [];
        if ($request->isMethod('post') && !empty($request->all())) {
            $curp = $request->input('Alumno.curp');
            $matricula = $request->input('Alumno.matricula');

            $query = Alumno::query();
            
            if (!empty($curp)) {
                $query->where('curp', 'LIKE', "%{$curp}%");
            }
            if (!empty($matricula)) {
                $query->where('numero_identificador', $matricula);
            }

            $alumnos = $query->get();
        }

        return view('consultas.busca_alumnos', compact('alumnos'));
    }
}
