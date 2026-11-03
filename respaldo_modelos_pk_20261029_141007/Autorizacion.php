<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autorizacion extends Model
{
    protected $table = 'p_cat_autorizaciones';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

}
