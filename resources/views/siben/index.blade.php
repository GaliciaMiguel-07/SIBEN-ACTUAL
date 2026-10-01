@extends('layouts.siben.siben')

@section('titulo', 'Inicio — SIBEN')

@section('content')

<div class="inicio-siben">

    {{-- =====================================================
         BIENVENIDA
    ====================================================== --}}
    <div class="bienvenida-siben">
        <h3>
            Bienvenido,
            <strong>{{ auth()->user()->nombre }}</strong>
        </h3>
    </div>


    {{-- =====================================================
         MENSAJES FLASH
    ====================================================== --}}
    @if (session('ok'))
        <div class="mensaje-siben mensaje-exito">
            {{ session('ok') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mensaje-siben mensaje-error">
            {{ session('error') }}
        </div>
    @endif


    {{-- =====================================================
         MENÚ
    ====================================================== --}}
    @include('layouts.siben.menu')


    <div id="area_contenido">

        {{-- Botón de regreso --}}
        @isset($anterior)
            <a href="{{ $anterior }}" class="btn-regresar">
                &larr; Regresar
            </a>
        @endisset


        <!--
        {{-- Mensajes del sistema --}}
        @if (!empty($mensajes) && $mensajes->isNotEmpty())

            <table class="datos2 tabla-mensajes">

                @foreach ($mensajes as $mensaje)

                    <tr>
                        <td>
                            {!! nl2br(e($mensaje->mensaje ?? '')) !!}
                        </td>
                    </tr>

                @endforeach

            </table>

        @else

            <p class="sin-mensajes">
                No hay mensajes nuevos del sistema.
            </p>

        @endif
        -->


        {{-- =================================================
             AYUDA
        ================================================== --}}
        @if ($nivel ?? 0)

            @if ($nivel != 1)

                <div class="ayuda-siben">

                    <a href="http://www.siben.sep.gob.mx/menu_ayuda/index.php"
                       target="_blank">

                        <span class="icono-ayuda">?</span>
                        Ayuda

                    </a>

                </div>

            @endif

        @endif

    </div>

</div>


<style>

    /* =========================================================
       CONTENEDOR PRINCIPAL
    ========================================================= */

    .inicio-siben {

        width: 100%;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

        color: #404040;

    }


    /* =========================================================
       BIENVENIDA
    ========================================================= */

    .bienvenida-siben {

        margin: 25px 0 20px 0;

    }

    .bienvenida-siben h3 {

        margin: 0;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

        font-size: 24px;

        font-weight: 400;

        color: #404040;

        line-height: 1.4;

    }

    .bienvenida-siben h3 strong {

        color: #404040;

        font-weight: 600;

    }


    /* =========================================================
       MENSAJES
    ========================================================= */

    .mensaje-siben {

        margin: 15px 0;

        padding: 12px 15px;

        border-radius: 4px;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

        font-size: 14px;

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
       ÁREA DE CONTENIDO
    ========================================================= */

    #area_contenido {

        margin-left: 170px;

        padding: 10px;

        font-family:
            "Montserrat",
            "Helvetica Neue",
            Helvetica,
            Arial,
            sans-serif;

    }


    /* =========================================================
       BOTÓN REGRESAR
    ========================================================= */

    .btn-regresar {

        display: inline-block;

        padding: 7px 15px;

        margin-bottom: 15px;

        background-color: #ffffff;

        color: #611232 !important;

        border: 1px solid #611232;

        border-radius: 4px;

        text-decoration: none !important;

        font-size: 14px;

        font-weight: 600;

        transition: all 0.2s ease;

    }


    .btn-regresar:hover {

        background-color: #611232;

        color: #ffffff !important;

    }


    /* =========================================================
       MENSAJES DEL SISTEMA
    ========================================================= */

    .tabla-mensajes {

        width: 450px;

        border-collapse: collapse;

        margin-top: 15px;

    }


    .tabla-mensajes td {

        padding: 12px;

        border: 1px solid #dddddd;

        background-color: #f7f7f7;

        font-size: 15px;

        line-height: 1.5;

    }


    .sin-mensajes {

        color: #777777;

        font-size: 14px;

    }


    /* =========================================================
       AYUDA
    ========================================================= */

    .ayuda-siben {

        text-align: right;

        margin-top: 25px;

        padding-top: 10px;

    }


    .ayuda-siben a {

        color: #cc1543 !important;

        font-size: 14px;

        font-weight: 600;

        text-decoration: none;

    }


    .ayuda-siben a:hover {

        text-decoration: underline;

    }


    .icono-ayuda {

        display: inline-block;

        width: 20px;

        height: 20px;

        margin-right: 4px;

        border-radius: 50%;

        background-color: #cc1543;

        color: #ffffff;

        text-align: center;

        line-height: 20px;

        font-weight: bold;

    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 768px) {

        .bienvenida-siben {

            margin-top: 20px;

        }

        .bienvenida-siben h3 {

            font-size: 21px;

        }

        #area_contenido {

            margin-left: 0;

            padding: 10px;

        }

        .tabla-mensajes {

            width: 100%;

        }

        .ayuda-siben {

            text-align: center;

        }

    }


    @media (max-width: 480px) {

        .bienvenida-siben h3 {

            font-size: 19px;

        }

        .mensaje-siben {

            font-size: 13px;

        }

    }

</style>

@endsection