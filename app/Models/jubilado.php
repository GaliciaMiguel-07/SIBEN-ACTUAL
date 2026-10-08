<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jubilado extends Model
{
    protected $table = 'jubilados';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    // Un jubilado pertenece a una persona (tabla personal2)
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'id_personal');
    }
}
