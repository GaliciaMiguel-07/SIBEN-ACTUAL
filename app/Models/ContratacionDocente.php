<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratacionDocente extends Model
{
    protected $table = 'contrataciones_docentes';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

}
