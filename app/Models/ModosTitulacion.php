<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModosTitulacion extends Model
{
    protected $table = 'p_cat_modos_titulacion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function estudiosRealizados()
    {
        return $this->hasMany(EstudiosRealizados::class, 'id_modo_titulacion');
    }
}
