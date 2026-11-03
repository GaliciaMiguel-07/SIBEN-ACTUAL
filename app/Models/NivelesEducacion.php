<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelesEducacion extends Model
{
    protected $table = 'p_niveles_educacion';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

}
