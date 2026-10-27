<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoCeneval extends Model
{
    protected $table = 'alumnos_ceneval';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public function logAlumnoCeneval()
    {
        return $this->hasMany(
            LogAlumnoCeneval::class,
            'id_alumno_ceneval',
            'id'
        );
    }

}
