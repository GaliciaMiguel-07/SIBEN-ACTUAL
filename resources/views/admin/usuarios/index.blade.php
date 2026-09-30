@extends('layouts.siben.siben')

@section('titulo', 'Administración de Usuarios — SIBEN')

@section('content')

@include('layouts.siben.menu')

<div id="area_contenido" style="margin-left:170px; padding:10px;">

    <h3>Administración de Usuarios</h3>

    {{-- Mensajes flash --}}
    @if (session('ok') || session('exito')) 
        <div class="alert alert-success text-center" style="margin-top:10px; margin-bottom:10px;">
            {{ session('ok') ?? session('exito') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger text-center" style="margin-top:10px; margin-bottom:10px;">
            {{ session('error') }}
        </div>
    @endif
    
    {{-- Buscador --}}
    <form method="GET" action="{{ route('admin.usuarios.index') }}"
        style="margin-bottom:15px; display:flex; gap:10px; flex-wrap:wrap;">

        <input type="text"
            name="buscar"
            value="{{ request('buscar') }}"
            placeholder="Buscar por nombre o usuario..."
            class="form-control"
            style="max-width:280px;">

        <select name="nivel" class="form-control" style="max-width:200px;">
            <option value="">— Todos los niveles —</option>
            @foreach ($niveles as $num => $etiqueta)
                <option value="{{ $num }}" {{ request('nivel') == $num ? 'selected' : '' }}>
                    {{ $etiqueta }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">Buscar</button>

        @if (request('buscar') || request('nivel'))
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-default">
                Limpiar
            </a>
        @endif

    </form>

    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary"
       style="margin-bottom:15px;">
        + Nuevo Usuario
    </a>

    <table class="datos2" style="width:100%;">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Nivel</th>
                <th>Estatus</th>
                <th>2FA</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarios as $u)
                <tr>
                    <td>{{ $u->id_usuario }}</td>
                    <td>{{ $u->nombre }}</td>
                    <td>{{ $u->nivel }}</td>
                    <td>
                        @if ($u->status == 'a')
                            <span style="color:green;">Activo</span>
                        @else
                            <span style="color:red;">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        @if ($u->google2fa_secret)
                            <span style="color:green;">✔ Activo</span>
                        @else
                            <span style="color:#aaa;">Sin 2FA</span>
                        @endif
                    </td>
                    <td>
                        {{-- Editar --}}
                        <a href="{{ route('admin.usuarios.edit', $u->id) }}"
                           class="btn btn-sm btn-default">
                            Editar
                        </a>

                        {{-- Activar / Desactivar --}}
                        <form method="POST"
                              action="{{ route('admin.usuarios.toggle', $u->id) }}"
                              style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-warning"
                                    onclick="return confirm('¿Cambiar estatus de {{ $u->id_usuario }}?')">
                                {{ $u->status == 'a' ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>

                        {{-- Entrar como (solo si no es el admin actual) --}}
                        @if ($u->id !== auth()->id())
                            <form method="POST"
                                  action="{{ route('admin.impersonate', $u->id) }}"
                                  style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-info"
                                        onclick="return confirm('¿Entrar como {{ $u->id_usuario }}? Verás el sistema desde su perspectiva.')">
                                    Entrar como
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">No hay usuarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

        {{-- Paginación --}}
    <div style="margin-top:15px;">
        {{ $usuarios->appends(request()->query())->links() }}
    </div>

</div>

@endsection