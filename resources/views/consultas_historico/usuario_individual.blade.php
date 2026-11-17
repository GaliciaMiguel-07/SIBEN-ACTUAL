@extends('layouts.siben.siben')

@section('titulo', 'Consulta individual de alumnos')

@section('content')
<section class="contenedor" style="max-width: 980px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">
    <div class="buscar01" style="margin-bottom: 20px;">
        @if(!empty($mensaje))
            <div class="alert alert-danger" role="alert" style="margin-bottom: 16px;">{{ $mensaje }}</div>
        @endif

        <form method="POST" action="{{ route('consultas_historico.usuario.individual.buscar') }}" style="max-width: 420px; margin: 0 auto 18px;">
            @csrf
            <table style="width: 100%; border-collapse: collapse; border: 1px solid var(--principal);">
                <tr>
                    <th style="padding: 8px; text-align: left; border-bottom: 1px solid var(--principal);">Información Personal del Alumno</th>
                </tr>
                <tr><td style="padding: 6px;">Nombre(s):</td></tr>
                <tr><td style="padding: 6px;"><input class="form-control" type="text" name="nombre" value="{{ $criterios['nombre'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td></tr>
                <tr><td style="padding: 6px;">Primer Apellido:</td></tr>
                <tr><td style="padding: 6px;"><input class="form-control" type="text" name="apellido_paterno" value="{{ $criterios['apellido_paterno'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td></tr>
                <tr><td style="padding: 6px;">Segundo Apellido:</td></tr>
                <tr><td style="padding: 6px;"><input class="form-control" type="text" name="apellido_materno" value="{{ $criterios['apellido_materno'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td></tr>
                <tr><td style="padding: 6px;">Curp:</td></tr>
                <tr><td style="padding: 6px;"><input class="form-control" type="text" name="curp" value="{{ $criterios['curp'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td></tr>
                <tr><td style="padding: 6px;">Número Identificador:</td></tr>
                <tr><td style="padding: 6px;"><input class="form-control" type="text" name="nid" value="{{ $criterios['nid'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td></tr>
                <tr>
                    <th style="padding: 8px; text-align: left; color: var(--principal); font-size: .95rem; border-top: 1px solid var(--principal);">
                        *Puede buscar llenando los campos de nombre o por NID o por CURP
                    </th>
                </tr>
            </table>

            <div style="text-align: center; margin-top: 14px;">
                <button type="submit" class="boton__primario">Buscar</button>
            </div>
        </form>

        <div style="text-align: center; display: flex; justify-content: center; gap: 10px;">
            <a href="{{ route('alumnos.index') }}" class="boton__secundario" style="text-decoration: none; display: inline-block;">Anterior</a>
            <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration: none; display: inline-block;">Inicio</a>
        </div>
    </div>
</section>
@endsection
