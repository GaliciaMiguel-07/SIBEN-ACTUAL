
{{-- Menú del módulo de Alumnos --}}
@extends('layouts.siben.siben')

@section('content')
@php
    $opcionesMenu = $opcMenu ?? [];
    $mensaje = $mensaje ?? '';

    $obtenerRuta = function ($sublista) {
        $controlador = $sublista[0] ?? '';
        $accion = $sublista[2] ?? '';

        if ($controlador === 'consultasHistorico') {
            if ($accion === 'busquedaGlobal/p') {
                return route('consultas_historico.usuario.individual.form');
            }

            if ($accion === 'busquedaGlobal/g') {
                return route('consultas_historico.usuario.global.form');
            }
        }

        if ($controlador === 'alumnos') {
            if ($accion === 'listaEscuelas') {
                return route('alumnos.listaEscuelas');
            }

            if ($accion === 'opcionesAltaAlumno') {
                return route('alumnos.opcionesAltaAlumno');
            }

            if ($accion === 'opcionCambioEscuela') {
                return route('alumnos.opcionCambioEscuela');
            }

            if (preg_match(
                '/^busquedaAlumnos\/([iet])\/(\d+)$/',
                $accion,
                $coincidencias
            )) {
                return route('alumnos.busquedaAlumnos', [
                    'opc' => $coincidencias[1],
                    'id_escuela' => $coincidencias[2],
                ]);
            }
        }

        return null;
    };
@endphp

<section class="contenedor" style="max-width: 900px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">

    <header style="margin-bottom: 24px;">
        <h1 class="h2r patria" style="color: var(--neutro900); font-size: 1.75rem; margin: 0 0 4px;">
            Alumnos
        </h1>
        <p style="color: var(--neutro600); font-size: 1.05rem; margin: 0;">
            Selecciona la opción que deseas consultar.
        </p>
    </header>

    {{-- Mensaje del controlador --}}
    @if (!empty($mensaje))
        <div role="status" class="alert alert-warning" style="margin-bottom: 18px;">
            {!! $mensaje !!}
        </div>
    @endif

    <div class="buscar01" style="margin-bottom: 24px; overflow-x: auto;">

        @if (count($opcionesMenu))

            @foreach ($opcionesMenu as $seccion => $opciones)
                <div style="margin-bottom: 25px;">

                    <h2 class="h2r" style="
                        color: var(--neutro900);
                        font-size: 1.25rem;
                        margin: 0 0 10px;
                    ">
                        {{ $seccion }}
                    </h2>

                    <table style="width: 100%; border-collapse: collapse; text-align: left;">

                        <thead>
                            <tr style="border-bottom: 2px solid var(--neutro400);">
                                <th scope="col" style="padding: 12px 16px;">
                                    Opción
                                </th>
                                <th scope="col" style="padding: 12px 16px; text-align: center; width: 160px;">
                                    Acceso
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($opciones as $sublista)
                                @php
                                    $url = $obtenerRuta($sublista);
                                    $nombre = $sublista[1] ?? 'Opción';
                                @endphp

                                <tr style="border-bottom: 1px solid var(--neutro300);">

                                    <td style="padding: 14px 16px;">
                                        {{ $nombre }}
                                    </td>

                                    <td style="padding: 14px 16px; text-align: center;">

                                        @if (!empty($url))
                                            <a
                                                href="{{ $url }}"
                                                class="boton__primario"
                                                style="
                                                    padding: 6px 16px;
                                                    font-size: .9rem;
                                                    text-decoration: none;
                                                    display: inline-block;
                                                "
                                            >
                                                Acceder
                                            </a>
                                        @else
                                            <span
                                                class="text-muted"
                                                aria-disabled="true"
                                                title="La ruta de este módulo todavía no está migrada"
                                            >
                                                En migración
                                            </span>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            @endforeach

        @else

            <p style="padding: 20px; text-align: center; color: var(--neutro600);">
                No hay opciones disponibles para este módulo y nivel.
            </p>

        @endif

    </div>

</section>
@endsection

@push('scripts')
    <script src="{{ asset('js/security.js') }}" defer></script>
@endpush
