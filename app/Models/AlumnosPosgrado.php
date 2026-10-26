<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnosPosgrado extends Model
{
    protected $table = 'p_alumnos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'id_escuela');
    }

    public function posgrado()
    {
        return $this->belongsTo(Posgrado::class, 'id_posgrado');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
}
