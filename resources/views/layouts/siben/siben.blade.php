<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SIBEN - @yield('titulo', 'Sistema de Información Básica de Escuelas Normales')</title>

    {{-- CSS heredado del sistema original --}}
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

    {{-- Hoja oficial de estilos del Gobierno de México (apariencia original de SIBEN) --}}
    <link rel="stylesheet" href="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd-guinda.css">

    {{-- Bootstrap (si lo usas) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">

    @stack('styles')
</head>

<body onload="@yield('onload', '')">

    {{-- Pleca superior con datos del usuario --}}
    @include('layouts.siben.pleca_superior')

    {{-- Área de contenido principal --}}
    <div id="contenido">
        @yield('content')
    </div>

    {{-- Pie de página --}}
    @include('layouts.siben.foot')

    {{-- JS heredados del sistema original --}}
    <script src="{{ asset('js/prototype.js') }}"></script>
    <script src="{{ asset('js/test5.js') }}"></script>
    <script src="{{ asset('js/efectos.js') }}"></script>
    <script src="{{ asset('js/common3.js') }}"></script>
    <script src="{{ asset('js/overlay.js') }}"></script>
    <script src="{{ asset('js/overlib.js') }}"></script>
    <script src="{{ asset('js/liveval.js') }}"></script>
    <script src="{{ asset('js/security.js') }}"></script>

    @stack('scripts')
</body>
</html>