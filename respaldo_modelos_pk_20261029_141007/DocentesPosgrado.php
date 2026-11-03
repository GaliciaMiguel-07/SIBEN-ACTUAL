<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocentesPosgrado extends Model
{
    protected $table = 'p_docentes';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'fecha_alta' => 'datetime',
    ];

    public function experienciaDocente()
    {
        return $this->hasMany(ExperienciaDocente::class, 'id_docente');
    }

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'id_escuela');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

}
