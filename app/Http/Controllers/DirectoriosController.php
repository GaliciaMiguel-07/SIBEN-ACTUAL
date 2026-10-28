<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use App\Models\Directorio;
use App\Models\Disfuncion;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class DirectoriosController extends sibenController
{
    /**
     * index
     * Muestra la lista de entidades para consultar directorios
     */
    public function index(Request $request)
    {
        Entidad::$displayField = 'entidad';
        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');

        return view('directorios.index', compact('entidades'));
    }

    /**
     * gendir
     * Genera y descarga el reporte en formato Excel (XLS) del directorio por entidad
     */
    public function gendir(Request $request, $id_entidad = null)
    {
        $entidadModel = Entidad::find($id_entidad);

        Disfuncion::$displayField = 'funcion';
        $funciones = Disfuncion::pluck('funcion', 'id');

        $conditions = [];
        if ($id_entidad >= 1 && $id_entidad <= 33) {
            $conditions['id_entidad'] = $id_entidad;
        }

        $directorio = Directorio::with('entidad')
            ->when($id_entidad >= 1 && $id_entidad <= 33, function ($query) use ($id_entidad) {
                $query->where('id_entidad', $id_entidad);
            })
            ->orderBy('id_entidad', 'asc')
            ->orderBy('id_funcion', 'asc')
            ->orderBy('apellido_paterno', 'asc')
            ->orderBy('apellido_materno', 'asc')
            ->orderBy('nombre', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'Entidad')
            ->setCellValue('B1', 'Nombre')
            ->setCellValue('C1', 'Cargo')
            ->setCellValue('D1', 'Correo electronico')
            ->setCellValue('E1', 'Dirección')
            ->setCellValue('F1', 'Teléfono')
            ->setCellValue('G1', 'Función');

        $row = 2;
        foreach ($directorio as $registro) {
            $nombreEntidad = $registro->entidad->entidad ?? '';
            $nombreCompleto = trim("{$registro->nombre} {$registro->apellido_paterno} {$registro->apellido_materno}");
            $telefono = "({$registro->lada}) {$registro->telefono}";
            $nombreFuncion = $funciones[$registro->id_funcion] ?? '';

            $sheet->setCellValue("A{$row}", $nombreEntidad);
            $sheet->setCellValue("B{$row}", trim("{$registro->titulo} {$nombreCompleto}"));
            $sheet->setCellValue("C{$row}", $registro->cargo);
            $sheet->setCellValue("D{$row}", $registro->correo_electronico);
            $sheet->setCellValue("E{$row}", $registro->direccion);
            $sheet->setCellValue("F{$row}", $telefono);
            $sheet->setCellValue("G{$row}", $nombreFuncion);

            $row++;
        }

        $writer = new Xls($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'directorio.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * directorio
     * Muestra la información del directorio de autoridades mediante AJAX
     */
    public function directorio(Request $request)
    {
        $id_entidad = $request->input('Entidad.id');

        $entidad = Entidad::find($id_entidad);

        Disfuncion::$displayField = 'funcion';
        $funciones = Disfuncion::pluck('funcion', 'id');

        $directorio = Directorio::where('id_entidad', $id_entidad)
            ->orderBy('id_funcion')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        return view('directorios.directorio', compact('entidad', 'funciones', 'directorio'));
    }

    /**
     * entidad
     * Muestra el directorio de la entidad con las opciones de edición
     */
    public function entidad(Request $request)
    {
        $ctx = $this->contexto();
        $id_entidad = $ctx['id_entidad'] ?? session('Usuario.id_entidad');
        $status_enlace = $ctx['status'] ?? session('Usuario.status');
        $nivel_enlace = $ctx['nivel'] ?? session('Usuario.nivel');

        $cabeceras = [
            'No.',
            'Cargo',
            'Título',
            'Nombre',
            'Primer Ap.',
            'Segundo Ap.',
            'Correo Electrónico',
            'Función',
            ''
        ];

        $botones = ['terminar' => ''];
        $edita = '';
        $mensaje = '';

        if ($status_enlace == 'x' && $nivel_enlace == '2') {
            $edita = 'REVISE EL DIRECTORIO, SI ES NECESARIO ACTUALICE';
            $urlLogout = route('logout');
            $evento = "locacion('{$urlLogout}')";
            $botones['terminar'] = "<input type='button' value='Terminar' onclick=\"{$evento}\" class='btn btn-primary' />";
            $mensaje = 'Al dar clic en terminar se cerrará la sesión, Favor de iniciar sesión nuevamente';
        }

        $entidad = Entidad::find($id_entidad);

        Disfuncion::$displayField = 'funcion';
        $funciones = Disfuncion::pluck('funcion', 'id');

        $campos = ['id', 'id_funcion', 'cargo', 'titulo', 'nombre', 'apellido_paterno', 'apellido_materno', 'correo_electronico'];

        $directorio = Directorio::where('id_entidad', $id_entidad)
            ->select($campos)
            ->orderBy('id_funcion')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $urlEdita = route('directorios.editaEntrada');
        $renglones = [];

        foreach ($directorio as $entrada) {
            $parametros = "data[Directorio][id]={$entrada->id}";
            $evento = "consultaAjax('{$urlEdita}', '{$parametros}', muestraOverlay, muestraOcupado)";
            $titulo = 'Editar';
            $funcion = $funciones[$entrada->id_funcion] ?? '';

            $boton = "<input type=\"button\" id=\"b_{$entrada->id}\" onclick=\"{$evento}\" value=\"{$titulo}\" class=\"btn btn-primary\" style=\"font-size:0.8em;\" />";
            $indice = $entrada->id;

            $entradaArray = $entrada->toArray();
            $entradaArray['funcion'] = $funcion;
            $entradaArray['accion'] = $boton;

            unset($entradaArray['id'], $entradaArray['id_funcion']);

            $renglones[$indice] = $entradaArray;
        }

        return view('directorios.entidad', compact(
            'cabeceras',
            'botones',
            'entidad',
            'funciones',
            'renglones',
            'edita',
            'mensaje'
        ));
    }

    /**
     * editaEntrada
     * Formulario para editar un registro del directorio (vía AJAX)
     */
    public function editaEntrada(Request $request)
    {
        $id_directorio = $request->input('Directorio.id');
        $entrada = Directorio::find($id_directorio);

        Disfuncion::$displayField = 'funcion';
        $funciones = Disfuncion::pluck('funcion', 'id');

        return view('directorios.edita_entrada', compact('id_directorio', 'funciones', 'entrada'));
    }

    /**
     * agregaEntrada
     * Formulario para agregar una nueva entrada al directorio (vía AJAX)
     */
    public function agregaEntrada(Request $request)
    {
        Disfuncion::$displayField = 'funcion';
        $funciones = Disfuncion::pluck('funcion', 'id');

        return view('directorios.agrega_entrada', compact('funciones'));
    }

    /**
     * guardaEntrada
     * Guarda o actualiza un registro en el directorio
     */
    public function guardaEntrada(Request $request, $id_directorio = null)
    {
        $ctx = $this->contexto();
        $id_entidad = $ctx['id_entidad'] ?? session('Usuario.id_entidad');

        $dataDirectorio = $request->input('Directorio', []);

        if ($request->isMethod('post') && !empty($dataDirectorio)) {
            $dataDirectorio['id_entidad'] = $id_entidad;

            if ($id_directorio) {
                $directorio = Directorio::find($id_directorio);
                if ($directorio) {
                    $directorio->update($dataDirectorio);
                }
            } else {
                Directorio::create($dataDirectorio);
            }

            return redirect()->route('directorios.entidad', ['id_entidad' => $id_entidad]);
        }

        Disfuncion::$displayField = 'funcion';
        $funciones = Disfuncion::pluck('funcion', 'id');
        $directorio = Directorio::find($id_directorio);

        return view('elements.vacio', compact('id_directorio', 'funciones', 'directorio'));
    }
}