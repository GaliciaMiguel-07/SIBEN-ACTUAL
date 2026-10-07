<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Detalles de Escuela — SIBEN</title>
    
    <link href="{{ url('/images/favicon.ico') }}" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
    
    <x-header />
</head>
<body>

<div class="container" style="margin-top: 40px;">       
    <section class="col-md-12">
        <h2>DETALLES DE LA ESCUELA NORMAL SELECCIONADA</h2>
        <hr class="red">          

        @if(isset($escuela))
            
            <!-- ENCABEZADO BÁSICO -->
            <div class="well well-sm">
                <h4><b>{{ $escuela->escuela }}</b></h4>
                <p>
                    <b>Entidad:</b> {{ is_object($entidad) ? ($entidad->entidad ?? $entidad->nombre) : (is_object($localidad) && isset($localidad->entidad) ? $localidad->entidad : $entidad) }}<br>
                    <b>Sostenimiento:</b> {{ $sostenimiento }}<br>
                    <b>Turno:</b> {{ $turno }}
                </p>
            </div>

            <!-- DOMICILIO Y CONTACTO -->
            @if(!empty($domicilioData))
                <div class="panel panel-default">
                    <div class="panel-heading"><b>Datos de Ubicación y Contacto</b></div>
                    <div class="panel-body">
                        <table class="table table-bordered table-condensed">
                            <tbody>
                                <tr>
                                    <th style="width: 20%;">Vialidad:</th>
                                    <td style="width: 30%;">{{ $domicilioData['vialidad'] }}</td>
                                    <th style="width: 20%;">Núm Ext 1:</th>
                                    <td style="width: 30%;">{{ $domicilioData['num_ext1'] }}</td>
                                </tr>
                                <tr>
                                    <th>Núm Ext 2:</th>
                                    <td>{{ $domicilioData['num_ext2'] }}</td>
                                    <th>Núm Int:</th>
                                    <td>{{ $domicilioData['num_int'] }}</td>
                                </tr>
                                <tr>
                                    <th>Asentamiento Humano:</th>
                                    <td>{{ $domicilioData['asentamiento'] }}</td>
                                    <th>Localidad:</th>
                                    <td>{{ is_object($localidad) ? ($localidad->localidad ?? $localidad->nombre ?? '') : $localidad }}</td>
                                </tr>
                                <tr>
                                    <th>Municipio:</th>
                                    <td>{{ is_object($municipio) ? ($municipio->municipio ?? $municipio->nombre ?? '') : (is_object($localidad) && isset($localidad->municipio) ? $localidad->municipio : $municipio) }}</td>
                                    <th>Entidad:</th>
                                    <td>{{ is_object($entidad) ? ($entidad->entidad ?? $entidad->nombre ?? '') : (is_object($localidad) && isset($localidad->entidad) ? $localidad->entidad : $entidad) }}</td>
                                </tr>
                                <tr>
                                    <th>Referencia 1:</th>
                                    <td>{{ $domicilioData['referencia1'] }}</td>
                                    <th>Referencia 2:</th>
                                    <td>{{ $domicilioData['referencia2'] }}</td>
                                </tr>
                                <tr>
                                    <th>Referencia 3:</th>
                                    <td colspan="3">{{ $domicilioData['referencia3'] }}</td>
                                </tr>
                                <tr>
                                    <th>Complementaria:</th>
                                    <td colspan="3">{{ $domicilioData['complementaria'] }}</td>
                                </tr>
                                <tr>
                                    <th>Teléfono # 1:</th>
                                    <td>{{ $domicilioData['telefono1'] }}</td>
                                    <th>Teléfono # 2:</th>
                                    <td>{{ $domicilioData['telefono2'] }}</td>
                                </tr>
                                <tr>
                                    <th>Teléfono # 3:</th>
                                    <td>{{ $domicilioData['telefono3'] }}</td>
                                    <th>LADA:</th>
                                    <td>{{ $domicilioData['lada'] }}</td>
                                </tr>
                                <tr>
                                    <th>Web:</th>
                                    <td>
                                        @if(!empty($escuela->pagina_web))
                                            <a href="{{ str_starts_with($escuela->pagina_web, 'http') ? $escuela->pagina_web : 'http://'.$escuela->pagina_web }}" target="_blank">
                                                {{ $escuela->pagina_web }}
                                            </a>
                                        @endif
                                    </td>
                                    <th>Fax:</th>
                                    <td>{{ $domicilioData['fax'] }}</td>
                                </tr>
                                <tr>
                                    <th>Correo Electrónico:</th>
                                    <td>{{ $escuela->correo_electronico ?? '' }}</td>
                                    <th>C.P.:</th>
                                    <td>{{ $domicilioData['cp'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- DIRECTIVOS -->
            @if(isset($personal) && count($personal) > 0)
                <div class="panel panel-default">
                    <div class="panel-heading"><b>Directivos</b></div>
                    <table class="table table-striped table-condensed">
                        <tbody>
                            @foreach($personal as $persona)
                                <tr>
                                    <td>{{ mb_strtoupper($persona->nombre ?? '') }}</td>
                                    <td>{{ mb_strtoupper($persona->apellido_paterno ?? '') }}</td>
                                    <td>{{ mb_strtoupper($persona->apellido_materno ?? '') }}</td>
                                    <td>{{ $funciones[$persona->id_funcion] ?? 'Sin función especificada' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- LICENCIATURAS -->
            @if(isset($licenciaturas) && count($licenciaturas) > 0)
                <div class="panel panel-default">
                    <div class="panel-heading"><b>Licenciaturas</b></div>
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>Licenciaturas</th>
                                <th>Especialidad</th>
                                <th>Modalidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($licenciaturas as $lic)
                                <tr>
                                    <td>{{ $lic->licenciatura ?? '' }} {{ $lic->plan ?? '' }}</td>
                                    <td>{{ $lic->especialidad ?? 'n/a' }}</td>
                                    <td>
                                        {{ strtolower($lic->modalidad ?? '') === 'e' ? 'Escolarizada' : (strtolower($lic->modalidad ?? '') === 'm' ? 'Mixta' : ($lic->modalidad ?? 'Escolarizada')) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- IMÁGENES -->
            <div class="panel panel-default">
                <div class="panel-heading"><b>Imágenes</b></div>
                <div class="panel-body">
                    @if(isset($imagenes) && count($imagenes) > 0)
                        <div class="row">
                            @foreach($imagenes as $img)
                                <div class="col-xs-6 col-md-4 col-lg-3" style="margin-bottom: 15px;">
                                    <div class="thumbnail">
                                        <img src="{{ $img }}" alt="Fotografía {{ $escuela->escuela }}" class="img-responsive">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No hay imágenes disponibles para esta escuela.</p>
                    @endif
                </div>
            </div>

        @else
            <div class="alert alert-danger">
                No se encontró información para esta escuela.
            </div>
        @endif

        <br>
        <a href="{{ route('siben.busqueda_escuelas.busqueda') }}" class="btn btn-default">Regresar a Búsqueda</a>
        <br/><br/><br/>
    </section>
</div>

<x-footer />

</body>
</html>