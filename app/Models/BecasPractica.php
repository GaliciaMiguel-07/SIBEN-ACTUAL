<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BecasPractica extends Model
{
    protected $table = 'becas_practica';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public function registro()
    {
        return $this->belongsTo(
            Registro::class,
            'id_registro',
            'id'
        );
    }
}
