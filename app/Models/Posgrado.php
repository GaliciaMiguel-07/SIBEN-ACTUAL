<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posgrado extends Model
{
    protected $table = 'p_posgrados';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'id_escuela');
    }

    public function alumnosPosgrado()
    {
        return $this->hasMany(AlumnosPosgrado::class, 'id_posgrado');
    }
}
