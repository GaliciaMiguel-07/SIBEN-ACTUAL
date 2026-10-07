<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcion extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla explícita en la base de datos (equivale a $useTable = 'cat_funciones').
     */
    protected $table = 'cat_funciones';

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
}