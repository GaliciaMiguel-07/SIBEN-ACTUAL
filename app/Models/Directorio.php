<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directorio extends Model
{
    protected $table = 'directorio';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'id_entidad', 'id');
    }
}
