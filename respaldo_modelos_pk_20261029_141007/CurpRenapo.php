<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurpRenapo extends Model
{
    protected $table = 'curp_renapo';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'fecha_nacimiento_c' => 'date',
    ];

}
