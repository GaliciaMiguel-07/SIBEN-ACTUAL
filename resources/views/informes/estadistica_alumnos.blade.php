
@extends('layouts.siben.siben')

@section('content')
<div class="container">
    <h2>Estadística de alumnos</h2>

    @if($escuela)
        <div class="mb-3">
            @if($escuelas->count() === 1)
                <h5>{{ $escuela->escuela }}</h5>
                <p>
                    <strong>Entidad:</strong>
                    {{ $escuela->entidad->entidad ?? '' }}
                </p>
            @else
                <h5>Estadísticas de todas las escuelas autorizadas</h5>
            @endif

            <p>
                <strong>Captura:</strong> {{ $captura ?? 'Sin información' }}
            </p>
        </div>
    @else
        <div class="alert alert-info">
            No hay escuelas disponibles para consultar.
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th rowspan="2">Escuela</th>
                    <th rowspan="2">Licenciatura</th>
                    <th rowspan="2">Especialidad</th>
                    <th rowspan="2">Modalidad</th>
                    <th colspan="2" class="text-center">Género</th>
                    <th colspan="6" class="text-center">Semestre</th>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
                    <th>Hombres</th>
                    <th>Mujeres</th>
                    <th>2</th>
                    <th>4</th>
                    <th>6</th>
                    <th>8</th>
                    <th>10</th>
                    <th>12</th>
                </tr>
            </thead>

            <tbody>
                @forelse($escuelaLicenciaturas as $item)
                    @php
                        $cuentas = $item['Cuentas'] ?? [];

                        $hombres = $cuentas['totalHombres'] ?? 0;
                        $mujeres = $cuentas['totalMujeres'] ?? 0;
                    @endphp

                    <tr>
                        <td>{{ $item['escuela'] ?? 'Sin escuela' }}</td>
                        <td>{{ $item['licenciatura']['licenciatura'] ?? 'Sin nombre' }}</td>
                        <td>{{ $item['especialidad']['especialidad'] ?: 'N/A' }}</td>
                        <td>{{ $modalidades[$item['modalidad'] ?? ''] ?? 'Sin modalidad' }}</td>
                        <td>{{ $hombres }}</td>
                        <td>{{ $mujeres }}</td>
                        <td>{{ $cuentas['2'] ?? 0 }}</td>
                        <td>{{ $cuentas['4'] ?? 0 }}</td>
                        <td>{{ $cuentas['6'] ?? 0 }}</td>
                        <td>{{ $cuentas['8'] ?? 0 }}</td>
                        <td>{{ $cuentas['10'] ?? 0 }}</td>
                        <td>{{ $cuentas['12'] ?? 0 }}</td>
                        <td>{{ $hombres + $mujeres }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center">
                            No hay información de alumnos para mostrar.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="4">Totales</th>
                    <th>{{ $totales['hombres'] ?? 0 }}</th>
                    <th>{{ $totales['mujeres'] ?? 0 }}</th>
                    <th>{{ $totales['2'] ?? 0 }}</th>
                    <th>{{ $totales['4'] ?? 0 }}</th>
                    <th>{{ $totales['6'] ?? 0 }}</th>
                    <th>{{ $totales['8'] ?? 0 }}</th>
                    <th>{{ $totales['10'] ?? 0 }}</th>
                    <th>{{ $totales['12'] ?? 0 }}</th>
                    <th>{{ $totales['general'] ?? 0 }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <a href="{{ route('informes.estadisticas') }}" class="btn btn-secondary">
        Regresar
    </a>
</div>
@endsection
