<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Entidad;
use App\Models\Escuela;

class usuariosController extends Controller
{
    /**
     * Mostrar formulario de inicio de sesión
     */
    public function login()
    {
        return view('layouts.usuarios.login');
    }

    public function authenticate(Request $request)
    {
        $id_usuario = $request->input('id_usuario');
        $contrasenia = $request->input('contrasenia');

        $usuario = User::where('id_usuario', $id_usuario)->first();

        if (!$usuario) {
            return redirect()
                ->route('usuarios.login')
                ->with('error', 'Usuario o contraseña incorrectos.');
        }

        if (sha1($contrasenia) !== $usuario->contrasenia) {
            return redirect()
                ->route('usuarios.login')
                ->with('error', 'Usuario o contraseña incorrectos.');
        }

        if ($usuario->status !== 'a') {
            return redirect()
                ->route('usuarios.login')
                ->with('error', 'El usuario no está activo.');
        }

        $datosUsuario = [
            'id' => $usuario->id,
            'id_usuario' => $usuario->id_usuario,
            'nombre' => $usuario->nombre,
            'nivel' => $usuario->nivel,
            'modulo' => $usuario->modulo,
            'status' => $usuario->status,
            'id_renapo' => $usuario->id_renapo,
        ];

        switch ($usuario->nivel) {

            case 2:
                // Supervisor estatal
                $entidad = Entidad::where(
                    'id_renapo',
                    $usuario->id_renapo
                )->first();

                if ($entidad) {
                    $datosUsuario['id_entidad'] = $entidad->id;
                    $datosUsuario['entidad'] = $entidad->entidad;
                }

                $datosUsuario['entorno'] = $usuario->id_renapo;
                break;


            case 3:
            case 4:
                // Supervisor de escuela / Capturista

                $escuela = Escuela::find($usuario->id_escuela);

                if ($escuela) {

                    $entidad = Entidad::find($escuela->id_entidad);

                    if ($entidad) {
                        $datosUsuario['id_entidad'] = $entidad->id;
                        $datosUsuario['entidad'] = $entidad->entidad;
                    }

                    $datosUsuario['id_escuela'] = $escuela->id;
                    $datosUsuario['escuela'] = $escuela->escuela;
                }

                $datosUsuario['entorno'] = $usuario->id_renapo;
                break;


            case 6:
                $entidad = Entidad::where(
                    'id_renapo',
                    $usuario->id_renapo
                )->first();

                if ($entidad) {
                    $datosUsuario['id_entidad'] = $entidad->id;
                    $datosUsuario['entidad'] = $entidad->entidad;
                }

                $datosUsuario['entorno'] = $usuario->id_renapo;
                break;


            default:
                break;
        }

        // Guardar toda la información en sesión
        session([
            'Usuario' => $datosUsuario
        ]);

        return redirect('/siben');
    }


    public function logout()
    {
        session()->forget('Usuario');
        session()->flush();

        return redirect()->route('usuarios.login');
    }

    // Lista datos
    public function index()
    {
        return view('layouts.usuarios.login');
    }

    //Muestra formulario
    public function create()
    {
        return view('layouts.usuarios.create');
    }

    //Recibe y guarda los datos del formulario
    public function store(Request $request)
    {
        return redirect()->route('usuarios.login');
    }                                
}
