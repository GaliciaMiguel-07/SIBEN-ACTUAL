<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historia extends Model
{
    /**
     * Tabla utilizada por el modelo.
     */
    protected $table = 'historia';
    
    public $timestamps = false;

    /*
     * Si la tabla no tiene las columnas created_at y updated_at,
     * descomenta la siguiente línea:
     *
     * public $timestamps = false;
     */

    /**
     * Genera las opciones de semestre disponibles para reinscripción
     * y edición, considerando el ciclo de captura, la entidad
     * y las excepciones de determinadas escuelas.
     */
    public function CalcularSemestre(
        $semestre = null,
        $capturaActual = null,
        $nombreEntidad = null,
        $id_escuela = null
    ) {
        $ListaSemestres = array();
        $ParImpar = $capturaActual % 2;

        switch ($ParImpar) {
            case 0:
                if ($semestre % 2 == 0) {
                    for ($i = $semestre; $i > 0; $i--) {
                        if ($i % 2 == 0) {
                            $ListaSemestres[$i] = $i;

                            if (count($ListaSemestres) == 2) {
                                break;
                            }
                        }
                    }
                } else {
                    if (($semestre - 1) % 2 == 0 && $semestre - 1 > 0) {
                        $ListaSemestres = array(
                            $semestre - 1 => $semestre - 1
                        );
                    }

                    if ($nombreEntidad == 'TS' && $semestre - 1 == 2) {
                        $ListaSemestres['3'] = '3';
                    }

                    if ($nombreEntidad == 'TS' && $semestre - 1 == 4) {
                        $ListaSemestres['5'] = '5';
                    }

                    if ($nombreEntidad == 'TS' && $semestre - 1 == 6) {
                        $ListaSemestres['7'] = '7';
                    }

                    if ($nombreEntidad == 'TS' && $semestre - 1 == 7) {
                        $ListaSemestres['8'] = '8';
                    }

                    if ($id_escuela == 180 && $semestre - 1 == 7) {
                        $ListaSemestres['8'] = '8';
                    }

                    if ($id_escuela == 193 && $semestre - 1 == 7) {
                        $ListaSemestres['8'] = '8';
                    }
                }
                break;

            case 1:
                if ($semestre % 2 == 1) {
                    for ($i = $semestre; $i > 0; $i--) {
                        if ($i % 2 == 1) {
                            $ListaSemestres[$i] = $i;

                            if (count($ListaSemestres) == 2) {
                                break;
                            }
                        }
                    }
                } else {
                    if (($semestre - 1) % 2 == 1 && $semestre - 1 > 0) {
                        $ListaSemestres = array(
                            $semestre - 1 => $semestre - 1
                        );

                        if ($nombreEntidad == 'TS' && $semestre - 1 == 3) {
                            $ListaSemestres['4'] = '4';
                        }

                        if ($nombreEntidad == 'TS' && $semestre - 1 == 5) {
                            $ListaSemestres['6'] = '6';
                        }

                        if ($nombreEntidad == 'TS' && $semestre - 1 == 7) {
                            $ListaSemestres['8'] = '8';
                        }

                        if ($id_escuela == 180 && $semestre - 1 == 7) {
                            $ListaSemestres['8'] = '8';
                        }

                        if ($id_escuela == 193 && $semestre - 1 == 7) {
                            $ListaSemestres['8'] = '8';
                        }
                    }
                }
                break;
        }

        return $ListaSemestres;
    }

    /**
     * Genera opciones de semestre del mismo tipo par o impar,
     * sin limitar el resultado a dos opciones.
     */
    public function CalcSemSinLimit($semestre = null, $capturaActual = null)
    {
        $ListaSemestres = array();
        $ParImpar = $capturaActual % 2;

        switch ($ParImpar) {
            case 0:
                if ($semestre % 2 == 0) {
                    for ($i = $semestre; $i > 0; $i--) {
                        if ($i % 2 == 0) {
                            $ListaSemestres[$i] = $i;
                        }
                    }
                } else {
                    if (($semestre - 1) % 2 == 0 && $semestre - 1 > 0) {
                        $ListaSemestres = array(
                            $semestre - 1 => $semestre - 1
                        );
                    }
                }
                break;

            case 1:
                if ($semestre % 2 == 1) {
                    for ($i = $semestre; $i > 0; $i--) {
                        if ($i % 2 == 1) {
                            $ListaSemestres[$i] = $i;
                        }
                    }
                } else {
                    if (($semestre - 1) % 2 == 1 && $semestre - 1 > 0) {
                        $ListaSemestres = array(
                            $semestre - 1 => $semestre - 1
                        );
                    }
                }
                break;
        }

        return $ListaSemestres;
    }

    /**
     * Comprueba los criterios establecidos para becados.
     */
    public function CriteriosBecados(
        $sostenimiento = null,
        $semestre = null,
        $modalidad = null,
        $licenciatura = null
    ) {
        if ($sostenimiento != 'p') {
            return false;
        }

        if ($modalidad != 'e') {
            return false;
        }

        if ($semestre != 7 && $semestre != 8) {
            return false;
        }

        $licenciaturasPermitidas = array(
            2, 4, 6, 7, 8, 13, 16, 17, 18, 19, 20
        );

        if (!in_array($licenciatura, $licenciaturasPermitidas)) {
            return false;
        }

        return true;
    }

    /**
     * Comprueba la condición especial de licenciatura,
     * semestre y especialidad.
     */
    public function isEspecial(
        $id_licenciatura = null,
        $semestre = null,
        $id_especialidad = null
    ) {
        if ($id_licenciatura != 7) {
            return false;
        }

        if ($semestre > 4) {
            return false;
        }

        if ($id_especialidad > 0) {
            return false;
        }

        return true;
    }

    /**
     * Devuelve los permisos de edición según el nivel del usuario.
     */
    public function permisosUsuarios($nivel = null)
    {
        switch ($nivel) {
            case 2: // Enlace
            case 3: // Supervisor
                $permisos = array(
                    'id_licenciatura' => 0,
                    'id_especialidad' => 1,
                    'modalidad' => 1,
                    'turno' => 1,
                    'semestre' => 1,
                    'grupo' => 1,
                    'matricula' => 1,
                    'apellido_paterno' => 1,
                    'apellido_materno' => 1,
                    'nombre' => 1,
                    'curp' => 1,
                    'genero' => 1,
                    'edad' => 1,
                    'entidad_nacimiento' => 1,
                    'nacionalidad' => 1,
                    'id_documento' => 1,
                    'id_entidad' => 1,
                    'id_municipio' => 1,
                    'id_escuela_practica' => 1,
                    'situacion' => 1,
                    'fecha_cobro' => 1
                );
                break;

            case 4: // Capturista
                $permisos = array(
                    'id_licenciatura' => 0,
                    'id_especialidad' => 0,
                    'modalidad' => 0,
                    'turno' => 1,
                    'semestre' => 1,
                    'grupo' => 1,
                    'matricula' => 0,
                    'apellido_paterno' => 0,
                    'apellido_materno' => 0,
                    'nombre' => 0,
                    'curp' => 0,
                    'genero' => 0,
                    'edad' => 0,
                    'entidad_nacimiento' => 0,
                    'nacionalidad' => 0,
                    'id_documento' => 0,
                    'id_entidad' => 1,
                    'id_municipio' => 1,
                    'id_escuela_practica' => 1,
                    'situacion' => 1,
                    'fecha_cobro' => 1
                );
                break;

            default:
                $permisos = array();
                break;
        }

        return $permisos;
    }

    /**
     * Compara las opciones seleccionadas con las almacenadas.
     */
    public function diffOpciones(
        $opcSeleccionadas = null,
        $opcAlmacenadas = null
    ) {
        if (
            $opcSeleccionadas['Registro']['id_licenciatura'] !=
            $opcAlmacenadas['Historia']['id_licenciatura']
        ) {
            return false;
        }

        if (
            $opcSeleccionadas['Registro']['id_especialidad'] !=
            $opcAlmacenadas['Historia']['id_especialidad']
        ) {
            return false;
        }

        if (
            $opcSeleccionadas['Registro']['modalidad'] !=
            $opcAlmacenadas['Historia']['modalidad']
        ) {
            return false;
        }

        if (
            $opcSeleccionadas['Registro']['semestre'] !=
            $opcAlmacenadas['Historia']['semestre']
        ) {
            return false;
        }

        return true;
    }
}
