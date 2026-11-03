<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienciaDocente extends Model
{
    protected $table = 'p_experiencia_docente';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function docentesPosgrado()
    {
        return $this->belongsTo(DocentesPosgrado::class, 'id_docente');
    }

    public function nivelesEducacion()
    {
        return $this->belongsTo(NivelesEducacion::class, 'id_nivel');
    }

}
