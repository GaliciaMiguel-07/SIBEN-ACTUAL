@extends('layouts.siben.siben')

@section('titulo', 'Personal')

@section('content')
@php
    $obtenerRuta = function ($sublista) {
        $controlador = $sublista[0] ?? '';
        $accion = $sublista[2] ?? '';

        if ($controlador === 'consultas_personal' && $accion === 'formulario') {
            return route('consultas_personal.formulario');
        }

        if ($controlador === 'personal') {
            $mapeo = [
                'agrega' => 'personal.panel.agrega',
                'jubilacion' => 'personal.panel.jubilacion',
                'jubilados' => 'personal.panel.jubilados',
                'index' => 'personal.panel.index',
            ];

            if (isset($mapeo[$accion]) && \Illuminate\Support\Facades\Route::has($mapeo[$accion])) {
                return route($mapeo[$accion]);
            }
        }

        return null;
    };
@endphp

<section class="contenedor" style="max-width: 900px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">
    <header style="margin-bottom: 24px;">
        <h1 class="h2r patria" style="color: var(--neutro900); font-size: 1.75rem; margin: 0 0 4px;">Personal</h1>
        <p style="color: var(--neutro600); font-size: 1.05rem; margin: 0;">Seleccione la opción que desea consultar.</p>
    </header>

    @if (!empty($mensajeExito))
        <div role="status" class="alert alert-success" style="margin-bottom: 12px;">{{ $mensajeExito }}</div>
    @endif

    @if (!empty($mensaje))
        <div role="status" class="alert alert-warning" style="margin-bottom: 18px;">{{ $mensaje }}</div>
    @endif

    <div class="buscar01" style="margin-bottom: 24px; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid var(--neutro400);">
                    <th style="padding: 12px 16px;">Opción</th>
                    <th style="padding: 12px 16px; text-align: center; width: 160px;">Acceso</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($opcionesMenu as $sublista)
                    @php
                        $url = $obtenerRuta($sublista);
                        $nombre = $sublista[1] ?? 'Opción';
                    @endphp
                    <tr style="border-bottom: 1px solid var(--neutro300);">
                        <td style="padding: 14px 16px;">{{ $nombre }}</td>
                        <td style="padding: 14px 16px; text-align: center;">
                            @if (!empty($url))
                                <a href="{{ $url }}" class="boton__primario" style="padding: 6px 16px; font-size: .9rem; text-decoration: none; display: inline-block;">Acceder</a>
                            @else
                                <span class="text-muted" aria-disabled="true" title="La ruta de este módulo todavía no está migrada">En migración</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
