<?php

namespace App\Http\Controllers;

use App\Models\Egresado;
use App\Models\RegistroEgresados;
use App\Models\Registro;
use App\Models\Alumno;
use Illuminate\Http\Request;

class EgresadosController extends sibenController
{
    /**
     * beforeFilter / Filtro de seguridad y control de acceso basado en contexto
     */
    public function beforeFilter(Request $request)
    {
        parent::beforeFilter();

        $ctx = $this->contexto();
        $captura = $ctx['captura'] ?? null;
        $currentAction = $request->route()->getActionMethod();

        $accionesPermitidas = [
            'index',
            'busquedax',
            'listasEgresados',
            'editarEgresado',
            'agregarEgresado',
            'noEgreso',
            'agregarnoEgresado'
        ];

        if (!$captura && !in_array($currentAction, $accionesPermitidas)) {
            return redirect()->route('alumnos.index');
        }
    }

   /**
     * index
     * Vista principal del módulo de egresados
     */
    public function index()
    {
        // Si necesitas el request, puedes usar el helper global request()
        // $request = request(); 

        return view('egresados.index');
    }

    /**
     * busquedax
     * Se muestra el formulario para buscar en la base de egresados por ciclo escolar
     */
    public function busquedax(Request $request)
    {
        $ciclo_escolar = [
            'd' => 'Desconocida', 
            '25' => '2024-2025',
            '24' => '2023-2024',
            '23' => '2022-2023',
            '22' => '2021-2022',
            '21' => '2020-2021',
            '20' => '2019-2020',
            '19' => '2018-2019',
            '18' => '2017-2018',
            '17' => '2016-2017',
            '16' => '2015-2016',
            '15' => '2014-2015',
            '14' => '2013-2014',
            '13' => '2012-2013',
            '12' => '2011-2012',
            '11' => '2010-2011',
            '10' => '2009-2010',
            '9' => '2008-2009', 
            '8' => '2007-2008',
            '7' => '2006-2007', 
            '6' => '2005-2006', 
            '5' => '2004-2005',
            '4' => '2003-2004'
        ];

        $cicloEscolar = $ciclo_escolar;
        $anterior = route('alumnos.index');

        return view('egresados.busquedax', compact('cicloEscolar', 'anterior'));
    }

