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

    /**
     * Proceso de inicio de sesión 
     */
    public function authenticate(Request $request)
    {
        $id_usuario = $request->input('id_usuario');
        $contrasenia = $request->input('contrasenia');

        // Buscar usuario
        $usuario = User::where('id_usuario', $id_usuario)->first();

        if (!$usuario) {
            return redirect()
                ->route('usuarios.login')
                ->with('error', 'Usuario o contraseña incorrectos.');
        }

        // Comprobar contraseña usando SHA-1, igual que CakePHP
        if (sha1($contrasenia) !== $usuario->contrasenia) {
            return redirect()
                ->route('usuarios.login')
                ->with('error', 'Usuario o contraseña incorrectos.');
        }

        // Comprobar que el usuario esté activo
        if ($usuario->status !== 'a') {
            return redirect()
                ->route('usuarios.login')
                ->with('error', 'El usuario no está activo.');
        }

        /*
        * Datos básicos del usuario.
        * Esto corresponde a los Session->write()
        * que hacía UsuariosController de CakePHP.
        */
        $datosUsuario = [
            'id' => $usuario->id,
            'id_usuario' => $usuario->id_usuario,
            'nombre' => $usuario->nombre,
            'nivel' => $usuario->nivel,
            'modulo' => $usuario->modulo,
            'status' => $usuario->status,
            'id_renapo' => $usuario->id_renapo,
        ];

        /*
        * Datos adicionales dependiendo del nivel
        * del usuario.
        */
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
                // Consulta

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
                // No se agregan datos adicionales
                break;
        }

        // Guardar toda la información en sesión
        session([
            'Usuario' => $datosUsuario
        ]);

        return redirect('/siben');
    }


    /**
     * Lista datos
     */
    public function index()
    {
        return view('layouts.usuarios.login');
    }

    /**
     * Muestra un formulario
     */
    public function create()
    {
        return view('layouts.usuarios.create');
    }

    /**
     * Recibe y guarda datos de formulario
     */
    public function store(Request $request)
    {
        return redirect()->route('usuarios.login');
    }

}
