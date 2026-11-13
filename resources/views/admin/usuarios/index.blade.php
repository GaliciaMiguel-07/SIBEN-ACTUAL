@extends('layouts.siben.siben')

@section('titulo', 'Lista de Usuarios — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    {{-- Regresar al panel --}}
    <div style="margin-bottom: 20px;">
        <a href="{{ route('siben.index') }}"
           class="boton__secundario"
           style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">
            &larr; Inicio
        </a>
    </div>

    <div class="buscar01" style="margin-bottom: 24px; padding: 24px; background: #fff; border: 1px solid var(--neutro400); border-radius: 4px;">

        <div style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 20px;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">
                Lista de Usuarios
            </h3>
        </div>

        {{-- Mensajes flash --}}
        @if (session('exito') || session('ok'))
            <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('exito') ?? session('ok') }}
            </div>
        @endif
        @if (session('error'))
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $mensaje)
                        <li>{{ $mensaje }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Filtro por entidad y búsqueda por nombre de usuario --}}
        <form id="form-filtros-usuarios" method="GET" action="{{ route('admin.usuarios.index') }}"
              style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 14px;">

            <div>
                <label for="entidad" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Entidad:</label>
                <select id="entidad" name="entidad"
                        style="padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; background: #fff; min-width: 260px;">
                    <option value="">— Todas las entidades —</option>
                    <option value="NE" @selected($entidad === 'NE')>Enlaces nacionales (nivel 2)</option>
                    @foreach ($entidades as $clave => $nombre)
                        <option value="{{ $clave }}" @selected($entidad === (string) $clave)>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="buscar" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Nombre de usuario:</label>
                <input type="text" id="buscar" name="buscar" value="{{ $buscar }}" maxlength="20"
                       placeholder="Escriba el nombre de usuario"
                       style="padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; min-width: 220px; box-sizing: border-box;">
            </div>

            <button type="submit" class="boton__primario" style="padding: 8px 24px; font-size: 0.9rem; cursor: pointer;">
                Buscar
            </button>

            @if ($buscar !== '' || $entidad !== '')
                <a href="{{ route('admin.usuarios.index') }}" class="boton__secundario"
                   style="text-decoration: none; padding: 8px 16px; font-size: 0.9rem; display: inline-block;">
                    Limpiar
                </a>
            @endif

        </form>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px;">
            <form method="POST" action="{{ route('admin.usuarios.toggle.masivo') }}" style="margin: 0;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="accion" value="activar">
                <input type="hidden" name="entidad" value="{{ $entidad }}">
                <input type="hidden" name="buscar" value="{{ $buscar }}">
                <button type="submit" class="boton__secundario"
                        style="padding: 7px 16px; font-size: 0.85rem;"
                        onclick="return confirm('¿Activar a todos los usuarios mostrados en el filtro actual?')">
                    Activar todos
                </button>
            </form>

            <form method="POST" action="{{ route('admin.usuarios.toggle.masivo') }}" style="margin: 0;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="accion" value="desactivar">
                <input type="hidden" name="entidad" value="{{ $entidad }}">
                <input type="hidden" name="buscar" value="{{ $buscar }}">
                <button type="submit" class="boton__secundario"
                        style="padding: 7px 16px; font-size: 0.85rem;"
                        onclick="return confirm('¿Desactivar a todos los usuarios mostrados en el filtro actual? (Nunca se tocan cuentas nivel 1)')">
                    Desactivar todos
                </button>
            </form>
        </div>

        <div style="margin-bottom: 12px; color: var(--neutro700); font-size: 0.9rem;">
            {{ $usuarios->total() }} {{ $usuarios->total() === 1 ? 'usuario' : 'usuarios' }}
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--principal, #811331);">
                        <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">No.</th>
                        <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">No. Usuario</th>
                        <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Nombre</th>
                        <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600; text-align: center;">Acción</th>
                        <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600; text-align: center;">2FA</th>
                        <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600; text-align: center;">Suplantar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $i => $u)
                        <tr style="border-bottom: 1px solid var(--neutro300); vertical-align: top;">
                            <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: top;">
                                {{ $usuarios->firstItem() + $i }}
                            </td>

                            <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: top; white-space: nowrap;">
                                {{ $u->id_usuario }}
                                <div style="font-size: 0.75rem; color: var(--neutro600);">
                                    {{ $u->status === 'a' ? 'Activo' : 'Inactivo' }}
                                </div>
                            </td>

                            {{-- Nombre y correo: en panel admin (nivel 1) solo lectura, sin botón Cambiar --}}
                            <td style="padding: 10px 12px; vertical-align: top;">
                                @if ((int) auth()->user()->nivel === 1)
                                    <div style="font-weight: 600; color: var(--neutro800);">{{ $u->nombre }}</div>
                                    <div style="font-size: 0.8rem; color: var(--neutro600);">{{ $u->correo_electronico !== '' ? $u->correo_electronico : '-' }}</div>
                                @else
                                    <form method="POST" action="{{ route('admin.usuarios.nombre', $u->id) }}" style="margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="nombre" value="{{ $u->nombre }}" maxlength="255"
                                               aria-label="Nombre de {{ $u->id_usuario }}"
                                               style="width: 100%; min-width: 260px; padding: 6px 10px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.85rem; box-sizing: border-box; margin-bottom: 6px;">
                                        <input type="email" name="correo_electronico" value="{{ $u->correo_electronico }}" maxlength="128"
                                               aria-label="Correo de {{ $u->id_usuario }}"
                                               style="width: 100%; min-width: 260px; padding: 6px 10px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.85rem; box-sizing: border-box; margin-bottom: 6px;">
                                        <button type="submit" class="boton__secundario"
                                                style="padding: 5px 18px; font-size: 0.8rem; cursor: pointer;"
                                                onclick="return confirm('¿Guardar los cambios de {{ $u->id_usuario }}?')">
                                            Cambiar
                                        </button>
                                    </form>
                                @endif
                            </td>

                            {{-- Activar / Desactivar --}}
                            <td style="padding: 10px 12px; text-align: center; vertical-align: top;">
                                <form method="POST" action="{{ route('admin.usuarios.toggle', $u->id) }}" style="margin: 0;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            style="padding: 6px 14px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: 1px solid var(--neutro400); border-radius: 4px; background: #fff; color: var(--neutro800);"
                                            onclick="return confirm('¿{{ $u->status === 'a' ? 'Desactivar' : 'Activar' }} a {{ $u->id_usuario }}?')">
                                        {{ $u->status === 'a' ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </td>

                            {{-- 2FA --}}
                            <td style="padding: 10px 12px; text-align: center; vertical-align: top;">
                                @if ($u->google2fa_secret)
                                    <form method="POST" action="{{ route('admin.usuarios.reset2fa', $u->id) }}" style="margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                style="padding: 6px 14px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: 1px solid var(--neutro400); border-radius: 4px; background: #fff; color: var(--neutro800);"
                                                onclick="return confirm('¿Desactivar el 2FA de {{ $u->id_usuario }}? Deberá configurarlo de nuevo con el QR al iniciar sesión.')">
                                            Desactivar 2FA
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size: 0.8rem; color: var(--neutro600);">Sin 2FA</span>
                                @endif
                            </td>

                            {{-- Entrar como --}}
                            <td style="padding: 10px 12px; text-align: center; vertical-align: top;">
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.impersonate', $u->id) }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit"
                                                style="padding: 6px 14px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: 1px solid var(--neutro400); border-radius: 4px; background: #fff; color: var(--neutro800);"
                                                onclick="return confirm('¿Entrar como {{ $u->id_usuario }}? Verá el sistema desde su cuenta.')">
                                            Entrar como
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: var(--neutro600);">
                                No se encontraron usuarios con esos criterios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación (estilo como búsqueda de escuelas) --}}
        <div class="paginacion-container" style="margin-top: 22px;">
            {{ $usuarios->withQueryString()->links('pagination::bootstrap-4') }}
        </div>

    </div>

