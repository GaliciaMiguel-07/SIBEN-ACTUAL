<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratacionesPosgrado extends Model
{
    protected $table = 'p_docentes_contratacion';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function docentesPosgrado()
    {
        return $this->belongsTo(DocentesPosgrado::class, 'id_docente');
    }

}
