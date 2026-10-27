<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudios extends Model
{
    protected $table = 'p_cat_estudios';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function estudiosRealizados()
    {
        return $this->hasMany(EstudiosRealizados::class, 'id_estudio');
    }
}
