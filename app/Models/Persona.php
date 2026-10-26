<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'p_personas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function preguntasPersona()
    {
        return $this->hasMany(PreguntasPersona::class, 'id_persona');
    }

    public function lenguaHabilidades()
    {
        return $this->hasMany(LenguaHabilidades::class, 'id_persona');
    }

    public function estudiosRealizados()
    {
        return $this->hasMany(EstudiosRealizados::class, 'id_persona');
    }

    public function docentesPosgrado()
    {
        return $this->hasOne(DocentesPosgrado::class, 'id_persona');
    }

    public function alumnosPosgrado()
    {
        return $this->hasOne(AlumnosPosgrado::class, 'id_persona');
    }

    public function datosCurp($curp)
    {
        if (!empty($curp)) {
            $d = $curp[8] . $curp[9];
            $m = $curp[6] . $curp[7];
            $a = '19' . $curp[4] . $curp[5];

            $fecha = $a . '-' . $m . '-' . $d;
            $datos = [
                'dia' => $d,
                'mes' => $m,
                'year' => $a,
            ];

            $anio_dif = 2009 - $a;
            $mes_dif = 8 - $m;
            $dia_dif = 24 - $d;

            if ($dia_dif < 0) {
                $mes_dif--;
            }

            if ($mes_dif < 0) {
                $anio_dif--;
            }

            $edad = $anio_dif;
            $datos['edad'] = $edad;

            $entidad_nacimiento = $curp[11] . $curp[12];
            $datos['entidad'] = $entidad_nacimiento;
            $datos['genero'] = $curp[10];
        }

        return $datos;
    }
    
}
