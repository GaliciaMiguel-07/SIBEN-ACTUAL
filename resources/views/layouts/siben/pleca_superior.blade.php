@php
    $usuario = auth()->user();

    $nivel = $usuario->nivel ?? 0;
    $nombre = $usuario->nombre ?? '';

    $nombreEntidad = '';
    $nombreEscuela = '';

    // Solo consultar datos si existe un usuario autenticado
    if ($usuario) {

        // Obtener entidad
        $entidadObj = \Illuminate\Support\Facades\DB::table('cat_entidades')
                        ->where('id_renapo', $usuario->id_renapo ?? '')
                        ->first();

        $nombreEntidad = $entidadObj->entidad
                        ?? $entidadObj->nombre
                        ?? '';

        // Obtener escuela
        $nombreEscuela = $usuario->escuela->escuela ?? '';
    }
@endphp


<div id="pleca_superior">

    {{-- Imagen principal --}}
    <img
        src="{{ url('/images/pleca_superior2020.jpg') }}"
        alt="SEP - Sistema de Información Básica de Escuelas Normales"
        class="pleca-imagen"
    >


    {{-- Imagen correspondiente al nivel --}}
    @if ($nivel > 0)
        <img
            src="{{ url('/images/pleca_superior_subtitulo' . $nivel . '.jpg') }}"
            alt="Nivel {{ $nivel }}"
            class="pleca-imagen"
        >
    @endif


    {{-- Información del usuario --}}
    @if ($usuario)

        <div id="info_usuario">

            <div class="datos-usuario">

                @if ($nivel == 1)

                    <strong>{{ $nombre }}</strong>

                @elseif ($nivel == 2)

                    <strong>Entidad:</strong>
                    {{ $nombreEntidad }}

                    <span class="separador">|</span>

                    <strong>{{ $nombre }}</strong>

                @elseif ($nivel >= 3 && $nivel <= 5)

                    <strong>Escuela:</strong>
                    {{ $nombreEscuela }}

                    <span class="separador">|</span>

                    <strong>Entidad:</strong>
                    {{ $nombreEntidad }}

                    <span class="separador">|</span>

                    <strong>{{ $nombre }}</strong>

                @else

                    <strong>{{ $nombre }}</strong>

                @endif

            </div>


            {{-- Cerrar sesión --}}
            <div class="cerrar-sesion">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="btn-cerrar-sesion">
                        Cerrar sesión
                    </button>
                </form>

            </div>

        </div>

    @endif

</div>


<style>

    /* =========================================================
       PLECA SUPERIOR
    ========================================================= */

    #pleca_superior {
        width: 100%;
        margin: 0;
        padding: 0;
        background: #ffffff;
    }


    /* =========================================================
       IMÁGENES DE LA PLECA
    ========================================================= */

    .pleca-imagen {
        width: 100%;
        height: auto;
        display: block;
    }


    /* =========================================================
       INFORMACIÓN DEL USUARIO
    ========================================================= */

    #info_usuario {
        width: 100%;
        box-sizing: border-box;

        background-color: #f5f5f5;

        border-top: 1px solid #dddddd;
        border-bottom: 3px solid #611232;

        padding: 8px 20px;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

        font-size: 13px;
        color: #404040;

        text-align: right;
    }


    /* =========================================================
       DATOS
    ========================================================= */

    .datos-usuario {
        line-height: 20px;
    }

    .datos-usuario strong {
        font-weight: 600;
        color: #611232;
    }


    /* =========================================================
       SEPARADORES
    ========================================================= */

    .separador {
        margin: 0 8px;
        color: #777777;
    }


    /* =========================================================
       CERRAR SESIÓN
    ========================================================= */

    .cerrar-sesion {
        margin-top: 4px;
    }

    .btn-cerrar-sesion {
        background: transparent;
        border: none;

        padding: 0;

        color: #611232;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

        font-size: 12px;
        font-weight: 600;

        text-decoration: underline;

        cursor: pointer;

        transition: color 0.2s ease;
    }

    .btn-cerrar-sesion:hover {
        color: #4d0e27;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 768px) {

        #info_usuario {
            padding: 8px 12px;
            font-size: 12px;
            text-align: center;
        }

        .datos-usuario {
            line-height: 22px;
        }

        .separador {
            margin: 0 4px;
        }

    }


    @media (max-width: 480px) {

        #info_usuario {
            font-size: 11px;
        }

        .datos-usuario {
            line-height: 20px;
        }

    }

</style>