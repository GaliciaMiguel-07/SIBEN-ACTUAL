<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    use HasFactory;

    protected $table = 'cat_localidades';

    // La llave primaria real de la tabla.
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];
}
