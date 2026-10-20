<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'cat_especialidades';

    public $timestamps = false;

    public function escuelaLicenciaturas()
    {
        return $this->hasMany(
            EscuelaLicenciatura::class,
            'id_especialidad'
        );
    }

    public function licenciatura()
    {
        return $this->belongsTo(
            Licenciatura::class,
            'id_licenciatura'
        );
    }
}