    /**
     * listasEgresados
     * Se muestra la lista de egresados de acuerdo al ciclo escolar de egreso (AJAX)
     */
    public function listasEgresados(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $nivel = $ctx['nivel'] ?? null;

        if (!$id_escuela) {
            return redirect()->route('alumnos.index');
        }

        $ciclo_escolar = $request->input('busca.ciclo_escolar');

        $egresados = collect();
        $url = '';
        $url2 = '';
        $nombreBoton = '';
        $nombreBoton2 = '';

        if ($ciclo_escolar && $ciclo_escolar != 'd') {
            $egresados = Egresado::select([
                    'alumnos.numero_identificador',
                    'alumnos.apellido_paterno',
                    'alumnos.apellido_materno',
                    'alumnos.nombre',
                    'alumnos.curp',
                    'registro_egresados.status',
                    'registro_egresados.id'
                ])
                ->join('registro_egresados', 'alumnos.numero_identificador', '=', 'registro_egresados.nid')
                ->where('registro_egresados.id_escuela', $id_escuela)
                ->where('registro_egresados.nid', '<>', '')
                ->where('egresados.ciclo_escolar', $ciclo_escolar)
                ->where('registro_egresados.status', 'e')
                ->orderBy('alumnos.apellido_paterno')
                ->orderBy('alumnos.apellido_materno')
                ->orderBy('alumnos.nombre')
                ->get();

            $url = route('egresados.editarEgresado');
            $nombreBoton = "Editar";
        } else {
            $egresados = RegistroEgresados::select([
                    'alumnos.numero_identificador',
                    'alumnos.apellido_paterno',
                    'alumnos.apellido_materno',
                    'alumnos.nombre',
                    'alumnos.curp',
                    'registro_egresados.status',
                    'registro_egresados.id'
                ])
                ->join('alumnos', 'alumnos.numero_identificador', '=', 'registro_egresados.nid')
                ->where('registro_egresados.id_escuela', $id_escuela)
                ->where('registro_egresados.nid', '<>', '')
                ->where('registro_egresados.status', 'x')
                ->orderBy('alumnos.apellido_paterno')
                ->orderBy('alumnos.apellido_materno')
                ->orderBy('alumnos.nombre')
                ->get();

            $url = route('egresados.agregarEgresado');
            $nombreBoton = "Egresar";
            $url2 = route('egresados.agregarnoEgresado');
            $nombreBoton2 = "No Egreso";
        }

        $renglones = [];
        foreach ($egresados as $egresado) {
            $idRegistro = $egresado->id;
            $nid = $egresado->numero_identificador;

            $parametros = "data[Historia][id_registro]={$idRegistro}&data[Historia][nid]={$nid}";
            $evento = "consultaAjax('$url', '$parametros', muestraOverlay, muestraOcupado);";
            $boton = '<input id="b_' . $idRegistro . '" type="button" value="' . $nombreBoton . '" onclick="' . $evento . '" class="btn btn-primary" />';

            $boton2 = '';
            if ($url2) {
                $evento2 = "consultaAjax('$url2', '$parametros', muestraOverlay, muestraOcupado);";
                $boton2 = '<input id="b2_' . $idRegistro . '" type="button" value="' . $nombreBoton2 . '" onclick="' . $evento2 . '" class="btn btn-primary" />';
            }

            $estilo = '';
            $estado = '';
            switch ($egresado->status) {
                case 'x':
                    $estilo = 'style="text-align:center; background-color:#CC0000; color:#FFCCCC; font-weight:bold;"';
                    $estado = 'No Insc.';
                    break;
                case 'e':
                    $estilo = 'style="text-align:center; background-color:#FFD700; color:#993300; font-weight:bold;"';
                    $estado = 'Egresado';
                    break;
            }

            $statusHtml = "<div {$estilo}>{$estado}</div>";

            $alumnoHistoria = [
                'numero_identificador' => $egresado->numero_identificador,
                'apellido_paterno' => $egresado->apellido_paterno,
                'apellido_materno' => $egresado->apellido_materno,
                'nombre' => $egresado->nombre,
                'curp' => $egresado->curp,
                'status' => $statusHtml,
                'accion' => $boton
            ];

            if ($nivel == 3 && $boton2) {
                $alumnoHistoria['accion2'] = $boton2;
            }

            $renglones[$idRegistro] = $alumnoHistoria;
        }

        $cabeceras = ['No', 'nid', 'Primer Apellido', 'Segundo Apellido', 'Nombre', 'Curp', 'Estado', 'Accion'];

        return view('elements.tablaAjax', compact('renglones', 'cabeceras'));
    }

