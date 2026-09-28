<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumnos';

    // La PK real es `id` (auto-incremental entero).
    // `numero_identificador` es un campo UNIQUE pero NO es la llave primaria.
    protected $primaryKey = 'id';

    // Los timestamps (created_at, updated_at) no existen en esta tabla
    public $timestamps = false;
}
