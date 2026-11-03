<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroEgresados extends Model
{
    protected $table = 'registro_egresados';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'fecha_actualizacion' => 'date',
        'fecha_alta' => 'datetime',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'nid');
    }

    // TODO: revisar mÃ©todo 'egresadoLicenciatura($nid = null, $id_registro = null, $id_licenciatura = null, $id_especialidad = null)': dependÃ­a del ORM de CakePHP ($this->find).

    public function egresadoSemMod($semestre = null, $modalidad = null)
    {
    
        switch ($modalidad) {
          case 'e':
            if ($semestre != 9) {
              return 2;
            }
            break;
          case 'm':
            if ($semestre != 13) {
              return 2;
            }
            break;
        }
        return 0;
    
    }

    public function verificarEgresado($opc = null)
    {
    
        $errores = array(
          '1' => 'Error 1: El alumno esta ya es egresado de esa licenciatura.',
          '2' => 'Error 2: La matricula ya existe.'
        );
    
        if (isset ($opc['id_licenciatura'])) {
          $errorLicenciatura = $this->egresadoLicenciatura($opc['nid'], $opc['id_registro'], $opc['id_licenciatura'], $opc['id_especialidad']);
        }
        if (isset ($opc['modalidad'])) {
          $errorModSem = $this->egresadoSemMod($opc['semestre'], $opc['modalidad']);
        }
    
        $errorEgresado = array();
        if ($ErrorLicenciatura != 0) {
          $errorEgresado[$errorLicenciatura] = $errores[$errorLicenciatura];
        }
        if ($ErrorMatricula != 0) {
          $errorEgresado[$errorModSem]    = $errores[$errorModSem];
        }
    
        return $errorEgresado;
    
    }

}
