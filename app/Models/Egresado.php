<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Egresado extends Model
{
    protected $table = 'egresados';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'fecha_titulacion' => 'date',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'nid');
    }

    public function veficacionEgreso()
    {
    
    
    }

}
