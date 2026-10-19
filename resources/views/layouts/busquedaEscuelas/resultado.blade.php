<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Resultados de Búsqueda — SIBEN</title>

    <x-header />

    <!-- Estilos integrados para la tabla y la paginación oficial -->
    <style>
        .tabla-siben {
            width: 100%;
            background: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            border-collapse: separate;
            border-spacing: 0;
            font-family: 'Noto Sans', system-ui, -apple-system, sans-serif;
            margin-top: 20px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            overflow: hidden;
        }
        .tabla-siben th {
            background-color: #611232 !important; /* Color guinda oficial */
            color: #ffffff !important;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
            text-align: left;
            border: none;
            white-space: nowrap;
        }
        .tabla-siben td {
            padding: 14px 16px;
            font-size: 14px;
            line-height: 20px;
            color: #161a1d;
            border-top: 1px solid #eeeeee;
            vertical-align: middle;
        }
        .tabla-siben tbody tr:hover {
            background-color: #f9f9f9;
        }
        .btn-accion-mas {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            padding: 4px 12px;
            background: #f2f2f2;
            border: 1px solid #dddddd;
            border-radius: 4px;
            color: #611232;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-accion-mas:hover {
            background: #e6e6e6;
            color: #611232;
            text-decoration: none;
        }

        /* ---------- Paginación Oficial Estilo Design System ---------- */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 35px;
            width: 100%;
        }

        .pagination {
            display: flex;
            padding-left: 0;
            list-style: none;
            gap: 6px;
            align-items: center;
            margin: 0;
        }

        .pagination .page-item .page-link {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            padding: 0;
            font-family: 'Noto Sans', system-ui, sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #161a1d;
            background-color: transparent;
            border: none;
            border-radius: 50% !important; /* Círculo perfecto */
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination .page-item .page-link:hover {
            background-color: rgba(97, 18, 50, 0.08);
            color: #611232;
        }

        /* Página ACTIVA: Círculo sólido guinda con texto blanco */
        .pagination .page-item.active .page-link {
            background-color: #611232 !important;
            color: #ffffff !important;
            border-color: #611232 !important;
            font-weight: 600;
        }

        /* Estado deshabilitado para flechas extremas */
        .pagination .page-item.disabled .page-link {
            color: #cccccc;
            background-color: transparent;
            pointer-events: none;
        }
    </style>
</head>
<body>
      <br><br>
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="{{ url('/') }}"><i class="icon icon-home"></i></a></li>
            <li><a href="{{ url('/') }}">Inicio</a></li>
            <li class="active">Búsqueda</li>
        </ol>
    </div>

 <div class="mexico__contenedor" style="max-width: 1200px; margin: 0 auto; padding: 0 20px 60px 20px; margin-top: 40px;">       
    <section class="col-md-12">
        <h2>RESULTADOS DE LA BÚSQUEDA</h2>
        <hr class="red">          

        @php
            $estilo = "font-family:verdana,arial; font-size:8pt; font-weight:bold;";
            $esBusquedaNacional = empty(request()->input('Escuela.id_entidad'));
            $j = 0;
        @endphp

        <div style="{{ $estilo }}">
            {{ $esBusquedaNacional ? 'Búsqueda Nacional' : 'Búsqueda Estatal' }}
        </div>
        
        <div style="{{ $estilo }}">
            Número total de escuelas: {{ $escuelas->total() }}
        </div>
        <br />

        <!-- Tabla rediseñada -->
        <div class="table-responsive">
            <table class="tabla-siben">
                <thead>
                    <tr>
                        <th>Entidad</th>
                        <th>Clave</th>
                        <th>Nombre de la Escuela</th>
                        <th>Correo</th>
                        <th>Sitio Web</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if($escuelas->isEmpty())
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: #5d5d5d;">
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
                            <tr>
                                <td>
                                    <strong>{{ $j }}.</strong> {{ $nombreEntidad }}
                                </td>
                                <td>
                                    {{ $claveEscuela }}
                                </td>
                                <td>
                                    <strong>{{ $nombreEscuela }}</strong>
                                </td>
                                <td>
                                    {{ $correo }}
                                </td>
                                <td>
                                    @if($infoEscuela->pagina_web)
                                        <a href="{{ $infoEscuela->pagina_web }}" target="_blank" rel="noopener noreferrer">{{ $web }}</a>
                                    @else
                                        {{ $web }}
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ $urlDetalle }}" class="btn-accion-mas" title="Detalles de la escuela">
                                        Más
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <br/>
        <!-- Paginación limpia utilizando la vista de Bootstrap 4 -->
        <div class="pagination-container">
            {{ $escuelas->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
        
        <br>
        <a href="{{ route('siben.busqueda_escuelas.busqueda') }}" class="btn btn-default">Nueva Búsqueda</a>
        <br/><br/><br/>
    </section>
</div>

<!-- Contenedor del Overlay -->
<div id="overoverlay" style="display:none;position:absolute;left:25%;top:25%;width:880px;height:348px;z-index:99;background:black;">
    <div style="width:100%;text-align:right;color:white;font-size:.7em;font-weight:900;cursor:pointer;" onclick="ocultaOverlay();">[Cerrar]</div>
    <div id="overlay" style="border:2px solid black;margin:0 auto;width:876px;height:328px;overflow:auto;z-index:100;background:white;"></div>
</div>

<x-footer />

</body>
</html>