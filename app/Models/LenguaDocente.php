<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenguaDocente extends Model
{
    protected $table = 'lenguas_docentes';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
