<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Licenciatura;
use App\Models\Especialidad;
use App\Models\Entidad;
use App\Models\Municipio;
use App\Models\EscuelaLicenciatura;
use App\Models\ListaBeca;
use App\Models\Escuela2; // Usado como alias de Escuela según el código original
use Illuminate\Http\Request;

class PdfsController extends sibenController
{
    /**
     * index
     * Generación de prueba o vista base en PDF
     */
    public function index()
    {
        // En Laravel/PHP moderno manejamos la codificación UTF-8 de forma nativa.
        // Si el sistema de PDF requiere ISO-8859-1 específicamente, mb_convert_encoding es el reemplazo ideal de iconv.
        $data = mb_convert_encoding("¡hola mundo!\nAcentos:\náéíóú", "ISO-8859-1", "UTF-8");

        return view('pdfs.index', compact('data'));
    }

    /**
     * listadoBecasEntidad
     * Generación del reporte de listado de becas por entidad
     */
    public function listadoBecasEntidad($id_entidad = null, $semestre = 7)
    {
        $ctx = $this->contexto();
        $modulo = $ctx['modulo'] ?? null;

        if (!$id_entidad) {
            // Nota: Corregido el typo del original 'id_entida' hacia 'id_entidad'
            $id_entidad = $ctx['id_entidad'] ?? null;
        }

        $entidad = Entidad::where('id', $id_entidad)->first();

        $mnemonicos_lic = [
            0  => '',
            2  => 'LEPre',
            4  => 'LEP',
            6  => 'LES',
            7  => 'LEE',
            8  => 'LEF',
            13 => 'LEPIB',
        ];

        $mnemonicos_esp = [
            30 => 'TELE',
            31 => 'ESP',
            32 => 'MAT',
            33 => 'LEXT-I',
            34 => 'BIOL',
            35 => 'HIST',
            36 => 'QUIM',
            37 => 'FIS',
            38 => 'FCyE',
            39 => 'GEOGR',
            40 => 'LEXT-F',
            41 => 'PEDAG',
            42 => 'PSIC',
            51 => 'AyL',
            52 => 'INT',
            53 => 'MOT',
            54 => 'VIS',
        ];

        // Lista de escuelas públicas, activas y del módulo de licenciaturas en la entidad
        $escuelas = Escuela2::where('id_entidad', $id_entidad)
            ->where('modulo', $modulo)
            ->where('status', 'a')
            ->where('unica', '<', 2)
            ->where('sostenimiento', 'p')
            ->orderBy('escuela')
            ->pluck('escuela', 'id');

        // Lista de becados de la entidad
        $becas = ListaBeca::where('id_entidad', $id_entidad)
            ->where('semestre', $semestre)
            ->get();

        $title = 'Listado Becas Entidad';

        $licenciaturas = Licenciatura::pluck('licenciatura', 'id');

        $especialidadesCollection = Especialidad::pluck('especialidad', 'id')->toArray();
        $especialidades = ['0' => ''] + $especialidadesCollection;

        $situaciones = ['n' => 'AB', 'r' => 'AR'];

        $municipios = Municipio::where('id_entidad', $id_entidad)
            ->pluck('municipio', 'id');

        // Agregamos la información de las licenciaturas impartidas en cada escuela
        $escuelas_licenciaturas = [];
        foreach ($escuelas as $id_escuela => $escuelaNombre) {
            $escuela_licenciaturas[$id_escuela] = EscuelaLicenciatura::where('id_escuela', $id_escuela)
                ->groupBy('id_licenciatura', 'id_especialidad')
                ->pluck('clave', 'id_licenciatura')
                ->toArray();
        }

        $ancho = '1280'; // El ancho de la tabla principal y del pie de página
        $captura = 0;
        $tipo_beca = 'ÚNICA';
        $monto_mensual = '700';
        $factor_periodo = '3.5';

        return view('pdfs.listado_becas_entidad', compact(
            'title',
            'licenciaturas',
            'especialidades',
            'situaciones',
            'municipios',
            'escuelas',
            'ancho',
            'becas',
            'captura',
            'escuelas_licenciaturas',
            'tipo_beca',
            'monto_mensual',
            'factor_periodo',
            'entidad',
            'mnemonicos_lic',
            'mnemonicos_esp'
        ));
    }
}