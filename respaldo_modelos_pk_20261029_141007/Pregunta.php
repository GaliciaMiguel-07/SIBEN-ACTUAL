<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    protected $table = 'preguntas_handicapeados';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function opcionPregunta()
    {
        return $this->hasMany(OpcionPregunta::class, 'id_pregunta');
    }

}
