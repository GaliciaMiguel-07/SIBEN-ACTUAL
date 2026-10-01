@extends('layouts.siben.siben')

@section('titulo', 'Administración de Usuarios — SIBEN')

@section('content')

@include('layouts.siben.menu')


<div id="area_contenido" class="usuarios-siben">

    {{-- =====================================================
         TÍTULO
    ====================================================== --}}
    <div class="titulo-usuarios">
        <h3>Administración de Usuarios</h3>
    </div>


    {{-- =====================================================
         MENSAJES FLASH
    ====================================================== --}}
    @if (session('ok') || session('exito'))
        <div class="mensaje-siben mensaje-exito">
            {{ session('ok') ?? session('exito') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mensaje-siben mensaje-error">
            {{ session('error') }}
        </div>
    @endif


    {{-- =====================================================
         BUSCADOR
    ====================================================== --}}
    <form method="GET"
          action="{{ route('admin.usuarios.index') }}"
          class="buscador-usuarios">

        <input type="text"
               name="buscar"
               value="{{ request('buscar') }}"
               placeholder="Buscar por nombre o usuario..."
               class="campo-busqueda">

        <select name="nivel" class="campo-nivel">

            <option value="">
                — Todos los niveles —
            </option>

            @foreach ($niveles as $num => $etiqueta)

                <option value="{{ $num }}"
                    {{ request('nivel') == $num ? 'selected' : '' }}>
                    {{ $etiqueta }}
                </option>

            @endforeach

        </select>


        <button type="submit" class="btn-buscar">
            Buscar
        </button>


        @if (request('buscar') || request('nivel'))

            <a href="{{ route('admin.usuarios.index') }}"
               class="btn-limpiar">
                Limpiar
            </a>

        @endif

    </form>


    {{-- =====================================================
         NUEVO USUARIO
    ====================================================== --}}
    <div class="acciones-usuarios">

        <a href="{{ route('admin.usuarios.create') }}"
           class="btn-nuevo">
            + Nuevo Usuario
        </a>

    </div>


    {{-- =====================================================
         TABLA DE USUARIOS
    ====================================================== --}}
    <div class="tabla-responsive">

        <table class="tabla-usuarios">

            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Nivel</th>
                    <th>Estatus</th>
                    <th>2FA</th>
                    <th class="columna-acciones">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($usuarios as $u)

                    <tr>

                        <td>
                            {{ $u->id_usuario }}
                        </td>

                        <td>
                            {{ $u->nombre }}
                        </td>

                        <td>
                            {{ $u->nivel }}
                        </td>


                        {{-- ESTATUS --}}
                        <td>

                            @if ($u->status == 'a')

                                <span class="estado activo">
                                    Activo
                                </span>

                            @else

                                <span class="estado inactivo">
                                    Inactivo
                                </span>

                            @endif

                        </td>


                        {{-- 2FA --}}
                        <td>

                            @if ($u->google2fa_secret)

                                <span class="estado-2fa activo-2fa">
                                    ✔ Activo
                                </span>

                            @else

                                <span class="estado-2fa sin-2fa">
                                    Sin 2FA
                                </span>

                            @endif

                        </td>


                        {{-- ACCIONES --}}
                        <td class="acciones-tabla">

                            {{-- Editar --}}
                            <a href="{{ route('admin.usuarios.edit', $u->id) }}"
                               class="btn-accion btn-editar">
                                Editar
                            </a>


                            {{-- Activar / Desactivar --}}
                            <form method="POST"
                                  action="{{ route('admin.usuarios.toggle', $u->id) }}"
                                  class="form-accion">

                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                        class="btn-accion btn-estatus"
                                        onclick="return confirm('¿Cambiar estatus de {{ $u->id_usuario }}?')">

                                    {{ $u->status == 'a' ? 'Desactivar' : 'Activar' }}

                                </button>

                            </form>


                            {{-- Entrar como --}}
                            @if ($u->id !== auth()->id())

                                <form method="POST"
                                      action="{{ route('admin.impersonate', $u->id) }}"
                                      class="form-accion">

                                    @csrf

                                    <button type="submit"
                                            class="btn-accion btn-entrar"
                                            onclick="return confirm('¿Entrar como {{ $u->id_usuario }}? Verás el sistema desde su perspectiva.')">

                                        Entrar como

                                    </button>

                                </form>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="sin-usuarios">
                            No hay usuarios registrados.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =====================================================
         PAGINACIÓN
    ====================================================== --}}
    <div class="paginacion-usuarios">

        {{ $usuarios->appends(request()->query())->links() }}

    </div>

</div>


