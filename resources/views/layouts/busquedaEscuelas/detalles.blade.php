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
        <h2>DETALLES DE LA ESCUELA</h2>
        <hr class="red">          

        @if(isset($escuela))
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><b>{{ $escuela->escuela }}</b></h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Clave de la Escuela:</th>
                                <td>{{ $escuela->escuela_key }}</td>
                            </tr>
                            <tr>
                                <th>Entidad:</th>
                                <td>{{ $escuela->entidad_nombre ?? 'N/D' }}</td>
                            </tr>
                            <tr>
                                <th>Turno:</th>
                                <td>{{ $escuela->turno == 'm' ? 'Matutino' : ($escuela->turno == 'v' ? 'Vespertino' : 'Discontinuo / Otro') }}</td>
                            </tr>
                            <tr>
                                <th>Sostenimiento:</th>
                                <td>{{ $escuela->sostenimiento == 'p' ? 'Pública' : 'Particular' }}</td>
                            </tr>
                            <tr>
                                <th>Correo Electrónico:</th>
                                <td>{!! $escuela->correo_electronico ?: 'No registrado' !!}</td>
                            </tr>
                            <tr>
                                <th>Página Web:</th>
                                <td>
                                    @if(!empty($escuela->pagina_web))
                                        <a href="{{ $escuela->pagina_web }}" target="_blank">{{ $escuela->pagina_web }}</a>
                                    @else
                                        No registrada
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
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