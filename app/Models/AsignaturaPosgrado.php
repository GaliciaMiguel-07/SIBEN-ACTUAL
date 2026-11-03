<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignaturaPosgrado extends Model
{
    protected $table = 'asignaturas_posgrados';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

}
