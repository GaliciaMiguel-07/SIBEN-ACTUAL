<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aptitud extends Model
{
    protected $table = 'cat_aptitud';
    protected $primaryKey = 'id';
    public $incrementing = true;    
    public $timestamps = false;
    protected $guarded = [];

}
