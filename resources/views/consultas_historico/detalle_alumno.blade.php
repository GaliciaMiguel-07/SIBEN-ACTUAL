@extends('layouts.siben.siben')

@section('titulo', 'Detalle histórico del alumno')

@section('content')
<section class="contenedor" style="max-width: 1100px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">
    <div class="buscar01" style="margin-bottom: 20px;">

        @php $alumno = $historia->first(); @endphp

        <table style="width: 100%; border-collapse: collapse; border: 1px solid var(--principal); margin-bottom: 18px;">
            <thead>
                <tr style="border-bottom: 1px solid var(--principal);">
                    <th style="padding: 8px;">NID</th>
                    <th style="padding: 8px;">Nombre</th>
                    <th style="padding: 8px;">Primer Apellido</th>
                    <th style="padding: 8px;">Segundo Apellido</th>
                    <th style="padding: 8px;">CURP</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px;">{{ $alumno->nid }}</td>
                    <td style="padding: 8px;">{{ $alumno->nombre }}</td>
                    <td style="padding: 8px;">{{ $alumno->apellido_paterno }}</td>
                    <td style="padding: 8px;">{{ $alumno->apellido_materno }}</td>
                    <td style="padding: 8px;">{{ $alumno->curp }}</td>
                </tr>
            </tbody>
        </table>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; border: 1px solid var(--principal); margin-bottom: 18px;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--principal);">
                        <th style="padding: 8px;">No.</th>
                        <th style="padding: 8px;">Captura</th>
                        <th style="padding: 8px;">Entidad</th>
                        <th style="padding: 8px;">Licenciatura</th>
                        <th style="padding: 8px;">Especialidad</th>
                        <th style="padding: 8px;">Modalidad</th>
                        <th style="padding: 8px;">Semestre</th>
                        <th style="padding: 8px;">Matrícula</th>
                        <th style="padding: 8px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                        $idEscuelaAnterior = null;
                    @endphp
                    @foreach($historia as $fila)
                        @if($idEscuelaAnterior !== $fila->id_escuela)
                            <tr>
                                <th colspan="9" style="padding: 8px; text-align: left; border-top: 1px solid var(--principal); border-bottom: 1px solid var(--principal); background: #f2f2f2;">
                                    {{ $escuelas[$fila->id_escuela]->escuela ?? 'Escuela no disponible' }}
                                </th>
                            </tr>
                            @php $idEscuelaAnterior = $fila->id_escuela; @endphp
                        @endif
                        @php
                            $escuela = $escuelas[$fila->id_escuela] ?? null;
                            $entidad = $escuela ? ($entidades[$escuela->id_entidad] ?? '') : '';
                            $lic = $licenciaturas[$fila->id_licenciatura] ?? 'n/a';
                            $esp = $especialidades[$fila->id_especialidad] ?? 'n/a';
                            $estado = $statusTexto[$fila->status] ?? $fila->status;
                        @endphp
                        <tr style="border-bottom: 1px solid var(--neutro300);">
                            <td style="padding: 8px;">{{ $i++ }}</td>
                            <td style="padding: 8px;">{{ $fila->captura }}</td>
                            <td style="padding: 8px;">{{ $entidad }}</td>
                            <td style="padding: 8px;">{{ $lic }}</td>
                            <td style="padding: 8px;">{{ $esp }}</td>
                            <td style="padding: 8px;">{{ $fila->modalidad }}</td>
                            <td style="padding: 8px;">{{ $fila->semestre }}</td>
                            <td style="padding: 8px;">{{ $fila->matricula }}</td>
                            <td style="padding: 8px;">{{ $estado }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="text-align: center; display: flex; justify-content: center; gap: 10px;">
            <a href="{{ $regresar }}" class="boton__secundario" style="text-decoration: none; display: inline-block;">Regresar</a>
            <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration: none; display: inline-block;">Inicio</a>
        </div>

    </div>
</section>
@endsection
