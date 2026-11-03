<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntidadNal extends Model
{
    protected $table = 'cat_entidades_nal';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

}
