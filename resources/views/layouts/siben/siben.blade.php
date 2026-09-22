<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Cache-Control" content="private">
    <meta http-equiv="Cache-Control" content="no-store">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">

    <title>{{ $title ?? 'SIBEN' }}</title>

    {{-- CSS del sistema antiguo --}}
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menuPlegable.css') }}">
    <link rel="stylesheet" href="{{ asset('css/overlay.css') }}">

    {{-- JavaScript del sistema antiguo --}}
    <script src="{{ asset('js/prototype.js') }}"></script>
    <script src="{{ asset('js/test5.js') }}"></script>
    <script src="{{ asset('js/efectos.js') }}"></script>
    <script src="{{ asset('js/common3.js') }}"></script>
    <script src="{{ asset('js/overlay.js') }}"></script>
    <script src="{{ asset('js/overlib.js') }}"></script>
    <script src="{{ asset('js/liveval.js') }}"></script>
</head>

@php
    $onload = "muestraOculta('boton', 'aviso');";
@endphp

<body onload="{{ $onload }}">

<div id="contenedor">

    {{-- Capa que aparece mientras se carga información --}}
    <div id="ocupado" style="display:none;">
        <br><br><br><br><br><br><br><br><br>

        <h1 style="color:red;">CARGANDO...</h1>

        <img
            src="{{ url('/images/spinner2.gif') }}"
            alt="Cargando..."
        >
    </div>

    {{-- Barra superior --}}
    @include('layouts.siben.pleca_superior', [
        'nivel' => $nivel ?? ''
    ])

    <div id="cuerpo">

        <div id="contenido">

            {{-- Mensaje de error --}}
            @if(isset($error))
                <h3 class="error">
                    {{ $error }}
                </h3>
            @endif

            {{-- Contenido de cada página --}}
            @yield('content')

        </div>

    </div>

    {{-- Pie --}}
    <div id="pie">

        @if(isset($anterior))

            @if(is_bool($anterior))
                <button onclick="history.go(-1)">
                    Anterior
                </button>
            @else
                <button onclick="locacion('{{ url($anterior) }}')">
                    Anterior
                </button>
            @endif

        @endif

        @if(isset($id_usuario))
            <button onclick="locacion('{{ url('/siben') }}')">
                Inicio
            </button>
        @endif

    </div>

    {{-- Pie adicional --}}
    @include('layouts.siben.foot')

</div>

<script>
    document.addEventListener('contextmenu', function(event) {
        if (typeof alto === 'function') {
            alto(event);
        }
    });
</script>

</body>
</html>
