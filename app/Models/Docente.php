<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docentes';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    // Relación inversa (CakePHP $belongsTo Personal)
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'id_personal');
    }
}