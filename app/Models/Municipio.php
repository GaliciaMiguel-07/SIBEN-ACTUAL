<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla explícita (equivale a $useTable = 'cat_municipios').
     */
    protected $table = 'cat_municipios';

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

    // ─── Relaciones (CakePHP $belongsTo / $hasMany) ──────────────────────────

    /**
     * Entidad a la que pertenece el municipio (belongsTo).
     */
    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'id_entidad');
    }

    /**
     * Relación uno a muchos con Escuelas Prácticas (hasMany).
     */
    public function escuelasPracticas()
    {
        return $this->hasMany(EscuelaPractica::class, 'id_municipio');
    }

    /**
     * Relación uno a muchos con Bachilleratos (hasMany).
     */
    public function bachilleratos()
    {
        return $this->hasMany(Bachillerato::class, 'id_municipio2');
    }
}