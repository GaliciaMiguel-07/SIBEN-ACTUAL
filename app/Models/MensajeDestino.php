<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensajeDestino extends Model
{
    protected $table = 'mensajes_destinos';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
