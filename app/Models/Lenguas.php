<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lenguas extends Model
{
    protected $table = 'cat_lenguas2';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

}
