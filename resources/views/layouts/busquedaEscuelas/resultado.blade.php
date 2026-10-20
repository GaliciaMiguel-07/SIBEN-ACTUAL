<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/escuelas.css') }}">
    <title>Resultados de Búsqueda — SIBEN</title>

    <x-header />

    <!-- Estilos de adaptación basados en las variables oficiales de estilos.css -->
    <style>
        .paginacion-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 35px;
            width: 100%;
        }

        /* Mapeo de la paginación con las variables oficiales de gobierno */
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

        /* Ajuste institucional para que el scrollbar sea color guinda oficial (var(--principal)) */
        .deslizable--scroll {
            scrollbar-color: var(--principal) var(--neutro200);
        }

        .deslizable--scroll::-webkit-scrollbar {
            height: 8px;
        }

        .deslizable--scroll::-webkit-scrollbar-track {
            background: var(--neutro200);
            border-radius: 4px;
        }

        .deslizable--scroll::-webkit-scrollbar-thumb {
            background-color: var(--principal);
            border-radius: 4px;
        }
    </style>
</head>
<body>

<!-- Migas de pan oficiales SND -->
<div class="contenedor mt--24">
    <nav class="ruta__cont" aria-label="Ruta de navegación">
        <ol class="ruta__lista">
            <li class="ruta__li">
                <a class="ruta__a" href="{{ url('/') }}" aria-label="Inicio">
                    <i class="snd__icono snd-home icono--24" aria-hidden="true"></i>
                </a>
            </li>
            <li class="ruta__li">
                <a class="ruta__a" href="{{ route('siben.busqueda_escuelas.busqueda') }}">
                    Búsqueda
                </a>
            </li>
            <li class="ruta__li" aria-current="page">
                Resultados
            </li>
        </ol>
    </nav>
</div>

<div class="contenedor" style="padding-top: 20px; padding-bottom: 60px;">       
    <section>
        <h2 class="h2r patria">RESULTADOS DE LA BÚSQUEDA</h2>
        <hr class="red divider">          

        @php
            $esBusquedaNacional = empty(request()->input('Escuela.id_entidad'));
            $j = 0;
        @endphp

        <!-- Retícula oficial de 12 columnas para alinear la información superior -->
        <div class="reticulaGrid__12 gap--16 mb--24">
            <div class="columna__12--md columna__12--xs">
                <div class="sh1b" style="font-weight: 600; color: var(--neutro800);">
                    {{ $esBusquedaNacional ? 'Búsqueda Nacional' : 'Búsqueda Estatal' }}
                </div>
                <div class="sd mt--8" style="color: var(--neutro600); margin-bottom: 0;">
                    Número total de escuelas: {{ $escuelas->total() }}
                </div>
            </div>
        </div>

        <!-- Contenedor deslizable oficial con scrollbar en color guinda institucional -->
        <div class="deslizable--scroll">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: var(--neutro100); border-radius: 8px; border: 1px solid var(--neutro400);">
                <thead>
                    <tr style="background-color: var(--principal); color: var(--neutro100);">
                        <th style="padding: 12px 16px; text-align: left;">Entidad</th>
                        <th style="padding: 12px 16px; text-align: left;">Clave</th>
                        <th style="padding: 12px 16px; text-align: left;">Nombre de la Escuela</th>
                        <th style="padding: 12px 16px; text-align: left;">Correo</th>
                        <th style="padding: 12px 16px; text-align: left;">Sitio Web</th>
                        <th style="padding: 12px 16px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if($escuelas->isEmpty())
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: var(--neutro600);">
                                No se encontraron escuelas con los criterios seleccionados.
                            </td>
                        </tr>
                    @else
                        @foreach ($escuelas as $infoEscuela)
                            @php
                                $j++;
                                $urlDetalle = route('siben.busqueda_escuelas.detalles', $infoEscuela->id);
                                $nombreEntidad = $infoEscuela->entidad_nombre 
                                    ?? optional($infoEscuela->entidad)->nombre 
                                    ?? optional($infoEscuela->entidadRelacion)->entidad 
                                    ?? 'Entidad ' . ($infoEscuela->id_entidad ?? '');
                                $claveEscuela = $infoEscuela->escuela_key ?? $infoEscuela->clave ?? 'N/D';
                                $nombreEscuela = $infoEscuela->escuela ?? $infoEscuela->nombre ?? 'Sin nombre';
                                $correo = $infoEscuela->correo_electronico ?: '-';
                                $web = $infoEscuela->pagina_web ?: '-';
                            @endphp
                            <tr style="border-top: 1px solid var(--neutro400);">
                                <td style="padding: 14px 16px;"><strong>{{ $j }}.</strong> {{ $nombreEntidad }}</td>
                                <td style="padding: 14px 16px;">{{ $claveEscuela }}</td>
                                <td style="padding: 14px 16px;"><strong>{{ $nombreEscuela }}</strong></td>
                                <td style="padding: 14px 16px;">{{ $correo }}</td>
                                <td style="padding: 14px 16px;">
                                    @if($infoEscuela->pagina_web)
                                        <a href="{{ $infoEscuela->pagina_web }}" target="_blank" rel="noopener noreferrer">{{ $web }}</a>
                                    @else
                                        {{ $web }}
                                    @endif
                                </td>
                                <td style="padding: 14px 16px; text-align: center;">
                                    <a href="{{ $urlDetalle }}" class="boton__secundario" style="padding: 4px 12px; font-size: 12px; min-height: unset;">
                                        Más
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Paginación adaptada con las variables oficiales -->
        <div class="paginacion-container">
            {{ $escuelas->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
        
        <div class="mt--32">
            <a href="{{ route('siben.busqueda_escuelas.busqueda') }}" class="boton__secundario">Nueva Búsqueda</a>
        </div>
    </section>
</div>

<x-footer />
</body>
</html>