<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormaContratacion extends Model
{
    protected $table = 'cat_contrataciones';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

}
