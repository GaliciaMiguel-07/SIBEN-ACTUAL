<?php

namespace App\Http\Controllers;

use App\Models\AlumnoCeneval;
use App\Models\LogAlumnoCeneval;
use App\Models\Egc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class CenevalController extends Controller
{
    /**
     * index
     * Sólo de relleno
     */
    public function index()
    {
        return view('ceneval.index');
    }

    /**
     * login
     */
    public function login(Request $request)
    {
        $error = '';
        $directorio = base_path('matriculas');

        if ($request->isMethod('post') && !empty($request->all())) {
            $idUsuario = $request->input('AlumnoCeneval.id_usuario') ?? $request->input('id_usuario');
            $contrasenia = $request->input('AlumnoCeneval.contrasenia') ?? $request->input('contrasenia');

            $usuario = AlumnoCeneval::where('id_usuario', $idUsuario)
                ->where('contrasenia', $contrasenia)
                ->first();

            if ($usuario) {
                session([
                    'AlumnoCeneval.id_usuario' => $usuario->id_usuario,
                    'AlumnoCeneval.id' => $usuario->id,
                    'AlumnoCeneval.nombre' => trim("{$usuario->nombre} {$usuario->apellido_paterno} {$usuario->apellido_materno}"),
                ]);

                $matricula = session('AlumnoCeneval.id_usuario');
                $sessionId = session()->getId();
                $resultadosPath = public_path("files/resultados/{$sessionId}");

                if (!File::exists($resultadosPath)) {
                    File::makeDirectory($resultadosPath, 0755, true);
                }

                $sourcePdf = "{$directorio}/{$matricula}.pdf";
                $destPdf = "{$resultadosPath}/{$matricula}.pdf";
                
                if (File::exists($sourcePdf)) {
                    File::copy($sourcePdf, $destPdf);
                }

                return redirect('/ceneval/resultados');
            } else {
                $error = 'Datos Incorrectos';
            }
        } else {
            $request->session()->regenerate();
        }

        return view('ceneval.login', compact('error'));
    }

    /**
     * logout
     */
    public function logout()
    {
        $sessionId = session()->getId();
        $resultadosPath = public_path("files/resultados/{$sessionId}");

        if (File::exists($resultadosPath)) {
            File::deleteDirectory($resultadosPath);
        }

        session()->forget('AlumnoCeneval');
        session()->flush();

        return redirect('/ceneval/login');
    }

    /**
     * resultados
     */
    public function resultados()
    {
        if (!session()->has('AlumnoCeneval.id_usuario')) {
            return redirect('/ceneval/login');
        }

        $nombre = session('AlumnoCeneval.nombre');
        $matricula = session('AlumnoCeneval.id_usuario');
        $idAlumno = session('AlumnoCeneval.id');

        $alumno = AlumnoCeneval::where('id_usuario', $matricula)->first();

        if ($alumno && $alumno->ingreso == 0) {
            $alumno->ingreso = 1;
            $alumno->save();
        }

        $correos = LogAlumnoCeneval::where('id_alumno_ceneval', $idAlumno)
            ->select('correo')
            ->distinct()
            ->get();

        $id = session()->getId();
        $anterior = '/ceneval/';

        return view('ceneval.resultados', compact('nombre', 'matricula', 'correos', 'id', 'anterior'));
    }

    /**
     * solicitudCorreo
     * Se muestra el formulario con los datos para solicitar la contraseña vía correo electrónico.
     */
    public function solicitudCorreo(Request $request)
    {
        // Catálogo de exámenes
        $catExamenes = Egc::all();
        $examenes = $catExamenes->mapWithKeys(function ($item) {
            return [$item->id => "{$item->clave}: {$item->egc}"];
        })->toArray();

        $mensaje = '';
        $correo3 = '';

        if ($request->isMethod('post') && !empty($request->all())) {
            $data = $request->input('AlumnoCeneval', []);
            $logData = $request->input('LogAlumnoCeneval', []);

            $idUsuario = $data['id_usuario'] ?? null;
            $examenIngresado = $data['examen'] ?? null;
            $correoIngresado = $logData['correo'] ?? null;

            $alumno = AlumnoCeneval::where('id_usuario', $idUsuario)->first();

            if ($alumno) {
                $idExamen = $alumno->id_egc;
                $correo3 = $alumno->correo;
                $registrado = $alumno->registro;

                if ($idExamen == $examenIngresado && $correoIngresado == $correo3 && $registrado == 1) {
                    $correoRegistrado = LogAlumnoCeneval::where('correo', $correoIngresado)->first();

                    if (empty($correoRegistrado)) {
                        $alumno->solicitud = ($alumno->solicitud ?? 0) + 1;
                        $alumno->save();

                        LogAlumnoCeneval::create([
                            'id_alumno_ceneval' => $alumno->id,
                            'correo' => $correoIngresado,
                        ]);

                        $nombreCompleto = trim("{$alumno->nombre} {$alumno->apellido_paterno} {$alumno->apellido_materno}");

                        try {
                            Mail::html(
                                "Hola Estimado(a) {$nombreCompleto}<br /><br /><br />" .
                                "Para poder ingresar al SIBEN para consultar tus resultados<br />" .
                                "del Examen General de Conocimiento los datos son los siguientes:<br /><br />" .
                                "<b>Usuario: </b>{$alumno->id_usuario}<br />" .
                                "<b>Contraseña: </b>{$alumno->contrasenia}<br /><br /><br />" .
                                "<font color='red'><b>Nota: </b>Debe escribir la contraseña tal como se envió en este correo</font><br /><br /><br />" .
                                "Recibe un cordial saludo.",
                                function ($message) use ($correoIngresado) {
                                    $message->to($correoIngresado, 'Destinatario')
                                            ->subject('Contraseña para consultar tus resultados del Examen General')
                                            ->from('siben@sep.gob.mx', 'SIBEN');
                                }
                            );
                            $mensaje = "Envio de correo exitoso";
                        } catch (\Exception $e) {
                            $mensaje = "Problemas de envio de correo a la siguiente dirección: {$correoIngresado} Intente mas tarde <br>" . $e->getMessage();
                        }
                    } else {
                        $mensaje = "No podra enviarse la informacion al correo";
                    }
                } else {
                    if ($idExamen != $examenIngresado && $correoIngresado == $correo3 && $registrado == 1) {
                        $mensaje = "Los datos que está ingresando son incorrectos. No coincide el Examen realizado favor de verificar.";
                    } elseif ($correoIngresado != $correo3 && $idExamen != $examenIngresado) {
                        $mensaje = "Los datos que está ingresando son incorrectos. No coindicen ni Correo electrónico ni Examen favor de verificar.";
                    } elseif ($idExamen == $examenIngresado && $correoIngresado != $correo3 && $registrado == 1) {
                        $mensaje = "Los datos que está ingresando son incorrectos. El correo electrónico tiene que coincidir con el que te registraste en CENEVAL. Manda un correo a siben@sep.gob.mx para poder recuperar tu contraseña, adjuntando tu credencial de elector escaneada.";
                    } elseif ($idExamen == $examenIngresado && $registrado == 0) {
                        $mensaje = "Debido a que no te registraste en CENEVAL, solicita tu contraseña al correo siben@sep.gob.mx, adjuntando tu credencial de elector escaneada.";
                    }
                }
            } else {
                $mensaje = "Usuario no encontrado.";
            }
        }

        return view('ceneval.solicitud_correo', compact('examenes', 'mensaje', 'correo3'));
    }
}   