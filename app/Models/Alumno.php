<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    // 1. Apuntamos a la tabla correcta
    protected $table = 'alumnos';

    // 2. Le decimos cuál es su verdadera llave primaria
    protected $primaryKey = 'numero_identificador';

    // 3. IMPORTANTÍSIMO: Le decimos que la llave NO es un número autoincrementable
    public $incrementing = false;

    // 4. Le decimos que el tipo de dato de la llave es texto ('string')
    protected $keyType = 'string';

    // Desactivamos los timestamps si la tabla vieja no los tenía (created_at, updated_at)
    public $timestamps = false; 
}