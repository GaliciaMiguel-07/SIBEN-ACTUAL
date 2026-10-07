<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escuela2 extends Model
{
    use HasFactory;

    /**
     * Nombre explícito de la tabla en la base de datos.
     * Equivale a $useTable = 'escuelas2' de CakePHP.
     *
     * @var string
     */
    protected $table = 'escuelas2';

    /**
     * Llave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Desactiva los timestamps si la tabla no usa created_at / updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Desprotege la asignación masiva de campos.
     *
     * @var array
     */
    protected $guarded = [];
}