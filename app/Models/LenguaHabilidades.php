<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenguaHabilidades extends Model
{
    protected $table = 'p_lengua_habilidades';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function extranjeraIndigena()
    {
        return $this->belongsTo(ExtrangeraIndigena::class, 'id_extrangera_indigena');
    }
}