</div>

@endsection

@push('styles')
<link href="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd-guinda.css" rel="stylesheet">
<style>
    tbody tr:hover {
        background-color: var(--neutro100, #f5f5f5);
    }

    .paginacion-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .pagination {
        list-style-type: none;
        padding: 0;
        display: flex;
        align-items: center;
        column-gap: 0.5rem;
        justify-content: center;
        flex-wrap: wrap;
        margin: 0;
    }

    .page-item {
        margin: 0;
    }

    .page-link {
        border-radius: 5rem !important;
        text-decoration: none;
        width: 1.875rem;
        height: 1.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        color: var(--neutro800);
        background-color: transparent;
        border: none !important;
        padding: 0 !important;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background: var(--neutro400);
        color: initial;
        font-weight: 600;
    }

    .page-item.active .page-link {
        background: var(--gama500) !important;
        color: var(--neutro100) !important;
        font-weight: 600;
    }

    .page-item.disabled .page-link {
        color: var(--neutro500);
        pointer-events: none;
    }
</style>
@endpush

@push('scripts')
<script src="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var entidad = document.getElementById('entidad');
        var formFiltros = document.getElementById('form-filtros-usuarios');

        if (!entidad || !formFiltros) {
            return;
        }

        var buscarAplicado = @json($buscar);
        var urlBase = @json(route('admin.usuarios.index'));

        entidad.addEventListener('change', function () {
            var params = new URLSearchParams();
            if (entidad.value !== '') {
                params.set('entidad', entidad.value);
            }
            if (buscarAplicado !== '') {
                params.set('buscar', buscarAplicado);
            }

            window.location.href = urlBase + (params.toString() ? ('?' + params.toString()) : '');
        });
    });
</script>
@endpush
