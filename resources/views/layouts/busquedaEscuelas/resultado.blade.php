<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Resultados de Búsqueda — SIBEN</title>
    
    <link href="{{ url('/images/favicon.ico') }}" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
    
    <x-header />
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

<div class="container" style="margin-top: 40px;">       
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

        <div id="resultados" style="width:100%;height:450px;overflow:auto;margin:auto;">
            @if($escuelas->isEmpty())
                <div class="alert alert-warning" style="padding: 15px; margin-top: 20px;">
                    No se encontraron escuelas con los criterios seleccionados.
                </div>
            @else
                <table class="table table-bordered">
                    @foreach ($escuelas as $infoEscuela)
                        @php
                            $j++;
                            $urlDetalle = route('siben.busqueda_escuelas.detalles', $infoEscuela->id);
                            // Intentar obtener el nombre de la entidad de varias formas comunes
                            $nombreEntidad = $infoEscuela->entidad_nombre 
                                ?? optional($infoEscuela->entidad)->nombre 
                                ?? optional($infoEscuela->entidadRelacion)->entidad 
                                ?? 'Entidad ' . ($infoEscuela->id_entidad ?? '');
                        @endphp

                        <tr style="background-color:#efefef">
                            <td>
                                {{ $j }}.-&nbsp;{{ $nombreEntidad }}
                            </td>
                            <td>
                                {{ $infoEscuela->escuela_key ?? $infoEscuela->clave ?? 'N/D' }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ $infoEscuela->escuela ?? $infoEscuela->nombre ?? 'Sin nombre' }}</strong>
                            </td>
                            <td>
                                <a href="{{ $urlDetalle }}" title="Detalles de la escuela">
                                    <img src="{{ asset('img/detalle.gif') }}" alt="Detalles de la escuela" width="22" height="20">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Correo: {!! $infoEscuela->correo_electronico ?: '&nbsp;' !!}
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                Web: {!! $infoEscuela->pagina_web ?: '&nbsp;' !!}
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>

        <br/>
        <div class="text-center">
            {{ $escuelas->withQueryString()->links() }}
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