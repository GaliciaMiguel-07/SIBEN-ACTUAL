<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutorizacionesPosgrado extends Model
{
    protected $table = 'p_autorizaciones';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'fecha_autorizacion' => 'date',
    ];

    public function posgrado()
    {
        return $this->belongsTo(Posgrado::class, 'id_posgrado');
    }

    public function autorizacion()
    {
        return $this->belongsTo(Autorizacion::class, 'id_autorizacion');
    }

}
