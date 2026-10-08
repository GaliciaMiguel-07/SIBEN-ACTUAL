<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelEstudio extends Model
{
    protected $table = 'cat_nivel_estudios';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}