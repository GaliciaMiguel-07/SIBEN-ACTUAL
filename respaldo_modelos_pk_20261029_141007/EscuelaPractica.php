<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscuelaPractica extends Model
{
    protected $table = 'escuelas_practica';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    // TODO: revisar método 'buscaEscuela($clave_ct = null, $id = null)': dependía del ORM de CakePHP ($this->find).

}
