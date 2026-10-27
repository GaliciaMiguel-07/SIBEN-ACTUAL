<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoC extends Model
{
    protected $table = 'alumnos_curp';
    protected $primaryKey = 'numero_identificador';
    public $timestamps = false;

    public $incrementing = false;
    protected $keyType = 'string';
}
