<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosgradosDocentes extends Model
{
    protected $table = 'p_docente_posgrado';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function docentesPosgrado()
    {
        return $this->belongsTo(DocentesPosgrado::class, 'id_docente');
    }

    public function posgrado()
    {
        return $this->belongsTo(Posgrado::class, 'id_posgrado');
    }

}
