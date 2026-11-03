<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurpRenapoper extends Model
{
    protected $table = 'curp_renapoper';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

}
