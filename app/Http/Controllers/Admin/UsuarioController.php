<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\sibenController;
use App\Http\Requests\UsuarioRequest;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends sibenController
{

    public function index(): View
    {
        $request = request(); // Obtenemos el request con el helper
        $query = Usuario::query();

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                // Las columnas reales de la tabla `usuarios` son `nombre` e `id_usuario`
                $q->where('nombre',     'like', "%$b%")
                  ->orWhere('id_usuario', 'like', "%$b%");
            });
        }

        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        $usuarios = $query->orderBy('nivel')->orderBy('nombre')->paginate(20);
        $niveles  = $this->listaNiveles();

        return view('admin.usuarios.index', compact('usuarios', 'niveles'));
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

    public function toggleStatus(int $id): RedirectResponse
    {
        $usuario = Usuario::findOrFail($id);

        // Comparamos la PK entera (id) con auth()->id() — ambos son enteros
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update([
            'status' => $usuario->status === 'a' ? 'x' : 'a',
        ]);

        $estado = $usuario->status === 'a' ? 'activado' : 'desactivado';

        return redirect()
            ->route('admin.usuarios.index')
            ->with('exito', "Usuario $estado.");
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
