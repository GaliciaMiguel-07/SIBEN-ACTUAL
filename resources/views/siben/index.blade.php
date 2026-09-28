@extends('layouts.siben.siben')

@section('titulo', 'Inicio — SIBEN')

@section('content')

<h3>Bienvenido, {{ auth()->user()->nombre }}</h3>


@include('layouts.siben.menu')

<div id="area_contenido" style="margin-left:170px; padding:10px;">

    
    {{-- Botón de regreso (si aplica) --}}
    @isset($anterior)
        <a href="{{ $anterior }}" class="btn btn-sm btn-secondary mb-3">&larr; Regresar</a>
    @endisset

    <!-- {{-- Mensajes del sistema --}}
    @if (!empty($mensajes) && $mensajes->isNotEmpty())
        <table class="datos2" style="width:450px;">
            @foreach ($mensajes as $mensaje)
                <tr>
                    <td style="font-size:1.1em;">
                        {{--
                            La columna real se llama `mensaje` (no `texto`).
                            nl2br preserva los saltos de línea del texto almacenado.
                        --}}
                        {!! nl2br(e($mensaje->mensaje ?? '')) !!}
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="text-muted">No hay mensajes nuevos del sistema.</p>
    @endif -->

    {{-- Espacio extra para niveles con layout más amplio --}}
    @if ($nivel ?? 0)
        @if ($nivel != 1)
            <br><br><br>
            <div style="text-align:right;">
                <span style="color:#cc1543;">
                    <a href="http://www.siben.sep.gob.mx/menu_ayuda/index.php"
                       target="_blank"
                       style="color:#cc1543;">
                        <b>?</b> Ayuda
                    </a>
                </span>
            </div>
        @endif
    @endif

</div>

@endsection
