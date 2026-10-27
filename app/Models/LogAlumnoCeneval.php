<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAlumnoCeneval extends Model
{
    protected $table = 'log_correos_ceneval';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public function alumnoCeneval()
    {
        return $this->belongsTo(
            AlumnoCeneval::class,
            'id_alumno_ceneval',
            'id'
        );
    }
}