    /**
     * editarEgresado
     * Este método es para los que ya son egresados (status e)
     */
    public function editarEgresado(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura_actual = $ctx['captura_actual'] ?? session('captura_actual', 1);

        if (!$id_escuela) {
            return redirect()->route('alumnos.index');
        }

        $historia = $request->input('Historia', []);
        $registroEgresadosData = $request->input('RegistroEgresados', []);
        $alumnoData = $request->input('Alumno', []);
        $egresadoData = $request->input('Egresado', []);

        if (!empty($historia['id_registro'])) {
            $id_registro = $historia['id_registro'];
            $nid = $historia['nid'] ?? null;
        } else {
            $id_registro = $registroEgresadosData['id'] ?? null;
            $nid = $alumnoData['numero_identificador'] ?? null;
        }

        $infoEgresados = Egresado::where('id_escuela', $id_escuela)
            ->where('nid', $nid)
            ->first();

        if (!empty($egresadoData)) {
            if (($egresadoData['titulado'] ?? 'n') == 'n') {
                $egresadoData['fecha_titulacion'] = '0000-00-00';
                $egresadoData['tema_titulacion'] = '';
            }

            $vinculacionArr = [];
            for ($i = 1; $i <= 6; $i++) {
                if (isset($egresadoData['op' . $i]) && $egresadoData['op' . $i] > 0) {
                    $vinculacionArr[] = $egresadoData['op' . $i];
                }
                unset($egresadoData['op' . $i]);
            }
            $egresadoData['vinculacion'] = implode(':', $vinculacionArr);

            $registroId = $registroEgresadosData['id'] ?? $id_registro;
            if ($registroId) {
                $numeroIdentificador = $alumnoData['numero_identificador'] ?? $nid;
                $noRepetir = Egresado::where('nid', $numeroIdentificador)
                    ->where('id_escuela', $id_escuela)
                    ->first();

                $payload = [
                    'ciclo_escolar' => $egresadoData['ciclo_escolar'] ?? null,
                    'fecha_titulacion' => $egresadoData['fecha_titulacion'] ?? null,
                    'tema_titulacion' => $egresadoData['tema_titulacion'] ?? null,
                    'vinculacion' => $egresadoData['vinculacion'] ?? null,
                    'nid' => $numeroIdentificador,
                    'captura' => $captura_actual,
                    'id_escuela' => $id_escuela,
                ];

                if ($noRepetir) {
                    $noRepetir->update($payload);
                } else {
                    Egresado::create($payload);
                }
            }
        } else {
            $infoArray = $infoEgresados ? $infoEgresados->toArray() : [];
            if (!empty($infoArray['tema_titulacion'])) {
                $infoArray['titulado'] = 's';
            } else {
                $infoArray['titulado'] = 'n';
            }

            if (!empty($infoArray['vinculacion'])) {
                $opc = explode(':', $infoArray['vinculacion']);
                foreach ($opc as $val) {
                    if ($val > 0) {
                        $infoArray['op' . $val] = $val;
                    }
                }
            }
            $infoEgresados = $infoArray;
        }

        $opCiclo_escolar = [
            '25' => '2024-2025',
            '24' => '2023-2024',
            '23' => '2022-2023',
            '22' => '2021-2022',
            '21' => '2020-2021',
            '20' => '2019-2020',
            '19' => '2018-2019',
            '18' => '2017-2018',
            '17' => '2016-2017',
            '16' => '2015-2016',
            '15' => '2014-2015',
            '14' => '2013-2014',
            '13' => '2012-2013',
            '12' => '2011-2012',
            '11' => '2010-2011',
            '10' => '2009-2010',
            '9' => '2008-2009', 
            '8' => '2007-2008',
            '7' => '2006-2007' 
        ];

        return view('egresados.editar_egresado', compact('opCiclo_escolar', 'infoEgresados', 'id_registro'));
    }

    /**
     * agregarEgresado
     * Método para agregar la información de un alumno que es recién egresado
     */
    public function agregarEgresado(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura_actual = $ctx['captura_actual'] ?? session('captura_actual', 1);

        if (!$id_escuela) {
            return redirect()->route('alumnos.index');
        }

        $historia = $request->input('Historia', []);
        $registroEgresadosData = $request->input('RegistroEgresados', []);
        $alumnoData = $request->input('Alumno', []);
        $egresadoData = $request->input('Egresado', []);

        if (!empty($historia['id_registro'])) {
            $id_registro = $historia['id_registro'];
            $nid = $historia['nid'] ?? null;
        } else {
            $id_registro = $registroEgresadosData['id'] ?? null;
            $nid = $alumnoData['numero_identificador'] ?? null;
        }

        $infoEgresados = RegistroEgresados::select([
                'alumnos.numero_identificador', 'alumnos.apellido_paterno', 'alumnos.apellido_materno', 'alumnos.nombre',
                'alumnos.curp', 'registro_egresados.id', 'registro_egresados.id_licenciatura',
                'registro_egresados.id_especialidad', 'registro_egresados.modalidad', 'registro_egresados.semestre'
            ])
            ->join('alumnos', 'alumnos.numero_identificador', '=', 'registro_egresados.nid')
            ->where('alumnos.numero_identificador', $nid)
            ->where('registro_egresados.id', $id_registro)
            ->first();

        if (!empty($egresadoData)) {
            if (($egresadoData['titulado'] ?? 'n') == 'n') {
                unset($egresadoData['fecha_titulacion'], $egresadoData['tema_titulacion']);
            }

            $parametros = [
                'id_registro' => $registroEgresadosData['id'] ?? $id_registro,
                'nid' => $alumnoData['numero_identificador'] ?? $nid,
                'semestre' => $infoEgresados->semestre ?? null,
                'modalidad' => $infoEgresados->modalidad ?? null,
                'id_licenciatura' => $infoEgresados->id_licenciatura ?? null,
                'id_especialidad' => $infoEgresados->id_especialidad ?? null
            ];

            $errorEgresados = RegistroEgresados::verificarEgresado($parametros);

            $vinculacionArr = [];
            for ($i = 1; $i <= 6; $i++) {
                if (isset($egresadoData['op' . $i]) && $egresadoData['op' . $i] > 0) {
                    $vinculacionArr[] = $egresadoData['op' . $i];
                }
                unset($egresadoData['op' . $i]);
            }
            $egresadoData['vinculacion'] = implode(':', $vinculacionArr);

            if (empty($errorEgresados)) {
                $regId = $registroEgresadosData['id'] ?? null;
                if ($regId) {
                    $regEgr = RegistroEgresados::find($regId);
                    if ($regEgr) {
                        $regEgr->update(['status' => 'e']);
                    }

                    $numeroIdentificador = $alumnoData['numero_identificador'] ?? $nid;
                    $noRepetir = Egresado::where('nid', $numeroIdentificador)
                        ->where('id_escuela', $id_escuela)
                        ->first();

                    $payload = array_merge($egresadoData, [
                        'nid' => $numeroIdentificador,
                        'captura' => $captura_actual,
                        'id_escuela' => $id_escuela,
                    ]);

                    if ($noRepetir) {
                        $noRepetir->update($payload);
                    } else {
                        Egresado::create($payload);
                    }
                }
            } else {
                $mensajes = "";
                for ($i = 1; $i <= 2; $i++) {
                    if (!empty($errorEgresados[$i])) {
                        $mensajes .= $errorEgresados[$i] . "\n";
                    }
                }
                return response($mensajes, 400);
            }
        } else {
            $request->merge(['RegistroEgresados' => $infoEgresados ? $infoEgresados->toArray() : []]);
        }

        $opCiclo_escolar = [
            '25' => '2024-2025',
            '24' => '2023-2024',
            '23' => '2022-2023',
            '22' => '2021-2022',
            '21' => '2020-2021',
            '20' => '2019-2020',
            '19' => '2018-2019',
            '18' => '2017-2018',
            '17' => '2016-2017',
            '16' => '2015-2016',
            '15' => '2014-2015',
            '14' => '2013-2014',
            '13' => '2012-2013',
            '12' => '2011-2012',
            '11' => '2010-2011',
            '10' => '2009-2010',
            '9' => '2008-2009', 
            '8' => '2007-2008',
            '7' => '2006-2007' 
        ];

        return view('egresados.agregar_egresado', compact('opCiclo_escolar', 'infoEgresados', 'id_registro'));
    }

