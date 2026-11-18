
@extends('layouts.siben.siben')

@section('content')
<div class="contenedor">
    <h2>{{ $title }}</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table">
        <tbody>
            <tr>
                <th>Nombre de la escuela</th>
                <td>{{ $escuela->escuela }}</td>
            </tr>
            <tr>
                <th>Teléfono principal</th>
                <td>{{ $escuela->telefono1 ?: 'No registrado' }}</td>
            </tr>
            <tr>
                <th>Teléfono alternativo 1</th>
                <td>{{ $escuela->telefono2 ?: 'No registrado' }}</td>
            </tr>
            <tr>
                <th>Teléfono alternativo 2</th>
                <td>{{ $escuela->telefono3 ?: 'No registrado' }}</td>
            </tr>
            <tr>
                <th>Fax</th>
                <td>{{ $escuela->fax ?: 'No registrado' }}</td>
            </tr>
            <tr>
                <th>Lada</th>
                <td>{{ $escuela->lada ?: 'No registrada' }}</td>
            </tr>
            <tr>
                <th>Correo electrónico</th>
                <td>{{ $escuela->correo_electronico ?: 'No registrado' }}</td>
            </tr>
            <tr>
                <th>Página web</th>
                <td>{{ $escuela->pagina_web ?: 'No registrada' }}</td>
            </tr>
            <tr>
                <th>Turno</th>
                <td>{{ $turnos[$escuela->turno] ?? 'No registrado' }}</td>
            </tr>
            <tr>
                <th>Sostenimiento</th>
                <td>{{ $sostenimientos[$escuela->sostenimiento] ?? 'No registrado' }}</td>
            </tr>
            <tr>
                <th>Realiza investigación</th>
                <td>{{ $investigacion[$escuela->investigacion] ?? 'No registrado' }}</td>
            </tr>
            <tr>
                <th>Fecha de autorización</th>
                <td>{{ $escuela->fecha_autorizacion }}</td>
            </tr>
            <tr>
                <th>Fecha de inicio de actividades</th>
                <td>{{ $escuela->fecha_inicio }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        @if ($nivel === 3)
            <a href="{{ route('siben.editaAdicionales') }}" class="boton__primario">
                Editar información
            </a>
        @endif

        <a href="{{ route('siben.menu_datos') }}" class="boton__secundario">
            Regresar
        </a>
    </div>
</div>
@endsection
