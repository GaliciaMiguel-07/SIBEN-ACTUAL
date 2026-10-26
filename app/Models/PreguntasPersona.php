<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntasPersona extends Model
{
    protected $table = 'p_preguntas_alumnos_docentes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
   
}
