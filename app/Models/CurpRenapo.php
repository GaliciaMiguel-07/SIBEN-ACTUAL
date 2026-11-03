<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurpRenapo extends Model
{
    protected $table = 'curp_renapo';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'fecha_nacimiento_c' => 'date',
    ];

}
