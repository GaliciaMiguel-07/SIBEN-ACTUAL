<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenguasIndigenas extends Model
{
    protected $table = 'cat_lenguas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

}
