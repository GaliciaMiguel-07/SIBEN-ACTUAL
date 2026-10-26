<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Registro extends Model
{
    protected $table = 'registros';

    protected $primaryKey = 'id';

    public $timestamps = false;

    /*
     * Relaciones
     */

    public function alumno()
    {
        return $this->belongsTo(
            Alumno::class,
            'nid',
            'numero_identificador'
        );
    }

    public function alumnoBachillerato()
    {
        return $this->belongsTo(
            AlumnoBachillerato::class,
            'nid',
            'nid'
        );
    }

    public function becasPractica()
    {
        return $this->hasOne(
            BecasPractica::class,
            'id_registro',
            'id'
        );
    }

    /*
     * Verifica si el alumno ya tiene un registro
     * en la misma licenciatura, especialidad y captura.
     */
    public function alumnoLicenciatura(
        $nid = null,
        $id_registro = null,
        $curp = null,
        $id_licenciatura = null,
        $id_especialidad = null,
        $captura = null
    ) {
        $consulta = DB::table('registros')
            ->join(
                'alumnos',
                'registros.nid',
                '=',
                'alumnos.numero_identificador'
            )
            ->where('registros.id_licenciatura', $id_licenciatura)
            ->where('registros.id_especialidad', $id_especialidad)
            ->where('registros.captura', $captura);

        if ($nid) {
            $consulta->where(
                'alumnos.numero_identificador',
                $nid
            );
        }

        if ($id_registro) {
            $consulta->where('registros.id', '<>', $id_registro);
        }

        if ($curp) {
            $curpCortada = substr((string) $curp, 0, -2);

            $consulta->where(
                'alumnos.curp',
                'like',
                $curpCortada . '%'
            );
        }

        return $consulta->exists() ? 1 : 0;
    }

    /*
     * Verifica si la matrícula ya está registrada
     * para la misma escuela y captura.
     */
    public function alumnoMatricula(
        $id_escuela = null,
        $id_registro = null,
        $matricula = null,
        $captura = null
    ) {
        $consulta = DB::table('registros')
            ->where('id_escuela', $id_escuela)
            ->where('matricula', $matricula)
            ->where('captura', $captura);

        if ($id_registro) {
            $consulta->where('id', '<>', $id_registro);
        }

        return $consulta->exists() ? 2 : 0;
    }

    /*
     * Verifica si existe otra persona con una CURP
     * coincidente en los caracteres previos a los dos últimos.
     */
    public function alumnoCurp($nid = null, $curp = null)
    {
        if ($curp === null || $curp === '') {
            return 0;
        }

        $curpCortada = substr((string) $curp, 0, -2);

        $consulta = DB::table('alumnos')
            ->where('curp', 'like', $curpCortada . '%');

        if ($nid) {
            $consulta->where(
                'numero_identificador',
                '<>',
                $nid
            );
        }

        return $consulta->exists() ? 4 : 0;
    }

    /*
     * Verifica si el alumno tiene una baja definitiva
     * en la captura indicada.
     */
    public function alumnoDefinitiva(
        $nid = null,
        $curp = null,
        $captura = null
    ) {
        $consulta = DB::table('registros')
            ->join(
                'alumnos',
                'registros.nid',
                '=',
                'alumnos.numero_identificador'
            )
            ->where('registros.status', 'd')
            ->where('registros.captura', $captura);

        if ($curp) {
            $curpCortada = substr((string) $curp, 0, -2);

            $consulta->where(
                'alumnos.curp',
                'like',
                $curpCortada . '%'
            );
        }

        if ($nid) {
            $consulta->where(
                'alumnos.numero_identificador',
                $nid
            );
        }

        return $consulta->exists() ? 3 : 0;
    }

    /*
     * Valida los semestres restringidos para ciertas licenciaturas.
     */
    public function alumnoSemestre(
        $semestre = null,
        $id_registro = null,
        $nid = null,
        $captura = null,
        $id_licenciatura = null
    ) {
        if (
            (string) $semestre === '3'
            && in_array(
                (string) $id_licenciatura,
                ['2', '4', '13'],
                true
            )
        ) {
            return 5;
        }

        return 0;
    }

    /*
     * Verifica cambios de licenciatura entre los pares
     * restringidos por el sistema original.
     */
    public function alumnoCambio(
        $semestre = null,
        $id_registro = null,
        $nid = null,
        $captura = null,
        $id_licenciatura = null
    ) {
        $licenciaturaAnterior = DB::table('registros')
            ->where('nid', $nid)
            ->where('captura', $captura)
            ->where('semestre', $semestre)
            ->when(
                $id_registro,
                fn ($consulta) => $consulta->where(
                    'id',
                    '<>',
                    $id_registro
                )
            )
            ->value('id_licenciatura');

        if ($licenciaturaAnterior === null) {
            return 0;
        }

        $paresRestringidos = [
            ['2', '16'],
            ['4', '17'],
            ['13', '18'],
        ];

        foreach ($paresRestringidos as [$origen, $destino]) {
            if (
                (
                    (string) $licenciaturaAnterior === $origen
                    && (string) $id_licenciatura === $destino
                )
                || (
                    (string) $licenciaturaAnterior === $destino
                    && (string) $id_licenciatura === $origen
                )
            ) {
                return 6;
            }
        }

        return 0;
    }

    /*
     * Valida la longitud mínima de la matrícula.
     */
    public function Matricula2($matricula = null)
    {
        return strlen((string) $matricula) < 12 ? 7 : 0;
    }

    /*
     * Ejecuta las validaciones de inscripción.
     */
    public function inscripcionAlumno($opciones = null)
    {
        $opciones = $opciones ?? [];

        $errores = [
            1 => 'Error 1: El alumno ya esta estudiando esa licenciatura.',
            2 => 'Error 2: La matricula ya existe.',
            3 => 'Error 3: El alumno esta en una baja definitiva.',
            4 => 'Error 4: La curp ya existe.',
            5 => 'Error 5: No se puede inscribir en un plan anterior a 2012.',
            6 => 'Error 6: No se puede realizar el cambio de licenciatura.',
            7 => 'Error 7: La Matricula debe de ser de 12 digitos.',
        ];

        $errorLicenciatura = 0;
        $errorMatricula = 0;
        $errorMatricula2 = 0;
        $errorCurp = 0;
        $errorSemestre = 0;
        $errorCambio = 0;
        $errorDefinitiva = 0;

        if (isset($opciones['id_licenciatura'])) {
            $errorLicenciatura = $this->alumnoLicenciatura(
                $opciones['nid'] ?? null,
                $opciones['id_registro'] ?? null,
                null,
                $opciones['id_licenciatura'],
                $opciones['id_especialidad'] ?? null,
                $opciones['captura'] ?? null
            );
        }

        if (isset($opciones['matricula'])) {
            $semestre = $opciones['semestre'] ?? null;

            if (
                $semestre > 2
                || ($semestre !== null
                    && $semestre <= 2
                    && !empty($opciones['matricula']))
            ) {
                $errorMatricula = $this->alumnoMatricula(
                    $opciones['id_escuela'] ?? null,
                    $opciones['id_registro'] ?? null,
                    $opciones['matricula'],
                    $opciones['captura'] ?? null
                );

                $errorMatricula2 = $this->Matricula2(
                    $opciones['matricula']
                );
            }
        }

        if (isset($opciones['curp'])) {
            $errorCurp = $this->alumnoCurp(
                $opciones['nid'] ?? null,
                $opciones['curp']
            );
        }

        if (isset($opciones['semestre'])) {
            $errorSemestre = $this->alumnoSemestre(
                $opciones['semestre'],
                $opciones['id_registro'] ?? null,
                $opciones['nid'] ?? null,
                $opciones['captura'] ?? null,
                $opciones['id_licenciatura'] ?? null
            );
        }

        if (isset($opciones['id_licenciatura'])) {
            $errorCambio = $this->alumnoCambio(
                $opciones['semestre'] ?? null,
                $opciones['id_registro'] ?? null,
                $opciones['nid'] ?? null,
                $opciones['captura'] ?? null,
                $opciones['id_licenciatura']
            );
        }

        $errorDefinitiva = $this->alumnoDefinitiva(
            $opciones['nid'] ?? null,
            null,
            $opciones['captura'] ?? null
        );

        $errorInscripcion = [];

        foreach ([
            $errorLicenciatura,
            $errorMatricula,
            $errorMatricula2,
            $errorCurp,
            $errorSemestre,
            $errorCambio,
            $errorDefinitiva,
        ] as $codigo) {
            if ($codigo !== 0 && isset($errores[$codigo])) {
                $errorInscripcion[$codigo] = $errores[$codigo];
            }
        }

        return $errorInscripcion;
    }

    /*
     * Obtiene los alumnos que coinciden con el prefijo de CURP.
     */
    public function obtenerNid($curp = null)
    {
        if ($curp === null || $curp === '') {
            return collect();
        }

        $subCurp = substr((string) $curp, 0, -2);

        return DB::table('alumnos')
            ->select('numero_identificador', 'curp')
            ->where('curp', 'like', $subCurp . '%')
            ->groupBy('numero_identificador', 'curp')
            ->get();
    }

    /*
     * Valida los datos al dar de alta un alumno.
     */
    public function altaAlumno($opciones = null)
    {
        $opciones = $opciones ?? [];

        $errores = [
            1 => 'Error 1: El alumno ya esta estudiando esa licenciatura.',
            2 => 'Error 2: La matricula ya existe.',
            3 => 'Error 3: El alumno esta en una baja definitiva.',
            4 => 'Error 4: La curp ya existe.',
        ];

        $nid = collect();
        $validaCurp = 0;
        $validaLic = 0;
        $validaMatricula = 0;

        if (isset($opciones['curp'])) {
            $validaCurp = $this->alumnoCurp(
                null,
                $opciones['curp']
            );

            if ($validaCurp === 4) {
                $validaLic = $this->alumnoLicenciatura(
                    null,
                    null,
                    $opciones['curp'],
                    $opciones['id_licenciatura'] ?? null,
                    $opciones['id_especialidad'] ?? null,
                    $opciones['captura'] ?? null
                );

                if ($validaLic === 0) {
                    $nid = $this->obtenerNid($opciones['curp']);
                }
            }
        }

        if (isset($opciones['matricula'])) {
            $semestre = $opciones['semestre'] ?? null;

            if (
                $semestre > 2
                || ($semestre !== null
                    && $semestre <= 2
                    && !empty($opciones['matricula']))
            ) {
                $validaMatricula = $this->alumnoMatricula(
                    $opciones['id_escuela'] ?? null,
                    null,
                    $opciones['matricula'],
                    $opciones['captura'] ?? null
                );
            }
        }

        $errorAlta = [];

        if ($validaCurp === 4 && $validaLic === 1) {
            $errorAlta[4] = $errores[4];
            $errorAlta[1] = $errores[1];
        }

        if ($validaMatricula === 2) {
            $errorAlta[2] = $errores[2];
        }

        // La validación de baja definitiva estaba comentada
        // en el modelo original; no se activa aquí sin confirmar
        // el comportamiento esperado.

        return [$errorAlta, $nid];
    }

    /*
     * Convierte una cadena separada por ":" en un arreglo.
     * Sustituye split(), eliminado desde PHP 7.
     */
    public function separaApoyos($cadenaApoyos)
    {
        $listaApoyos = explode(':', (string) $cadenaApoyos);
        $apoyos = [];

        foreach ($listaApoyos as $apoyo) {
            $check = 'apoyos' . $apoyo;
            $apoyos[$check] = $apoyo;
        }

        return $apoyos;
    }

    /*
     * Une los apoyos seleccionados en una cadena separada por ":".
     */
    public function uneApoyos(&$listaApoyos)
    {
        $apoyos = [];

        for ($i = 1; $i <= 9; $i++) {
            $clave = 'apoyos' . $i;

            if (!empty($listaApoyos[$clave])) {
                $apoyos[] = $listaApoyos[$clave];
            }
        }

        return implode(':', $apoyos);
    }
}

