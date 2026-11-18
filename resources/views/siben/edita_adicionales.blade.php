
@extends('layouts.siben.siben')

@section('content')
<div class="contenedor">
    <h2>Editar información adicional de la escuela</h2>

    <p><strong>Escuela:</strong> {{ $escuela->escuela }}</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los siguientes datos:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('siben.guardaEscuela') }}">
        @csrf

        <div>
            <label for="telefono1">Teléfono principal</label>
            <input
                type="text"
                id="telefono1"
                name="telefono1"
                maxlength="10"
                value="{{ old('telefono1', $escuela->telefono1) }}"
            >
        </div>

        <div>
            <label for="telefono2">Teléfono alternativo 1</label>
            <input
                type="text"
                id="telefono2"
                name="telefono2"
                maxlength="10"
                value="{{ old('telefono2', $escuela->telefono2) }}"
            >
        </div>

        <div>
            <label for="telefono3">Teléfono alternativo 2</label>
            <input
                type="text"
                id="telefono3"
                name="telefono3"
                maxlength="10"
                value="{{ old('telefono3', $escuela->telefono3) }}"
            >
        </div>

        <div>
            <label for="fax">Fax</label>
            <input
                type="text"
                id="fax"
                name="fax"
                maxlength="10"
                value="{{ old('fax', $escuela->fax) }}"
            >
        </div>

        <div>
            <label for="lada">Lada</label>
            <input
                type="text"
                id="lada"
                name="lada"
                maxlength="5"
                value="{{ old('lada', $escuela->lada) }}"
            >
        </div>

        <div>
            <label for="correo_electronico">Correo electrónico</label>
            <input
                type="email"
                id="correo_electronico"
                name="correo_electronico"
                maxlength="128"
                value="{{ old('correo_electronico', $escuela->correo_electronico) }}"
            >
        </div>

        <div>
            <label for="pagina_web">Página web</label>
            <input
                type="text"
                id="pagina_web"
                name="pagina_web"
                maxlength="128"
                value="{{ old('pagina_web', $escuela->pagina_web) }}"
            >
        </div>

        <div>
            <label for="turno">Turno</label>
            <select id="turno" name="turno">
                @foreach ($turnos as $clave => $nombre)
                    <option
                        value="{{ $clave }}"
                        @selected(old('turno', $escuela->turno) == $clave)
                    >
                        {{ $nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="sostenimiento">Sostenimiento</label>
            <select id="sostenimiento" name="sostenimiento">
                @foreach ($sostenimientos as $clave => $nombre)
                    <option
                        value="{{ $clave }}"
                        @selected(old('sostenimiento', $escuela->sostenimiento) == $clave)
                    >
                        {{ $nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="investigacion">¿Realiza investigación?</label>
            <select id="investigacion" name="investigacion">
                @foreach ($investigacion as $clave => $nombre)
                    <option
                        value="{{ $clave }}"
                        @selected(old('investigacion', $escuela->investigacion) == $clave)
                    >
                        {{ $nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="fecha_autorizacion">Fecha de autorización</label>
            <input
                type="date"
                id="fecha_autorizacion"
                name="fecha_autorizacion"
                value="{{ old('fecha_autorizacion', $escuela->fecha_autorizacion) }}"
            >
        </div>

        <div>
            <label for="fecha_inicio">Fecha de inicio de actividades</label>
            <input
                type="date"
                id="fecha_inicio"
                name="fecha_inicio"
                value="{{ old('fecha_inicio', $escuela->fecha_inicio) }}"
            >
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="boton__primario">
                Guardar información
            </button>

            <a href="{{ route('siben.datosEscuela') }}" class="boton__secundario">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
