<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaConocimiento extends Model
{
    protected $table = 'cat_areas_conocimiento';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}