@extends('layouts.siben.siben')

@section('content')
<div class="container">
    <h2>Estadística de personal</h2>

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
            <h5>Total de personal</h5>
            <h3>{{ $cuentaTotal ?? 0 }}</h3>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Función</th>
                    <th>Hombres</th>
                    <th>Mujeres</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $i = 1;
                @endphp

                @foreach($funciones as $idFuncion => $funcion)
                    @php
                        $hombres = $cuentas[$i]['hombres'] ?? 0;
                        $mujeres = $cuentas[$i]['mujeres'] ?? 0;
                    @endphp

                    <tr>
                        <td>{{ $funcion }}</td>
                        <td>{{ $hombres }}</td>
                        <td>{{ $mujeres }}</td>
                        <td>{{ $hombres + $mujeres }}</td>
                    </tr>

                    @php
                        $i++;
                    @endphp
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th>Total general</th>
                    <th colspan="2"></th>
                    <th>{{ $cuentaTotal ?? 0 }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <a href="{{ route('informes.estadisticas') }}" class="btn btn-secondary">
        Regresar
    </a>
</div>
@endsection
