<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Baja extends Model
{
    protected $table = 'bajas';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(
            Alumno::class,
            'nid',
            'numero_identificador'
        );
    }

    public function revisarBaja($nid = null, $id_escuela = null)
    {
        $baja = self::where('nid', $nid)
            ->where('id_escuela', $id_escuela)
            ->first();

        if ($baja) {
            return $baja->delete();
        }

        return false;
    }
}
