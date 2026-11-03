<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurpRenapoper extends Model
{
    protected $table = 'curp_renapoper';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

}
