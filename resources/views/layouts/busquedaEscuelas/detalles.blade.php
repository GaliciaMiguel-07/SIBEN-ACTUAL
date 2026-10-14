<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Detalles de Escuela — SIBEN</title>
    
    <link href="{{ url('/images/favicon.ico') }}" rel="shortcut icon">
    
    <!-- Estilos integrados del sistema de diseño (sin dependencias del CSS antiguo) -->
    <style>
        body {
            font-family: 'Noto Sans', system-ui, -apple-system, sans-serif;
            color: #161a1d;
            background: #ffffff;
            margin: 0;
        }
        .siben-panel {
            background: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .siben-panel-heading {
            background-color: #611232;
            color: #ffffff;
            padding: 14px 16px;
            font-size: 16px;
            font-weight: 600;
        }
        .siben-panel-body {
            padding: 20px;
        }
        .siben-well {
            background: #f9f9f9;
            border: 1px solid #dddddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .tabla-siben {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 14px;
        }
        .tabla-siben th {
            background-color: #f9f9f9;
            color: #161a1d;
            font-weight: 600;
            padding: 12px 14px;
            border-bottom: 1px solid #dddddd;
            border-top: 1px solid #dddddd;
            vertical-align: middle;
            text-align: left;
        }
        .tabla-siben td {
            padding: 12px 14px;
            border-bottom: 1px solid #eeeeee;
            vertical-align: middle;
            color: #161a1d;
        }
        .tabla-siben tr:last-child td {
            border-bottom: none;
        }
        .thumbnail {
            border: 1px solid #dddddd;
            border-radius: 8px;
            padding: 4px;
            background: #fff;
        }
        .btn-siben {
            display: inline-block;
            padding: 8px 16px;
            background: #f2f2f2;
            border: 1px solid #dddddd;
            border-radius: 4px;
            color: #161a1d;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-siben:hover {
            background: #e6e6e6;
            text-decoration: none;
            color: #161a1d;
        }
    </style>

    <x-header />
</head>
<body>

<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">       
    <section class="col-md-12">
        <h2>DETALLES DE LA ESCUELA NORMAL SELECCIONADA</h2>
        <hr class="red">          

        @if(isset($escuela))
            
            <!-- ENCABEZADO BÁSICO -->
            <div class="siben-well">
                <h4 style="margin-top: 0; color: #611232;"><b>{{ $escuela->escuela }}</b></h4>
                <p style="margin-bottom: 0; line-height: 1.6;">
                    <b>Entidad:</b> {{ is_object($entidad) ? ($entidad->entidad ?? $entidad->nombre) : (is_object($localidad) && isset($localidad->entidad) ? $localidad->entidad : $entidad) }}<br>
                    <b>Sostenimiento:</b> {{ $sostenimiento }}<br>
                    <b>Turno:</b> {{ $turno }}
                </p>
            </div>

            <!-- DOMICILIO Y CONTACTO -->
            @if(!empty($domicilioData))
                <div class="siben-panel">
                    <div class="siben-panel-heading">Datos de Ubicación y Contacto</div>
                    <div class="siben-panel-body" style="padding: 0;">
                        <table class="tabla-siben">
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
                <div class="siben-panel">
                    <div class="siben-panel-heading">Directivos</div>
                    <div class="siben-panel-body" style="padding: 0;">
                        <table class="tabla-siben">
                            <tbody>
                                @foreach($personal as $persona)
                                    <tr>
                                        <td>{{ mb_strtoupper($persona->nombre ?? '') }}</td>
                                        <td>{{ mb_strtoupper($persona->apellido_paterno ?? '') }}</td>
                                        <td>{{ mb_strtoupper($persona->apellido_materno ?? '') }}</td>
                                        <td><b>{{ $funciones[$persona->id_funcion] ?? 'Sin función especificada' }}</b></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- LICENCIATURAS -->
            @if(isset($licenciaturas) && count($licenciaturas) > 0)
                <div class="siben-panel">
                    <div class="siben-panel-heading">Licenciaturas</div>
                    <div class="siben-panel-body" style="padding: 0;">
                        <table class="tabla-siben">
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
                </div>
            @endif

            <!-- IMÁGENES -->
            <div class="siben-panel">
                <div class="siben-panel-heading">Imágenes</div>
                <div class="siben-panel-body">
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
                        <p class="text-muted" style="margin: 0;">No hay imágenes disponibles para esta escuela.</p>
                    @endif
                </div>
            </div>

        @else
            <div class="alert alert-danger" style="padding: 15px; background: #fae5e5; color: #9b0f15; border-radius: 8px;">
                No se encontró información para esta escuela.
            </div>
        @endif

        <br>
        <a href="{{ route('siben.busqueda_escuelas.busqueda') }}" class="btn-siben">Regresar a Búsqueda</a>
        <br/><br/><br/>
    </section>
</div>

<x-footer />

</body>
</html>