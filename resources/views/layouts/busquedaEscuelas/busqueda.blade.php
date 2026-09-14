<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
<x-header />

</head>
<body>
    <div class="container">
<ol class="breadcrumb">
  <li><a href="{{ url('/') }}"><i class="icon icon-home"></i></a></li>
  <li><a href="{{ url('/') }}">Inicio</a></li>
  <li class="active">Búsqueda</li>
</ol>
</div>

<div class="container">       
  <section class="col-md-12">
    <h2>BÚSQUEDA DE ESCUELAS</h2>
    <hr class="red">

    <h3>Seleccione las características de las escuelas que desea buscar</h3>
<br><br>
<table class="table">				
<tr>
<td>Escuela:</td>
<td style="width:700px;">
<input class="form-control" placeholder="" type="text">
</td>
<td></td>
</tr>
<tr>
<td>Entidad:</td>
<td >
    <select class="form-control">
  <option>1</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select>
</td>
<td></td>
</tr>
<tr>
<td>Sostenimiento:</td>
<td>
    <select class="form-control">
  <option>1</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select>
</td>
<td></td>
</tr>
<tr>
<td>Turno:</td>
<td>
    <select class="form-control">
  <option>1</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select>
</td>
<td></td>
<tr>
<td>Licenciatura:</td>
<td>
    <select class="form-control">
  <option>1</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select>
</td>
<td></td>
</tr>
<tr>
<td>Que cuente con:
</td>
<td> Correo electrónico
</td>
<td>
    <div class="checkbox">
  <label>
    <input type="checkbox" value="opcion-01">
  </label>
</div>
</td>
</tr>
<tr>
<td></td>
<td> Página Web
</td>
<td>
        <div class="checkbox">
  <label>
    <input type="checkbox" value="opcion-01">
  </label>
</div>
</td>
</tr>
</table>
    <button class="btn btn-primary pull-right btn-icon-right" type="button">Buscar</button>
<br/>
<br/>
<br/>
<x-footer />
</body>
</html>