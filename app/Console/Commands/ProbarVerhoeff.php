<?php

namespace App\Console\Commands;

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProbarVerhoeff extends Command
{
    protected $signature = 'test:curp';
    protected $description = 'Prueba el parseo de CURP con siglo dinamico y edad exacta';

    public function handle()
    {
        $curpPrueba = 'RUPC040907MDFZBRA4'; // Pon aquí tu CURP real
        $capturaPrueba = '2026-11-03';
        
        $fechaCaptura = Carbon::parse($capturaPrueba);

        if (!empty($curpPrueba)) {
            $dCurp = substr($curpPrueba, 8, 2);
            $mCurp = substr($curpPrueba, 6, 2);
            $aDigitos = substr($curpPrueba, 4, 2);
            
            // Detección dinámica de siglo (si los dos dígitos de la CURP son mayores al año actual, es 19xx, si no 20xx)
            $siglo = (int)$aDigitos > (int)$fechaCaptura->format('y') ? '19' : '20';
            $aCurp = $siglo . $aDigitos;
            
            $entidadCurp = substr($curpPrueba, 11, 2);

            // Cálculo exacto de la edad usando Carbon y la fecha de captura
            $fechaNacimiento = Carbon::createFromDate($aCurp, (int)$mCurp, (int)$dCurp);
             // diffInYears calcula exactamente si ya cumplió años respecto a la fecha de captura
            $edad = (int) $fechaNacimiento->diffInYears($fechaCaptura);

            // Consulta en la tabla cat_entidades
            $entidad = DB::table('cat_entidades')->where('id_renapo', $entidadCurp)->first();
            $idEntidad = $entidad ? $entidad->id : null;

            $datos = [
                'dia' => $dCurp,
                'mes' => $mCurp,
                'anio' => $aCurp,
                'edad' => $edad,
                'entidad' => $idEntidad,
                'renapo' => $entidadCurp,
                'genero' => strtoupper($curpPrueba[10]),
            ];

            $this->info("Resultado corregido del parseo de CURP en Laravel:");
            dump($datos);
        }
    }
}