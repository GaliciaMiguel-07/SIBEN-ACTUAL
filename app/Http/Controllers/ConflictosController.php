<?php

namespace App\Http\Controllers;

use App\Models\Escuela2;
use App\Models\Historia;
use Illuminate\Http\Request;

class ConflictosController extends sibenController
{
    /**
     * index
     * Se muestra la lista de conflictos de acuerdo a la entidad del Enlace
     */
    public function index(Request $request)
    {
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $id_entidad = $ctx['id_entidad'] ?? null;
        $modulo = $ctx['modulo'] ?? null;
        $captura_actual = $ctx['captura_actual'] ?? session('captura_actual', 1);

        // Seguridad estricta: sólo permitido para Enlaces (nivel 2)
        if ($nivel != 2) {
            return redirect()->route('siben.index');
        }

        // Obtenemos la lista de escuelas de la entidad y módulo correspondientes
        $ids_escuelas = Escuela2::where('id_entidad', $id_entidad)
            ->where('status', 'a')
            ->where('unica', '<', 2)
            ->where('modulo', $modulo)
            ->pluck('id');

        // Buscamos los conflictos no resueltos
        $campos = [
            'nid',
            'apellido_paterno',
            'apellido_materno',
            'nombre',
            'id_registro',
            'id_escuela',
            'id_licenciatura',
            'id_especialidad',
            'modalidad',
            'semestre',
            'id_escuela_origen',
            'id_licenciatura_destino',
            'id_especialidad_destino',
            'modalidad_destino',
            'semestre_destino'
        ];

        $conflictos = Historia::whereIn('id_escuela', $ids_escuelas)
            ->where('captura', $captura_actual)
            ->where('status_conflictos', 'n')
            ->select($campos)
            ->get();

        return view('conflictos.index', compact('conflictos'));
    }
}