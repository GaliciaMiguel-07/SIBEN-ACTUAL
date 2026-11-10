<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Alumno extends Model
{
    protected $table = 'alumnos';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public static function infoCurpStatic($curp, $captura = null)
    {
        $curp = strtoupper(trim((string) $curp));

        $invalida = [
            'dia' => 99,
            'mes' => 99,
            'anio' => 0,
            'entidad' => 0,
        ];

        if (!preg_match('/^[A-Z0-9]{18}$/', $curp)) {
            return $invalida;
        }

        $anioCorto = (int) substr($curp, 4, 2);
        $mes = (int) substr($curp, 6, 2);
        $dia = (int) substr($curp, 8, 2);

        $siglo = $curp[16];

        $anio = ctype_digit($siglo)
            ? 2000 + $anioCorto
            : 1900 + $anioCorto;

        if (!checkdate($mes, $dia, $anio)) {
            return $invalida;
        }

        $claveEntidad = substr($curp, 11, 2);

        $entidad = DB::table('cat_entidades')
            ->where('id_renapo', $claveEntidad)
            ->value('id');

        if (!$entidad) {
            return $invalida;
        }

        return [
            'dia' => sprintf('%02d', $dia),
            'mes' => sprintf('%02d', $mes),
            'anio' => $anio,
            'entidad' => $entidad,
        ];
    }
}