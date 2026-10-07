<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla explícita en la base de datos (equivale a $useTable = 'cat_localidades').
     */
    protected $table = 'cat_localidades';

    /**
     * Desactiva el control automático de marcas de tiempo (created_at y updated_at).
     */
    public $timestamps = false;

    /**
     * Permite la asignación masiva de atributos.
     */
    protected $guarded = [];
}