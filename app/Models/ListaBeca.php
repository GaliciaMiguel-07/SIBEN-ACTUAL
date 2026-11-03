<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaBeca extends Model
{
    protected $table = 'listas_becas';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'fecha_cobro' => 'date',
        'fecha_nacimiento' => 'date',
    ];

}
