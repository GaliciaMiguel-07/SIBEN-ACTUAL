<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpcionPregunta extends Model
{
    protected $table = 'opciones_preguntas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

}