    /**
     * noEgreso
     * Mueve el registro de egresados de vuelta a registros normales
     */
    public function noEgreso(Request $request)
    {
        $historia = $request->input('Historia', []);
        $registroEgresadosData = $request->input('RegistroEgresados', []);
        $alumnoData = $request->input('Alumno', []);

        if (!empty($historia['id_registro'])) {
            $id_registro = $historia['id_registro'];
        } else {
            $id_registro = $registroEgresadosData['id'] ?? ($alumnoData['id'] ?? null);
        }

        $infoEgresados = RegistroEgresados::find($id_registro);

        if ($infoEgresados) {
            $registroPayload = [
                'nid' => $infoEgresados->nid,
                'id_alumno' => $infoEgresados->id_alumno,
                'id_escuela' => $infoEgresados->id_escuela,
                'id_licenciatura' => $infoEgresados->id_licenciatura,
                'id_especialidad' => $infoEgresados->id_especialidad,
                'modalidad' => $infoEgresados->modalidad,
                'turno' => $infoEgresados->turno,
                'semestre' => '8',
                'grupo' => $infoEgresados->grupo,
                'matricula' => $infoEgresados->matricula,
                'sepa_ingles' => $infoEgresados->sepa_ingles,
                'apoyos' => $infoEgresados->apoyos,
                'status' => $infoEgresados->status,
                'fecha_alta' => $infoEgresados->fecha_alta,
                'fecha_actualizacion' => now(),
                'captura' => $infoEgresados->captura,
            ];

            Registro::create($registroPayload);
            $infoEgresados->delete();
        }

        $opCiclo_escolar = [];
        return view('egresados.agregar_egresado', compact('opCiclo_escolar', 'infoEgresados', 'id_registro'));
    }

