<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscuelaLicenciatura extends Model
{
    protected $table = 'escuelas_licenciaturas';

    public $timestamps = false;

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'id_escuela');
    }

    public function licenciatura()
    {
        return $this->belongsTo(Licenciatura::class, 'id_licenciatura');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'id_personal');
    }
}