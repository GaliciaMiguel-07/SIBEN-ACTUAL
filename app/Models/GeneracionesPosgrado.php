<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneracionesPosgrado extends Model
{
    protected $table = 'p_generaciones';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'fecha_fin' => 'date',
        'fecha_inicio' => 'date',
    ];

    public function posgrado()
    {
        return $this->belongsTo(Posgrado::class, 'id_posgrado');
    }

}
