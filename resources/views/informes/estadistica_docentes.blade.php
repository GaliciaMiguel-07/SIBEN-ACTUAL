@extends('layouts.siben.siben')

@section('content')
<div class="container">
    <h2>Estadística de docentes</h2>

    <div class="mb-3">
        <h5>{{ $escuela->escuela ?? 'Escuela' }}</h5>
        <p>
            <strong>Entidad:</strong>
            {{ $escuela->entidad->entidad ?? '' }}
        </p>
        <p>
            <strong>Captura:</strong>
            {{ $captura ?? 'Sin información' }}
        </p>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Total de docentes registrados</h5>
            <h3>{{ $total ?? 0 }}</h3>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Licenciatura</th>
                    <th>Especialidad</th>
                    <th>Modalidad</th>
                    <th>Hombres</th>
                    <th>Mujeres</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $hayDatos = false;
                    $modalidadesNombre = [
                        'e' => 'Escolarizada',
                        'm' => 'Mixta'
                    ];
                @endphp

                @foreach($cuentas as $idLic => $especialidadesCuenta)
                    @foreach($especialidadesCuenta as $idEsp => $modalidadesCuenta)
                        @foreach($modalidadesCuenta as $modalidad => $generos)
                            @php
                                $hayDatos = true;

                                $hombres = $generos['h'] ?? 0;
                                $mujeres = $generos['m'] ?? 0;
                            @endphp

                            <tr>
                                <td>
                                    {{ $licenciaturas[$idLic] ?? 'Sin nombre' }}
                                </td>
                                <td>
                                    {{ $especialidades[$idEsp] ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ $modalidadesNombre[$modalidad] ?? $modalidad }}
                                </td>
                                <td>{{ $hombres }}</td>
                                <td>{{ $mujeres }}</td>
                                <td>{{ $hombres + $mujeres }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach

                @if(!$hayDatos)
                    <tr>
                        <td colspan="6" class="text-center">
                            No hay información de docentes para mostrar.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <a href="{{ route('informes.estadisticas') }}" class="btn btn-secondary">
        Regresar
    </a>
</div>
@endsection
