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
            'renapo' => '',
            'edad' => 0,
            'genero' => '',
        ];

        if (!preg_match('/^[A-Z0-9]{18}$/', $curp)) {
            return $invalida;
        }

        $anioCorto = (int) substr($curp, 4, 2);
        $mes = (int) substr($curp, 6, 2);
        $dia = (int) substr($curp, 8, 2);

        $homoclave = $curp[16];

        $anio = ctype_digit($homoclave)
            ? 1900 + $anioCorto
            : 2000 + $anioCorto;

        if (!checkdate($mes, $dia, $anio)) {
            return $invalida;
        }

        $renapo = substr($curp, 11, 2);

        $entidad = DB::table('cat_entidades')
            ->where('id_renapo', $renapo)
            ->value('id');

        if (!$entidad) {
            return $invalida;
        }

        $anioCaptura = (int) substr((string) $captura, 0, 4);

        return [
            'dia' => sprintf('%02d', $dia),
            'mes' => sprintf('%02d', $mes),
            'anio' => $anio,
            'entidad' => $entidad,
            'renapo' => $renapo,
            'edad' => $anioCaptura - $anio,
            'genero' => $curp[10],
        ];
    }

    public static function calculaVerhoeffStatic($numero)
    {
        $tabla = [
            [0,1,2,3,4,5,6,7,8,9],
            [1,2,3,4,0,6,7,8,9,5],
            [2,3,4,0,1,7,8,9,5,6],
            [3,4,0,1,2,8,9,5,6,7],
            [4,0,1,2,3,9,5,6,7,8],
            [5,9,8,7,6,0,4,3,2,1],
            [6,5,9,8,7,1,0,4,3,2],
            [7,6,5,9,8,2,1,0,4,3],
            [8,7,6,5,9,3,2,1,0,4],
            [9,8,7,6,5,4,3,2,1,0]
        ];

        $permutaciones = [
            [0,1,2,3,4,5,6,7,8,9],
            [1,5,7,6,2,8,3,0,9,4],
            [5,8,0,3,7,9,6,1,4,2],
            [8,9,1,6,0,4,3,5,2,7],
            [9,4,5,3,1,2,6,8,7,0],
            [4,2,8,6,5,7,3,9,0,1],
            [2,7,9,3,8,0,6,4,1,5],
            [7,0,4,6,9,1,3,2,5,8]
        ];

        $inversos = [0,4,3,2,1,5,6,7,8,9];

        $c = 0;
        $numero = strrev((string) $numero);

        for ($i = 0; $i < strlen($numero); $i++) {
            $digito = (int) $numero[$i];
            $c = $tabla[$c][$permutaciones[($i + 1) % 8][$digito]];
        }

        return $inversos[$c];
    }

    public static function validaVerhoeffStatic($numero)
    {
        $tabla = [
            [0,1,2,3,4,5,6,7,8,9],
            [1,2,3,4,0,6,7,8,9,5],
            [2,3,4,0,1,7,8,9,5,6],
            [3,4,0,1,2,8,9,5,6,7],
            [4,0,1,2,3,9,5,6,7,8],
            [5,9,8,7,6,0,4,3,2,1],
            [6,5,9,8,7,1,0,4,3,2],
            [7,6,5,9,8,2,1,0,4,3],
            [8,7,6,5,9,3,2,1,0,4],
            [9,8,7,6,5,4,3,2,1,0]
        ];

        $permutaciones = [
            [0,1,2,3,4,5,6,7,8,9],
            [1,5,7,6,2,8,3,0,9,4],
            [5,8,0,3,7,9,6,1,4,2],
            [8,9,1,6,0,4,3,5,2,7],
            [9,4,5,3,1,2,6,8,7,0],
            [4,2,8,6,5,7,3,9,0,1],
            [2,7,9,3,8,0,6,4,1,5],
            [7,0,4,6,9,1,3,2,5,8]
        ];

        if (!preg_match('/^[0-9]+$/', (string) $numero)) {
            return false;
        }

        $c = 0;
        $numero = strrev((string) $numero);

        for ($i = 0; $i < strlen($numero); $i++) {
            $digito = (int) $numero[$i];
            $c = $tabla[$c][$permutaciones[$i % 8][$digito]];
        }

        return $c === 0;
    }

    public static function generarNidStatic($captura)
    {
        $anio = substr((string) $captura, 2, 2);

        $ultimo = DB::table('alumnos')
            ->where('numero_identificador', 'like', $anio . '%')
            ->orderByDesc('numero_identificador')
            ->value('numero_identificador');

        if ($ultimo) {
            $base = substr((string) $ultimo, 0, 7);
            $base = (string) ((int) $base + 1);
        } else {
            $base = ($anio <= 9 ? '0' : '') . $anio . '00001';
        }

        return $base . self::calculaVerhoeffStatic($base);
    }

public static function validaNombreStatic($data)
{
    $patron = '/[^a-zA-Z ñÑ]+/';

    foreach (['apellido_paterno', 'apellido_materno', 'nombre'] as $campo) {
        if (isset($data[$campo]) && preg_match($patron, (string) $data[$campo])) {
            return false;
        }
    }

    return true;
}

public static function validaEntidadStatic($data)
{
    if (
        empty($data['curp']) ||
        empty($data['id_renapo']) ||
        strlen((string) $data['curp']) !== 18
    ) {
        return false;
    }

    $renapoCurp = substr(strtoupper($data['curp']), 11, 2);

    return strtoupper(trim((string) $data['id_renapo'])) === $renapoCurp;
}

}