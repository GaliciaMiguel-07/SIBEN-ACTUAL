<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoBachillerato extends Model
{
    protected $table = 'alumnos_bachillerato';

    protected $primaryKey = 'nid';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(
            Alumno::class,
            'nid',
            'numero_identificador'
        );
    }

    public function registro()
    {
        return $this->hasOne(
            Registro::class,
            'nid',
            'nid'
        );
    }
}
