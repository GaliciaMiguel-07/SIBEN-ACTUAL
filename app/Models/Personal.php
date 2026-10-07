<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla explícita (equivale a $useTable = 'personal2').
     */
    protected $table = 'personal2';

    /**
     * Llave primaria de la tabla.
     */
    protected $primaryKey = 'id';

    /**
     * Desactiva el control automático de timestamps (created_at / updated_at).
     */
    public $timestamps = false;

    /**
     * Permite la asignación masiva de todos los atributos.
     */
    protected $guarded = [];

    /**
     * Regla de validación traducida para CURP.
     * Puede usarse en Form Requests o con Validator::make().
     */
    public static array $rules = [
        'curp' => ['required', 'regex:/^[A-Z]{4}\d{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9][0-9]$/'],
    ];

    // ─── Relaciones (CakePHP $hasOne / $belongsTo) ──────────────────────────

    /**
     * Relación uno a uno con Docente (hasOne).
     */
    public function docente()
    {
        return $this->hasOne(Docente::class, 'id_personal');
    }

    /**
     * Relación de pertenencia a Escuela (belongsTo).
     */
    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'id_escuela');
    }

    /**
     * Relación de pertenencia a Función (belongsTo).
     */
    public function funcion()
    {
        return $this->belongsTo(Funcion::class, 'id_funcion');
    }

    // ─── Métodos Auxiliares ──────────────────────────────────────────────────

    /**
     * Genera un reporte formateado en formato TSV (valores separados por tabulador)
     * para exportar el personal activo asignado a una escuela.
     */
    public function csv(?int $id_escuela = null): string
    {
        if (!$id_escuela) {
            return '';
        }

        // Carga ansiosa (Eager Loading) de relaciones con 'with' para evitar problemas N+1
        $personal = self::with(['funcion', 'docente'])
            ->where('id_escuela', $id_escuela)
            ->where('status', 'a')
            ->orderBy('id_funcion')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $csv  = "Apellido paterno\t";
        $csv .= "Apellido materno\t";
        $csv .= "Nombres\t";
        $csv .= "CURP\t";
        $csv .= "Edad\t";
        $csv .= "Género\t";
        $csv .= "Correo electrónico\t";
        $csv .= "Función\t";
        $csv .= "Forma contratación\t";
        $csv .= "Tiempo dedicación\t";
        $csv .= "Nivel max estudio\t";
        $csv .= "Avance nivel max\t";
        $csv .= "Egresado de\t";
        $csv .= "Fecha de tiulación\t";
        $csv .= "Tema de titulación\n";

        foreach ($personal as $persona) {
            $csv .= ($persona->apellido_paterno ?? '') . "\t";
            $csv .= ($persona->apellido_materno ?? '') . "\t";
            $csv .= ($persona->nombre ?? '') . "\t";
            $csv .= ($persona->curp ?? '') . "\t";
            $csv .= ($persona->edad ?? '') . "\t";
            $csv .= ($persona->genero ?? '') . "\t";
            $csv .= ($persona->correo_electronico ?? '') . "\t";
            $csv .= ($persona->funcion->funcion ?? '') . "\t";
            $csv .= ($persona->docente?->forma_contratacion ?? '') . "\t";
            $csv .= ($persona->docente?->tiempo_contratacion ?? '') . "\t";
            $csv .= ($persona->docente?->id_nivel_estudios ?? '') . "\t";
            $csv .= ($persona->docente?->titulo ?? '') . "\t";
            $csv .= ($persona->docente?->escuela_egreso ?? '') . "\t";
            $csv .= ($persona->docente?->fecha_titulacion ?? '') . "\t";
            $csv .= ($persona->docente?->tema_titulacion ?? '') . "\n";
        }

        return $csv;
    }
}