<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomicilioEscuela extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla (equivale a $useTable = 'domicilio_escuela')
     */
    protected $table = 'domicilio_escuela';

    /**
     * Llave primaria. Laravel asume 'id' por defecto, pero se define por seguridad.
     */
    protected $primaryKey = 'id';

    /**
     * Desactiva los campos created_at y updated_at que Laravel busca por defecto.
     */
    public $timestamps = false;

    /**
     * Permite la asignación masiva de todos los campos.
     */
    protected $guarded = [];

    // ─── Relaciones (Equivalente a $belongsTo en CakePHP) ─────────────

    /**
     * Relación con el modelo Escuela
     */
    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'id_escuela');
    }

    /**
     * Relación con el modelo Entidad
     */
    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'cve_ent');
    }

    /**
     * Relación con el modelo Municipio
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'cve_mun');
    }
}