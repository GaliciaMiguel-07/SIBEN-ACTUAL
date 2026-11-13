<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\sibenController;
use App\Http\Requests\UsuarioRequest;
use App\Models\Entidad;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends sibenController
{
    /**
     * Lista de usuarios (equivalente a sibenController::listaUsuarios del sistema viejo).
     *
     * Muestra a los usuarios de los niveles 2, 3 y 4 del módulo del administrador,
     * con filtro por entidad y búsqueda por nombre de usuario (id_usuario).
     */
    public function lista(Request $request): View
    {
        $admin = auth()->user();

        $entidad = (string) $request->input('entidad', '');
        $buscar  = trim((string) $request->input('buscar', ''));

        $query = Usuario::query()
            ->where('modulo', $admin->modulo)
            ->whereIn('nivel', [2, 3, 4]);

        // El filtro por entidad usa la clave RENAPO; "NE" son los enlaces nacionales (nivel 2).
        if ($entidad === 'NE') {
            $query->where('nivel', 2);
        } elseif ($entidad !== '') {
            $query->where('id_renapo', $entidad);
        }

        if ($buscar !== '') {
            $query->where('id_usuario', 'like', '%' . $buscar . '%');
        }

        $usuarios = $query->orderBy('id_usuario')
            ->paginate(25)
            ->appends($request->query());

        $entidades = Entidad::orderBy('entidad')->pluck('entidad', 'id_renapo');

        return view('admin.usuarios.index', compact('usuarios', 'entidades', 'entidad', 'buscar'));
    }

    public function create(): View
    {
        $niveles = $this->listaNiveles();
        return view('admin.usuarios.create', compact('niveles'));
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        Usuario::create($request->validated());

        return redirect()
            ->route('admin.usuarios.index')
            ->with('exito', 'Usuario creado correctamente.');
    }

    public function edit(int $id): View
    {
        $usuario = Usuario::findOrFail($id);
        $niveles = $this->listaNiveles();

        return view('admin.usuarios.edit', compact('usuario', 'niveles'));
    }

    public function update(UsuarioRequest $request, int $id): RedirectResponse
    {
        $usuario = Usuario::findOrFail($id);
        $datos   = $request->validated();

        // Si no se envió contraseña nueva, no sobrescribir la actual
        if (empty($datos['contrasenia'])) {
            unset($datos['contrasenia']);
        }

        $usuario->update($datos);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('exito', 'Usuario actualizado.');
    }

    /**
     * Botón "Cambiar": actualiza el nombre y el correo del usuario desde la lista.
     */
    public function cambiarNombre(Request $request, int $id): RedirectResponse
    {
        $usuario = Usuario::findOrFail($id);

        if (!$this->puedeGestionar($usuario)) {
            return back()->with('error', 'No puede modificar ese usuario.');
        }

        $datos = $request->validate([
            'nombre'             => ['required', 'string', 'max:255'],
            'correo_electronico' => ['nullable', 'email', 'max:128'],
        ], [
            'nombre.required' => 'Escriba el nombre del usuario.',
            'nombre.max'      => 'El nombre no puede tener más de 255 caracteres.',
            'correo_electronico.email' => 'El correo electrónico no tiene un formato válido.',
            'correo_electronico.max'   => 'El correo no puede tener más de 128 caracteres.',
        ]);

        // La columna es NOT NULL: si se deja vacía se guarda cadena vacía.
        $datos['nombre']             = trim($datos['nombre']);
        $datos['correo_electronico'] = trim((string) ($datos['correo_electronico'] ?? ''));

        $usuario->update($datos);

        return back()->with('exito', "Se actualizó el nombre de {$usuario->id_usuario}.");
    }

    /**
     * Botón "Activar / Desactivar": alterna el estatus entre 'a' y 'x'.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $usuario = Usuario::findOrFail($id);

        // Comparamos la PK entera (id) con auth()->id() — ambos son enteros
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        if (!$this->puedeGestionar($usuario)) {
            return back()->with('error', 'No puede modificar ese usuario.');
        }

        $usuario->update([
            'status' => $usuario->status === 'a' ? 'x' : 'a',
        ]);

        $estado = $usuario->status === 'a' ? 'activado' : 'desactivado';

        return back()->with('exito', "Usuario {$usuario->id_usuario} $estado.");
    }

    /**
     * Botón "Desactivar 2FA": deja en NULL google2fa_secret para que el usuario
     * vuelva a configurar su segundo factor con el QR en el siguiente inicio de sesión.
     */
    public function reset2fa(int $id): RedirectResponse
    {
        $usuario = Usuario::findOrFail($id);

        if (!$this->puedeGestionar($usuario)) {
            return back()->with('error', 'No puede modificar ese usuario.');
        }

        if (empty($usuario->google2fa_secret)) {
            return back()->with('error', "El usuario {$usuario->id_usuario} no tiene 2FA configurado.");
        }

        $usuario->google2fa_secret = null;
        $usuario->save();

        return back()->with(
            'exito',
            "Se desactivó el 2FA de {$usuario->id_usuario}. Podrá configurarlo de nuevo con el QR al iniciar sesión."
        );
    }

    /**
     * Solo se pueden gestionar usuarios de niveles 2, 3 y 4 del mismo módulo.
     */
    private function puedeGestionar(Usuario $usuario): bool
    {
        $admin = auth()->user();

        return (int) $admin->nivel === 1
            && $usuario->modulo === $admin->modulo
            && in_array((int) $usuario->nivel, [2, 3, 4], true);
    }

    private function listaNiveles(): array
    {
        return [
            1 => 'DGESPE (Admin Nacional)',
            2 => 'Enlace / Supervisor Estatal',
            3 => 'Supervisor de Escuela',
            4 => 'Capturista',
            5 => 'Consulta Externa',
            6 => 'Observador Estatal',
            7 => 'Búsqueda',
        ];
    }
}