    /**
     * agregarnoEgresado
     * Método alternativo para procesar la no titulación o estatus de no egreso
     */
    public function agregarnoEgresado(Request $request)
    {
        $ctx = $this->contexto();
        $id_escuela = $ctx['id_escuela'] ?? null;
        $captura_actual = $ctx['captura_actual'] ?? session('captura_actual', 1);

        if (!$id_escuela) {
            return redirect()->route('alumnos.index');
        }

        $historia = $request->input('Historia', []);
        $registroEgresadosData = $request->input('RegistroEgresados', []);
        $alumnoData = $request->input('Alumno', []);
        $egresadoData = $request->input('Egresado', []);

        if (!empty($historia['id_registro'])) {
            $id_registro = $historia['id_registro'];
            $nid = $historia['nid'] ?? null;
        } else {
            $id_registro = $registroEgresadosData['id'] ?? null;
            $nid = $alumnoData['numero_identificador'] ?? null;
        }

        $infoEgresados = RegistroEgresados::select([
                'alumnos.numero_identificador', 'alumnos.apellido_paterno', 'alumnos.apellido_materno', 'alumnos.nombre',
                'alumnos.curp', 'registro_egresados.id', 'registro_egresados.id_licenciatura',
                'registro_egresados.id_especialidad', 'registro_egresados.modalidad', 'registro_egresados.semestre'
            ])
            ->join('alumnos', 'alumnos.numero_identificador', '=', 'registro_egresados.nid')
            ->where('alumnos.numero_identificador', $nid)
            ->where('registro_egresados.id', $id_registro)
            ->first();

        if (!empty($egresadoData)) {
            if (($egresadoData['titulado'] ?? 'n') == 'n') {
                unset($egresadoData['fecha_titulacion'], $egresadoData['tema_titulacion']);
            }

            $parametros = [
                'id_registro' => $registroEgresadosData['id'] ?? $id_registro,
                'nid' => $alumnoData['numero_identificador'] ?? $nid,
                'semestre' => $infoEgresados->semestre ?? null,
                'modalidad' => $infoEgresados->modalidad ?? null,
                'id_licenciatura' => $infoEgresados->id_licenciatura ?? null,
                'id_especialidad' => $infoEgresados->id_especialidad ?? null
            ];

            $errorEgresados = RegistroEgresados::verificarEgresado($parametros);

            $vinculacionArr = [];
            for ($i = 1; $i <= 6; $i++) {
                if (isset($egresadoData['op' . $i]) && $egresadoData['op' . $i] > 0) {
                    $vinculacionArr[] = $egresadoData['op' . $i];
                }
                unset($egresadoData['op' . $i]);
            }
            $egresadoData['vinculacion'] = implode(':', $vinculacionArr);

            if (empty($errorEgresados)) {
                $regId = $registroEgresadosData['id'] ?? null;
                if ($regId) {
                    $regEgr = RegistroEgresados::find($regId);
                    if ($regEgr) {
                        $regEgr->update(['status' => 'e']);
                    }

                    $numeroIdentificador = $alumnoData['numero_identificador'] ?? $nid;
                    $noRepetir = Egresado::where('nid', $numeroIdentificador)
                        ->where('id_escuela', $id_escuela)
                        ->first();

                    $payload = array_merge($egresadoData, [
                        'nid' => $numeroIdentificador,
                        'captura' => $captura_actual,
                        'id_escuela' => $id_escuela,
                    ]);

                    if ($noRepetir) {
                        $noRepetir->update($payload);
                    } else {
                        Egresado::create($payload);
                    }
                }
            } else {
                $mensajes = "";
                for ($i = 1; $i <= 2; $i++) {
                    if (!empty($errorEgresados[$i])) {
                        $mensajes .= $errorEgresados[$i] . "\n";
                    }
                }
                return response($mensajes, 400);
            }
        } else {
            $request->merge(['RegistroEgresados' => $infoEgresados ? $infoEgresados->toArray() : []]);
        }

        $opCiclo_escolar = [
            '25' => '2024-2025',
            '24' => '2023-2024',
            '23' => '2022-2023',
            '22' => '2021-2022',
            '21' => '2020-2021',
            '20' => '2019-2020',
            '19' => '2018-2019',
            '18' => '2017-2018',
            '17' => '2016-2017',
            '16' => '2015-2016',
            '15' => '2014-2015',
            '14' => '2013-2014',
            '13' => '2012-2013',
            '12' => '2011-2012',
            '11' => '2010-2011',
            '10' => '2009-2010',
            '9' => '2008-2009', 
            '8' => '2007-2008',
            '7' => '2006-2007' 
        ];

        return view('egresados.agregar_egresado2', compact('opCiclo_escolar', 'infoEgresados', 'id_registro'));
    }
}