<style>

    /* =========================================================
       CONTENEDOR
    ========================================================= */

    .usuarios-siben {

        margin-left: 170px;
        padding: 10px;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

        color: #404040;

    }


    /* =========================================================
       TÍTULO
    ========================================================= */

    .titulo-usuarios {

        margin: 25px 0 20px 0;

    }

    .titulo-usuarios h3 {

        margin: 0;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

        font-size: 24px;

        font-weight: 500;

        color: #404040;

    }


    /* =========================================================
       MENSAJES
    ========================================================= */

    .mensaje-siben {

        margin: 10px 0;

        padding: 12px 15px;

        border-radius: 4px;

        font-size: 14px;

        text-align: center;

    }

    .mensaje-exito {

        color: #3c763d;

        background-color: #dff0d8;

        border: 1px solid #d6e9c6;

    }

    .mensaje-error {

        color: #a94442;

        background-color: #f2dede;

        border: 1px solid #ebccd1;

    }


    /* =========================================================
       BUSCADOR
    ========================================================= */

    .buscador-usuarios {

        display: flex;

        align-items: center;

        gap: 10px;

        flex-wrap: wrap;

        margin-bottom: 15px;

    }


    .campo-busqueda,
    .campo-nivel {

        height: 38px;

        padding: 6px 12px;

        border: 1px solid #cccccc;

        border-radius: 4px;

        background-color: #ffffff;

        color: #404040;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

        font-size: 14px;

        box-sizing: border-box;

    }


    .campo-busqueda {

        width: 280px;

    }


    .campo-nivel {

        width: 200px;

    }


    .campo-busqueda:focus,
    .campo-nivel:focus {

        border-color: #611232;

        outline: none;

        box-shadow: 0 0 3px rgba(97, 18, 50, 0.25);

    }


    /* =========================================================
       BOTONES DEL BUSCADOR
    ========================================================= */

    .btn-buscar,
    .btn-limpiar,
    .btn-nuevo {

        display: inline-block;

        padding: 8px 16px;

        border-radius: 4px;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

        font-size: 14px;

        font-weight: 600;

        text-decoration: none !important;

        cursor: pointer;

        transition: all 0.2s ease;

        box-sizing: border-box;

    }


    .btn-buscar {

        background-color: #611232;

        color: #ffffff;

        border: 1px solid #611232;

    }


    .btn-buscar:hover {

        background-color: #4d0e27;

        border-color: #4d0e27;

    }


    .btn-limpiar {

        background-color: #ffffff;

        color: #611232 !important;

        border: 1px solid #611232;

    }


    .btn-limpiar:hover {

        background-color: #611232;

        color: #ffffff !important;

    }


    /* =========================================================
       NUEVO USUARIO
    ========================================================= */

    .acciones-usuarios {

        margin-bottom: 15px;

    }


    .btn-nuevo {

        background-color: #611232;

        color: #ffffff !important;

        border: 1px solid #611232;

    }


    .btn-nuevo:hover {

        background-color: #4d0e27;

        border-color: #4d0e27;

    }


    /* =========================================================
       TABLA
    ========================================================= */

    .tabla-responsive {

        width: 100%;

        overflow-x: auto;

    }


    .tabla-usuarios {

        width: 100%;

        border-collapse: collapse;

        background-color: #ffffff;

        border: 1px solid #dddddd;

        font-size: 14px;

    }


    .tabla-usuarios thead th {

        padding: 13px 12px;

        background-color: #611232;

        color: #ffffff;

        border: 1px solid #611232;

        font-weight: 600;

        text-align: left;

        white-space: nowrap;

    }


    .tabla-usuarios tbody td {

        padding: 11px 12px;

        border: 1px solid #dddddd;

        vertical-align: middle;

    }


    .tabla-usuarios tbody tr:nth-child(even) {

        background-color: #f7f7f7;

    }


    .tabla-usuarios tbody tr:hover {

        background-color: #eeeeee;

    }


    /* =========================================================
       ESTADOS
    ========================================================= */

    .estado {

        font-weight: 600;

    }


    .estado.activo {

        color: #3c763d;

    }


    .estado.inactivo {

        color: #a94442;

    }


    .estado-2fa {

        font-weight: 600;

    }


    .activo-2fa {

        color: #3c763d;

    }


    .sin-2fa {

        color: #999999;

    }


    /* =========================================================
       ACCIONES DE TABLA
    ========================================================= */

    .columna-acciones {

        min-width: 270px;

        text-align: center !important;

    }


    .acciones-tabla {

        text-align: center;

        white-space: nowrap;

    }


    .form-accion {

        display: inline;

        margin: 0;

        padding: 0;

    }


    .btn-accion {

        display: inline-block;

        padding: 6px 10px;

        margin: 2px;

        border-radius: 4px;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

        font-size: 12px;

        font-weight: 600;

        text-decoration: none !important;

        cursor: pointer;

        transition: all 0.2s ease;

    }


    /* Editar */

    .btn-editar {

        background-color: #ffffff;

        color: #611232 !important;

        border: 1px solid #611232;

    }


    .btn-editar:hover {

        background-color: #611232;

        color: #ffffff !important;

    }


    /* Activar / Desactivar */

    .btn-estatus {

        background-color: #f0ad4e;

        color: #ffffff;

        border: 1px solid #eea236;

    }


    .btn-estatus:hover {

        background-color: #ec971f;

        border-color: #d58512;

    }


    /* Entrar como */

    .btn-entrar {

        background-color: #5bc0de;

        color: #ffffff;

        border: 1px solid #46b8da;

    }


    .btn-entrar:hover {

        background-color: #31b0d5;

        border-color: #269abc;

    }


    /* =========================================================
       SIN USUARIOS
    ========================================================= */

    .sin-usuarios {

        padding: 20px !important;

        text-align: center;

        color: #777777;

        font-size: 14px;

    }


    /* =========================================================
       PAGINACIÓN
    ========================================================= */

    .paginacion-usuarios {

        margin-top: 15px;

    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 768px) {

        .usuarios-siben {

            margin-left: 0;

            padding: 10px;

        }


        .titulo-usuarios h3 {

            font-size: 21px;

        }


        .campo-busqueda,
        .campo-nivel {

            width: 100%;

            max-width: none;

        }


        .btn-buscar,
        .btn-limpiar {

            width: auto;

        }


        .tabla-usuarios {

            font-size: 13px;

        }


        .tabla-usuarios tbody td,
        .tabla-usuarios thead th {

            padding: 9px 8px;

        }

    }


    @media (max-width: 480px) {

        .titulo-usuarios h3 {

            font-size: 19px;

        }


        .buscador-usuarios {

            flex-direction: column;

            align-items: stretch;

        }


        .btn-buscar,
        .btn-limpiar {

            width: 100%;

            text-align: center;

        }


        .acciones-usuarios .btn-nuevo {

            width: 100%;

            text-align: center;

        }

    }

</style>

@endsection