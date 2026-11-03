<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscuelaPractica extends Model
{
    protected $table = 'escuelas_practica';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    // TODO: revisar mÃ©todo 'buscaEscuela($clave_ct = null, $id = null)': dependÃ­a del ORM de CakePHP ($this->find).

}
