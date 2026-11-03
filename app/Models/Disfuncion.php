<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disfuncion extends Model
{
    protected $table = 'cat_funciones_directorio';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

}
