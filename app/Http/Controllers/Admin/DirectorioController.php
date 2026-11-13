<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\sibenController;
use App\Models\Directorio;
use App\Models\Disfuncion;
use App\Models\Entidad;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class DirectorioController extends sibenController
{
    public function index()
    {
        $request = request();

        $entidades = Entidad::where('id', '<', 33)
            ->orderBy('entidad')
            ->pluck('entidad', 'id');

        $idEntidad = $request->integer('id_entidad');
        if (!$idEntidad && $entidades->isNotEmpty()) {
            $idEntidad = (int) $entidades->keys()->first();
        }

        $funciones = Disfuncion::orderBy('id')->pluck('funcion', 'id');

        $directorio = collect();
        if ($idEntidad) {
            $directorio = Directorio::where('id_entidad', $idEntidad)
                ->orderBy('id_funcion')
                ->orderBy('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre')
                ->get();
        }

        return view('admin.directorio.index', compact(
            'entidades',
            'idEntidad',
            'funciones',
            'directorio'
        ));
    }

    public function moverJerarquia(Request $request, Directorio $directorio)
    {
        $direccion = $request->string('direccion')->toString();

        $idsFuncion = Disfuncion::orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->values();
        $indiceActual = $idsFuncion->search((int) $directorio->id_funcion);

        if ($indiceActual !== false) {
            if ($direccion === 'subir' && $indiceActual > 0) {
                $directorio->id_funcion = $idsFuncion[$indiceActual - 1];
                $directorio->save();
            }

            if ($direccion === 'bajar' && $indiceActual < ($idsFuncion->count() - 1)) {
                $directorio->id_funcion = $idsFuncion[$indiceActual + 1];
                $directorio->save();
            }
        }

        return redirect()->route('admin.directorio.index', [
            'id_entidad' => $request->integer('id_entidad', $directorio->id_entidad),
        ]);
    }

    public function eliminar(Request $request, Directorio $directorio)
    {
        $idEntidad = $request->integer('id_entidad', $directorio->id_entidad);
        $directorio->delete();

        return redirect()->route('admin.directorio.index', ['id_entidad' => $idEntidad]);
    }

    public function exportarExcel(Request $request)
    {
        $idEntidad = $request->integer('id_entidad');

        abort_unless($idEntidad, 404);

        $entidad = Entidad::find($idEntidad);
        $funciones = Disfuncion::pluck('funcion', 'id');

        $registros = Directorio::where('id_entidad', $idEntidad)
            ->orderBy('id_funcion')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Directorio');
        $sheet->setCellValue('A2', 'Entidad: ' . ($entidad->entidad ?? ''));

        $sheet->setCellValue('A4', 'Nombre');
        $sheet->setCellValue('B4', 'Cargo');
        $sheet->setCellValue('C4', 'Función');
        $sheet->setCellValue('D4', 'Correo');
        $sheet->setCellValue('E4', 'Dirección');
        $sheet->setCellValue('F4', 'Teléfono');

        $row = 5;
        foreach ($registros as $registro) {
            $nombreCompleto = trim(implode(' ', array_filter([
                $registro->titulo,
                $registro->nombre,
                $registro->apellido_paterno,
                $registro->apellido_materno,
            ])));

            $sheet->setCellValue("A{$row}", $nombreCompleto);
            $sheet->setCellValue("B{$row}", $registro->cargo);
            $sheet->setCellValue("C{$row}", $funciones[$registro->id_funcion] ?? '');
            $sheet->setCellValue("D{$row}", $registro->correo_electronico);
            $sheet->setCellValue("E{$row}", $registro->direccion);
            $sheet->setCellValue("F{$row}", trim("({$registro->lada}) {$registro->telefono}"));
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xls($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'directorio_entidad_' . $idEntidad . '.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
