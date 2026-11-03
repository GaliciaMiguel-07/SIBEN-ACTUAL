<?php
namespace App\Htts\Controllers;

use Illuminate\Http\Request;
use App\Models\Entidad;
use App\Models\Funcion;
use App\Models\Escuela;
use App\Models\Escuela2;
use App\Models\Historia;
use App\Models\Personal;
use App\Models\Licenciatura;
use App\Models\Especialidad;
use App\Models\NivelEstudio;
use App\Models\ContratacionDocente;
use App\Models\FormaContratacion;
use App\Models\Categorias;
use App\Models\Asignatura;
use App\Models\AsignaturaDocente;
use App\Models\EscuelaLicenciatura;
use App\Models\NivelSni;
use App\Models\AreaConocimiento;
use App\Models\Docente;

class ConsultasHistoricoController extends sibenController {

/**
     * Helper interno para reemplazar $this->contador() de CakePHP
     */
    private function contador($limite, $incluirCero = false)
    {
        $inicio = $incluirCero ? 0 : 1;
        $rango = range($inicio, $limite);
        return array_combine($rango, $rango);
    }
    public function buscarAlumno(){
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;

        $entidades = Entidad::where('id', '<', 33)->pluck('entidad', 'id');

        return view('consultas_historico.buscar_alumno', compact('nivel', 'entidades'));
    }
    public function buscarDocente(){
        $ctx = $this->contexto();
        $nivel = $ctx['nivel'] ?? null;
        $idEntidad = $ctx['id_entidad'] ?? null;
        $idEscuela = $ctx['id_escuela'] ?? null;

        $status = [
            'a'=>'Activo',
            'x'=>'Inactivo',
            'j'=>'Jubilado',
            'r'=>'Renuncia',
            'b'=>'Baja administrativa',
            'd'=>'Defuncion'
        ];
        $funciones = Funcion::pluck('fumcion', 'id');

        $entidades = [];
        $esculas = [];
    }

}

?>