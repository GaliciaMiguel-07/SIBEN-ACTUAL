<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstudiosRealizados extends Model
{
    protected $table = 'p_estudios_realizados';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function modosTitulacion()
    {
        return $this->belongsTo(ModosTitulacion::class, 'id_modo_titulacion');
    }

    public function estudios()
    {
        return $this->belongsTo(Estudios::class, 'id_estudio');
    }
}
