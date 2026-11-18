
@extends('layouts.siben.siben')

@section('titulo', 'Infraestructura de la Escuela')

@section('content')
<div class="contenedor" style="flex:1; padding:32px 16px; max-width:1000px; margin:0 auto; width:100%; box-sizing:border-box;">

    <div style="border-bottom:1px solid var(--neutro400); padding-bottom:12px; margin-bottom:24px;">
        <h2 class="patria" style="font-size:1.8rem; margin:0; color:var(--neutro900);">
            Infraestructura de la Escuela
        </h2>
        <p style="margin:8px 0 0;">{{ $escuela->escuela }}</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (!empty($mensaje))
        <div class="alert alert-warning">{{ $mensaje }}</div>
    @endif

    @if (!$estructura)
        <div class="alert alert-info">
            Esta escuela todavía no tiene información de infraestructura registrada.
        </div>
    @else

        @php
            $valor = fn($campo, $default = 0) => $estructura->$campo ?? $default;

            $etiqueta = function ($campo, $opciones) use ($estructura) {
                $id = $estructura->$campo ?? null;
                return $opciones[$id] ?? 'Sin información';
            };

            $secciones = [
                'Espacios e instalaciones' => [
                    'aulas' => 'Aulas',
                    'bibliotecas' => 'Bibliotecas',
                    'aulas_computo' => 'Aulas de cómputo',
                    'auditorios' => 'Auditorios',
                    'idiomas' => 'Aulas de idiomas',
                    'albercas' => 'Albercas',
                    'gimnasios' => 'Gimnasios',
                    'canchas' => 'Canchas',
                    'pistas' => 'Pistas',
                    'salas_maestros' => 'Salas de maestros',
                    'cubiculos_maestros' => 'Cubículos de maestros',
                    'salas_multiples' => 'Salas múltiples',
                    'laboratorios' => 'Laboratorios',
                    'talleres' => 'Talleres',
                ],
                'Equipos de cómputo' => [
                    'computo_alumnos' => 'Para alumnos',
                    'computo_docentes' => 'Para docentes',
                    'computo_administrativo' => 'Para administrativos',
                ],
                'Equipos con acceso a Internet' => [
                    'internet_alumnos' => 'Para alumnos',
                    'internet_docentes' => 'Para docentes',
                    'internet_administrativo' => 'Para administrativos',
                ],
                'Accesibilidad y recursos' => [
                    'd_rampas' => 'Rampas',
                    'd_elevadores' => 'Elevadores',
                    'd_computadoras' => 'Computadoras adaptadas',
                    'd_banios' => 'Baños adaptados',
                    'd_libros' => 'Libros accesibles',
                ],
            ];

            $opcionesSiNo = ['s' => 'Sí', 'n' => 'No'];
        @endphp

        <section style="margin-bottom:24px;">
            <h3>Características del inmueble</h3>
            <table style="width:100%; border-collapse:collapse;">
                <tbody>
                    <tr style="border-bottom:1px solid var(--neutro300);">
                        <th style="padding:10px; text-align:left;">Propiedad</th>
                        <td style="padding:10px;">{{ $etiqueta('propiedad', $tiposPropiedad->all()) }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--neutro300);">
                        <th style="padding:10px; text-align:left;">Tipo de construcción</th>
                        <td style="padding:10px;">{{ $etiqueta('estructura', $tiposEstructura->all()) }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        @foreach ($secciones as $titulo => $campos)
            <section style="margin-bottom:24px;">
                <h3>{{ $titulo }}</h3>
                <table style="width:100%; border-collapse:collapse;">
                    <tbody>
                        @foreach ($campos as $campo => $nombre)
                            <tr style="border-bottom:1px solid var(--neutro300);">
                                <th style="padding:10px; text-align:left;">{{ $nombre }}</th>
                                <td style="padding:10px;">{{ $valor($campo) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endforeach

        <section style="margin-bottom:24px;">
            <h3>Servicios de conectividad y recursos educativos</h3>
            <table style="width:100%; border-collapse:collapse;">
                <tbody>
                    @foreach ([
                        'internet' => 'Cuenta con Internet',
                        'internet_banda_ancha' => 'Tipo de conexión',
                        'restriccion_internet' => 'Restricción de Internet',
                        'siden' => 'SIDEN',
                        'edusat' => 'EDUSAT',
                        'd_software' => 'Software accesible',
                        'd_guias' => 'Guías accesibles',
                    ] as $campo => $nombre)
                        <tr style="border-bottom:1px solid var(--neutro300);">
                            <th style="padding:10px; text-align:left;">{{ $nombre }}</th>
                            <td style="padding:10px;">
                                @if (in_array($campo, ['internet', 'restriccion_internet', 'siden', 'edusat', 'd_software', 'd_guias']))
                                    {{ $etiqueta($campo, $opcionesSiNo) }}
                                @elseif ($campo === 'internet_banda_ancha')
                                    {{ $etiqueta($campo, $bandaAncha) }}
                                @else
                                    {{ $valor($campo) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif

    <div style="display:flex; flex-wrap:wrap; gap:12px; justify-content:center; margin-top:24px;">

        @if ($puedeEditar)
            <a href="{{ route('siben.editaEstructura') }}"
               class="boton__primario"
               style="padding:8px 18px; text-decoration:none;">
                Editar infraestructura
            </a>
        @endif

        @if ($puedeSubirFotos)
            <a href="{{ route('siben.imagenEscuela') }}"
               class="boton__primario"
               style="padding:8px 18px; text-decoration:none;">
                Subir fotografías
            </a>
        @endif

        <a href="{{ route('siben.menu_datos') }}"
           class="boton__secundario"
           style="padding:8px 18px; text-decoration:none;">
            Regresar
        </a>
    </div>
</div>
@endsection
