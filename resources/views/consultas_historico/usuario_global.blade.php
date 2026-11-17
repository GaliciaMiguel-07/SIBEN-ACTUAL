@extends('layouts.siben.siben')

@section('titulo', 'Consulta global de alumnos')

@section('content')
<section class="contenedor" style="max-width: 980px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">
    <div class="buscar01" style="margin-bottom: 20px;">
        @if(!empty($mensaje))
            <div class="alert alert-danger" role="alert" style="margin-bottom: 16px;">{{ $mensaje }}</div>
        @endif

        <form method="POST" action="{{ route('consultas_historico.usuario.global.buscar') }}" style="max-width: 780px; margin: 0 auto 18px;">
            @csrf
            <table style="width: 100%; border-collapse: collapse; border: 1px solid var(--principal);">
                <tr>
                    <th colspan="5" style="padding: 8px; text-align: left; border-bottom: 1px solid var(--principal);">Búsqueda global de alumnos (escuela del usuario)</th>
                </tr>
                <tr>
                    <td style="padding: 6px;">NID:</td>
                    <td style="padding: 6px;"><input class="form-control" type="text" name="nid" value="{{ $criterios['nid'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td>
                    <td style="padding: 6px;">Curp:</td>
                    <td colspan="2" style="padding: 6px;"><input class="form-control" type="text" name="curp" value="{{ $criterios['curp'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td>
                </tr>
                <tr>
                    <td style="padding: 6px;">Primer Apellido:</td>
                    <td style="padding: 6px;"><input class="form-control" type="text" name="apellido_paterno" value="{{ $criterios['apellido_paterno'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td>
                    <td style="padding: 6px;">Segundo Apellido:</td>
                    <td colspan="2" style="padding: 6px;"><input class="form-control" type="text" name="apellido_materno" value="{{ $criterios['apellido_materno'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td>
                </tr>
                <tr>
                    <td style="padding: 6px;">Nombre(s):</td>
                    <td colspan="4" style="padding: 6px;"><input class="form-control" type="text" name="nombre" value="{{ $criterios['nombre'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td>
                </tr>
            </table>

            <div style="text-align: center; margin-top: 14px;">
                <button type="submit" class="boton__primario">Buscar</button>
            </div>
        </form>

        @if(isset($resultados) && $resultados->count() > 0)
            <div style="overflow-x: auto; margin-bottom: 16px;">
                <table style="width: 100%; border-collapse: collapse; border: 1px solid var(--principal);">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--principal);">
                            <th style="padding: 8px;">No.</th>
                            <th style="padding: 8px;">NID</th>
                            <th style="padding: 8px;">Primer Apellido</th>
                            <th style="padding: 8px;">Segundo Apellido</th>
                            <th style="padding: 8px;">Nombre</th>
                            <th style="padding: 8px;">Curp</th>
                            <th style="padding: 8px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $regresarParams = collect($criterios ?? [])->filter(function ($v) { return $v !== null && $v !== ''; })->toArray();
                            $urlRegresar = route('consultas_historico.usuario.global.buscar', $regresarParams);
                        @endphp
                        @foreach($resultados as $index => $alumno)
                            <tr style="border-bottom: 1px solid var(--neutro300);">
                                <td style="padding: 8px;">{{ $index + 1 }}</td>
                                <td style="padding: 8px;">{{ $alumno->nid }}</td>
                                <td style="padding: 8px;">{{ $alumno->apellido_paterno }}</td>
                                <td style="padding: 8px;">{{ $alumno->apellido_materno }}</td>
                                <td style="padding: 8px;">{{ $alumno->nombre }}</td>
                                <td style="padding: 8px;">{{ $alumno->curp }}</td>
                                <td style="padding: 8px; text-align: center;">
                                    <a href="{{ route('consultas_historico.detalle', ['nid' => $alumno->nid, 'regresar' => $urlRegresar]) }}" class="boton__primario" style="text-decoration: none; display: inline-block;">Detalle</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div style="text-align: center; display: flex; justify-content: center; gap: 10px;">
            <a href="{{ route('alumnos.index') }}" class="boton__secundario" style="text-decoration: none; display: inline-block;">Anterior</a>
            <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration: none; display: inline-block;">Inicio</a>
        </div>
    </div>
</section>
@endsection
