<?php
namespace App\Http\Controllers;

use App\Models\Posgrado;
use App\Models\Autorizaciones;
use App\Models\AutorizacionesPosgrado;
use App\Models\GeneracionesPosgrado;
use App\Models\Entidad;
use App\Models\Escuela;
use App\Models\EscuelaStatus;
use App\Models\Municipio;
use App\Models\Localidad;
use App\Models\DomicilioEscuela;
use App\Models\Usuario;
use App\Models\CaracteristicasEscuela;
use Illuminate\Http\Request;

class PosgradosController extends sibenController {
    public function index(){
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel = $ctx['nivel'] ?? null;
        $captura = $ctx['captura'] ?? null;

        $mensaje = null;
        if(!$captura){
            $mensaje = 'La captura está finalizada';
        }
        $opcionesMenu = [];
        $opcionesMenu[] = ['posgrados', 'Agregar Posgrado', 'AgregarPosgrado', 0];
        $opcionesMenu[] = ['posgrados', 'Editar Posgrado', 'EscuelaPosgrado', 'p'];
        $opcionesMenu[] = ['posgrados', 'Agregar Generaciones', 'EscuelaPosgrado', 'g'];
        $opcionesMenu[] = ['posgrados', 'Agregar Autorizaciones', 'EscuelaPosgrado', 'a'];
        $opcionesMenu[] = ['posgrados', 'Escuela Información', 'EscuelaDetalle', $id_escuela];

        return view('posgrado.index', compact('id_escuela', 'opcionesMenu', 'mensaje'));
    }

    public function AgregarPosgrado(Request $request){
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura = $ctx['captura'] ?? null;

        if($captura != 1){
            return redirect()->route('siben.index');
        }
        if($request->isMethod('post') && $request->filled('posgrado')){
            $posgradoData = $request->input('posgrado');

            if(isset($posgradoData['anio'])){
                $posgradoData['fecha_inicio_operacion'] = $posgradoData['anio'];
            }
            $posgradoData['id_escuela'] = $id_escuela;

            $nuevoPosgrado = Posgrado::create($posgradoData);

            return redirect('/Posgrados/posgradoInfo' . $nuevoPosgrado->id);
        }
        for($i = date('Y'); $i >= 1975; $i--){
            $year[$i] = $i;
        }
    }
}

?>