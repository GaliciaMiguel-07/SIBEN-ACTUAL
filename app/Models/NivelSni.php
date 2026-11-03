<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelSni extends Model
{
    protected $table = 'cat_nivel_sni';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

}
