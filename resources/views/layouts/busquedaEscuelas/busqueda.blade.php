<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Búsqueda de Escuelas - SIBEN</title>
    
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

     <div class="mexico__contenedor" style="max-width: 1200px; margin: 0 auto; padding: 0 20px 60px 20px;">   
        <section class="col-md-12">
            <h2>BÚSQUEDA DE ESCUELAS</h2>
            <hr class="red">

            <h3>Seleccione las características de las escuelas que desea buscar</h3>
            <br><br>

            <form action="{{ route('siben.busqueda_escuelas.resultado') }}" method="POST">
                @csrf
                <table class="table">				
                    <tr>
                        <td>Escuela:</td>
                        <td style="width:700px;">
                            <input class="form-control" name="Escuela[escuela]" placeholder="Nombre de la escuela" type="text" value="{{ old('Escuela.escuela') }}">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Entidad:</td>
                        <td>
                            <select class="form-control" name="Escuela[id_entidad]">
                                <option value="">-- Seleccione una entidad --</option>
                                @foreach($entidades as $id => $entidad)
                                    <option value="{{ $id }}" {{ old('Escuela.id_entidad') == $id ? 'selected' : '' }}>
                                        {{ $entidad }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Sostenimiento:</td>
                        <td>
                            <select class="form-control" name="Escuela[sostenimiento]">
                                <option value="">-- Seleccione el sostenimiento --</option>
                                <option value="p" {{ old('Escuela.sostenimiento') == 'p' ? 'selected' : '' }}>Pública</option>
                                <option value="q" {{ old('Escuela.sostenimiento') == 'q' ? 'selected' : '' }}>Particular</option>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Turno:</td>
                        <td>
                            <select class="form-control" name="Escuela[turno]">
                                <option value="">-- Seleccione el turno --</option>
                                @foreach($turno as $key => $label)
                                    <option value="{{ $key }}" {{ old('Escuela.turno') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Licenciatura:</td>
                        <td>
                            <select class="form-control" name="EscuelaLicenciatura[id_licenciatura]">
                                <option value="">-- Seleccione una licenciatura --</option>
                                @foreach($licenciaturas as $id => $licenciatura)
                                    <option value="{{ $id }}" {{ old('EscuelaLicenciatura.id_licenciatura') == $id ? 'selected' : '' }}>
                                        {{ $licenciatura }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Que cuente con:</td>
                        <td>Correo electrónico</td>
                        <td>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="Correo[op1]" value="1" {{ old('Correo.op1') ? 'checked' : '' }}>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Página Web</td>
                        <td>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="Pagina[op2]" value="1" {{ old('Pagina.op2') ? 'checked' : '' }}>
                                </label>
                            </div>
                        </td>
                    </tr>
                </table>

                <button class="btn btn-primary pull-right btn-icon-right" type="submit">Buscar</button>
            </form>
            <br/><br/><br/>
        </section>
    </div>

    <x-footer />
</body>
</html>