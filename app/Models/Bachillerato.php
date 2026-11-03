<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bachillerato extends Model
{
    protected $table = 'cat_bachilleratos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'id_entidad');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id');
    }

}
