@extends('layouts.siben.siben')

@section('titulo', 'Administración de Usuarios — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    <!-- Botón de regreso al panel -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.index') }}" class="boton__secundario" style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">
            &larr; Panel
        </a>
    </div>

    <!-- Tarjeta contenedora principal -->
    <div class="buscar01" style="margin-bottom: 24px; padding: 24px; background: #fff; border: 1px solid var(--neutro400); border-radius: 4px;">
        
        <div style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 20px;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">Administración de Usuarios</h3>
        </div>

        {{-- Mensajes flash --}}
        @if (session('ok') || session('exito')) 
            <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                {{ session('ok') ?? session('exito') }}
            </div>
        @endif
        @if (session('error'))
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                {{ session('error') }}
            </div>
        @endif
        
        {{-- Buscador y botón nuevo usuario alineados --}}
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
            <form method="GET" action="{{ route('admin.usuarios.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin: 0;">

                <input type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Buscar por nombre o usuario..."
                    style="padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; max-width: 280px; font-size: 0.9rem;">

                <select name="nivel" style="padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; max-width: 200px; font-size: 0.9rem; background-color: #fff;">
                    <option value="">— Todos los niveles —</option>
                    @foreach ($niveles as $num => $etiqueta)
                        <option value="{{ $num }}" {{ request('nivel') == $num ? 'selected' : '' }}>
                            {{ $etiqueta }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="boton__primario" style="padding: 8px 16px; font-size: 0.9rem; cursor: pointer;">Buscar</button>

                @if (request('buscar') || request('nivel'))
                    <a href="{{ route('admin.usuarios.index') }}" class="boton__secundario" style="text-decoration: none; padding: 8px 16px; font-size: 0.9rem; display: inline-block;">
                        Limpiar
                    </a>
                @endif

            </form>

            <a href="{{ route('admin.usuarios.create') }}" class="boton__primario" style="text-decoration: none; padding: 8px 16px; font-size: 0.9rem; display: inline-block;">
                + Nuevo Usuario
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 15px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--neutro400);">
                        <th style="padding: 12px 15px; background: var(--principal, #811331); color: #fff; font-weight: 600;">Usuario</th>
                        <th style="padding: 12px 15px; background: var(--principal, #811331); color: #fff; font-weight: 600;">Nombre</th>
                        <th style="padding: 12px 15px; background: var(--principal, #811331); color: #fff; font-weight: 600;">Nivel</th>
                        <th style="padding: 12px 15px; background: var(--principal, #811331); color: #fff; font-weight: 600;">Estatus</th>
                        <th style="padding: 12px 15px; background: var(--principal, #811331); color: #fff; font-weight: 600;">2FA</th>
                        <th style="padding: 12px 15px; background: var(--principal, #811331); color: #fff; font-weight: 600; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $u)
                        <tr style="border-bottom: 1px solid var(--neutro300);">
                            <td style="padding: 12px 15px; color: var(--neutro800); vertical-align: middle;">{{ $u->id_usuario }}</td>
                            <td style="padding: 12px 15px; color: var(--neutro800); vertical-align: middle;">{{ $u->nombre }}</td>
                            <td style="padding: 12px 15px; color: var(--neutro800); vertical-align: middle;">{{ $u->nivel }}</td>
                            <td style="padding: 12px 15px; vertical-align: middle;">
                                @if ($u->status == 'a')
                                    <span style="color: #28a745; font-weight: 600;">Activo</span>
                                @else
                                    <span style="color: #dc3545; font-weight: 600;">Inactivo</span>
                                @endif
                            </td>
                            <td style="padding: 12px 15px; vertical-align: middle;">
                                @if ($u->google2fa_secret)
                                    <span style="color: #28a745; font-weight: 600;">✔ Activo</span>
                                @else
                                    <span style="color: var(--neutro500);">Sin 2FA</span>
                                @endif
                            </td>
                            <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                                <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                    {{-- Editar --}}
                                    <a href="{{ route('admin.usuarios.edit', $u->id) }}"
                                       class="boton__secundario" style="text-decoration: none; padding: 4px 10px; font-size: 0.8rem; display: inline-block;">
                                        Editar
                                    </a>

                                    {{-- Activar / Desactivar --}}
                                    <form method="POST"
                                          action="{{ route('admin.usuarios.toggle', $u->id) }}"
                                          style="display:inline; margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" style="padding: 4px 10px; background: #ffc107; color: #212529; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; font-weight: 600;"
                                                onclick="return confirm('¿Cambiar estatus de {{ $u->id_usuario }}?')">
                                            {{ $u->status == 'a' ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>

                                    {{-- Entrar como (solo si no es el admin actual) --}}
                                    @if ($u->id !== auth()->id())
                                        <form method="POST"
                                              action="{{ route('admin.impersonate', $u->id) }}"
                                              style="display:inline; margin: 0;">
                                            @csrf
                                            <button type="submit" style="padding: 4px 10px; background: #17a2b8; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; font-weight: 600;"
                                                    onclick="return confirm('¿Entrar como {{ $u->id_usuario }}? Verás el sistema desde su perspectiva.')">
                                                Entrar como
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 20px; color: var(--neutro600);">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div style="margin-top: 20px;">
            {{ $usuarios->appends(request()->query())->links() }}
        </div>

    </div>

</div>

@endsection

@push('styles')
<link href="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd-guinda.css" rel="stylesheet">
<style>
    tr:hover {
        background-color: var(--neutro100);
    }
</style>
@endpush

@push('scripts')
<script src="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd.js"></script>
@endpush