<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conflicto extends Model
{
    protected $table = 'conflictos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function conflictos($id_entidad)
    {
    
    
    
    }

}
