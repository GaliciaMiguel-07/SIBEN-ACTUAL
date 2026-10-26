<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaracteristicaEscuela extends Model
{
    protected $table = 'caracteristicas_escuelas';

    public $timestamps = false;

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'id_escuela');
    }

    public function propiedad()
    {
        return $this->belongsTo(Construccion::class, 'propiedad');
    }

    public function estructura()
    {
        return $this->belongsTo(Construccion::class, 'estructura');
    }

    public function alumno($internet = null, $computo = null)
    {
        return $internet > $computo ? 1 : 0;
    }

    public function docente($internet = null, $computo = null)
    {
        return $internet > $computo ? 2 : 0;
    }

    public function administrativo($internet = null, $computo = null)
    {
        return $internet > $computo ? 3 : 0;
    }

    public function revisaestructura($estructura = null)
    {
        $errores = [];

        if (isset($estructura['internet_alumnos']) &&
            $this->alumno(
                $estructura['internet_alumnos'],
                $estructura['computo_alumnos'] ?? null
            ) !== 0) {
            $errores[1] = 'Error 1: El número de computadoras con Internet para Alumnos no debe ser mayor al número de computadoras para Alumnos.';
        }

        if (isset($estructura['internet_docentes']) &&
            $this->docente(
                $estructura['internet_docentes'],
                $estructura['computo_docentes'] ?? null
            ) !== 0) {
            $errores[2] = 'Error 2: El número de computadoras con Internet para Docentes no debe ser mayor al número de computadoras para Docentes.';
        }

        if (isset($estructura['internet_administrativo']) &&
            $this->administrativo(
                $estructura['internet_administrativo'],
                $estructura['computo_administrativo'] ?? null
            ) !== 0) {
            $errores[3] = 'Error 3: El número de computadoras con Internet para Administrativos no debe ser mayor al número de computadoras para Administrativos.';
        }

        return $errores;
    }
}
