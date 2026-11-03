<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalC extends Model
{
    protected $table = 'personal_curp';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function alumno()
    {
        return $this->hasOne(Alumno::class, 'numero_identificador');
    }

}
