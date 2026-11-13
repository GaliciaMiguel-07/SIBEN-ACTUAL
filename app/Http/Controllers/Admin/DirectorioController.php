<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\sibenController;
use App\Models\Directorio;
use App\Models\Disfuncion;
use App\Models\Entidad;
use Illuminate\Http\Request;

/**
 * Directorio (línea de mando) compartido por nivel 1 y nivel 2.
 * - Nivel 1: cualquier entidad, puede eliminar.
 * - Nivel 2: solo su entidad, sin eliminar.
 */
class DirectorioController extends sibenController
{
    private function esAdmin(): bool
    {
        return (int) auth()->user()?->nivel === 1;
    }

    /** Entidad efectiva: nivel 2 siempre queda fijado a la suya. */
    private function entidadPermitida(?int $solicitada): ?int
    {
        if ($this->esAdmin()) {
            return $solicitada ?: null;
        }

        $propia = (int) ($this->contexto()['id_entidad'] ?? 0);
        abort_unless($propia, 403, 'Tu usuario no tiene entidad asignada.');

        return $propia;
    }

    private function autorizarRegistro(Directorio $directorio): void
    {
        if (!$this->esAdmin()) {
            abort_unless((int) $directorio->id_entidad === $this->entidadPermitida(null), 403);
        }
    }

    private function registros(int $idEntidad)
    {
        return Directorio::where('id_entidad', $idEntidad)
            ->orderBy('id_funcion')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();
    }

    public function index()
    {
        $esAdmin = $this->esAdmin();

        $entidades = $esAdmin
            ? Entidad::where('id', '<', 33)->orderBy('entidad')->pluck('entidad', 'id')
            : collect();

        $idEntidad = $this->entidadPermitida(request()->integer('id_entidad'));
        if (!$idEntidad && $entidades->isNotEmpty()) {
            $idEntidad = (int) $entidades->keys()->first();
        }

        $nombreEntidad = $idEntidad ? Entidad::where('id', $idEntidad)->value('entidad') : null;
        $funciones     = Disfuncion::orderBy('id')->pluck('funcion', 'id');
        $directorio    = $idEntidad ? $this->registros($idEntidad) : collect();

        return view('admin.directorio.index', compact(
            'esAdmin', 'entidades', 'idEntidad', 'nombreEntidad', 'funciones', 'directorio'
        ));
    }

    public function moverJerarquia(Request $request, Directorio $directorio)
    {
        $this->autorizarRegistro($directorio);

        $ids    = Disfuncion::orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->values();
        $actual = $ids->search((int) $directorio->id_funcion);
        $paso   = $request->input('direccion') === 'subir' ? -1 : 1;

        if ($actual !== false && isset($ids[$actual + $paso])) {
            $directorio->id_funcion = $ids[$actual + $paso];
            $directorio->save();
        }

        return redirect()->route('directorio.gestion.index', ['id_entidad' => $directorio->id_entidad]);
    }

    public function eliminar(Directorio $directorio)
    {
        abort_unless($this->esAdmin(), 403);

        $idEntidad = $directorio->id_entidad;
        $directorio->delete();

        return redirect()->route('directorio.gestion.index', ['id_entidad' => $idEntidad]);
    }

    /**
     * Excel sin dependencias externas (tabla HTML con extensión .xls,
     * Excel la abre directamente). Mantiene el orden jerárquico.
     */
    public function exportarExcel(Request $request)
    {
        $idEntidad = $this->entidadPermitida($request->integer('id_entidad'));
        abort_unless($idEntidad, 404);

        $html = view('admin.directorio.excel', [
            'entidad'    => Entidad::where('id', $idEntidad)->value('entidad'),
            'funciones'  => Disfuncion::pluck('funcion', 'id'),
            'registros'  => $this->registros($idEntidad),
        ])->render();

        return response("\xEF\xBB\xBF" . $html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="directorio_entidad_' . $idEntidad . '.xls"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
