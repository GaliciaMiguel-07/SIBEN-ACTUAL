
@extends('layouts.siben.siben')

@section('titulo', 'Estadísticas')

@section('content')
<div class="container">
    <h2>Estadísticas</h2>

    <p class="text-muted">
        Consulta el catálogo histórico de estadísticas del Sistema de Información Básica de la Educación Normal.
    </p>

    @php
        $catalogos = [
            [
                'titulo' => 'Estadísticas generales',
                'descripcion' => 'Información estadística general de las escuelas normales.',
                'documentos' => [
                    ['periodo' => '2009-2010', 'archivo' => 'Estadisticas_0910.pdf', 'ruta' => 'estadistica2010/Estadisticas_0910.pdf'],
                    ['periodo' => '2010-2011', 'archivo' => 'Estadisticas_1011.pdf', 'ruta' => 'estadistica2011/Estadisticas_1011.pdf'],
                    ['periodo' => '2011-2012', 'archivo' => 'Estadisticas_1112.pdf', 'ruta' => 'estadistica2012/Estadisticas_1112.pdf'],
                    ['periodo' => '2012-2013', 'archivo' => 'estadisticas_normales20122.xlsx', 'ruta' => 'estadistica2012/estadisticas_normales20122.xlsx'],
                    ['periodo' => '2013-2014', 'archivo' => 'estadisticas_normales2013.xlsx', 'ruta' => 'estadistica2013/estadisticas_normales2013.xlsx'],
                    ['periodo' => '2014-2015', 'archivo' => 'estadisticas_normales2014.xlsx', 'ruta' => 'estadistica2014/estadisticas_normales2014.xlsx'],
                    ['periodo' => '2015-2016', 'archivo' => 'estadisticas_2015-2016.xlsx', 'ruta' => 'estadistica2015/estadisticas_2015-2016.xlsx'],
                    ['periodo' => '2016-2017', 'archivo' => 'Estadisticas_2016-2017.xlsx', 'ruta' => 'estadistica2016/Estadisticas_2016-2017.xlsx'],
                    ['periodo' => '2017-2018', 'archivo' => 'Estadisticas_17-18.xlsx', 'ruta' => 'estadistica2017/Estadisticas_17-18.xlsx'],
                ],
            ],
            [
                'titulo' => 'Estadísticas de alumnos',
                'descripcion' => 'Información histórica de matrícula de alumnos.',
                'documentos' => [
                    ['periodo' => '2007-2008', 'archivo' => 'alumnos0708.xls', 'ruta' => 'estadistica/alumnos0708.xls'],
                    ['periodo' => '2008-2009', 'archivo' => 'alumnos_gral.xls', 'ruta' => 'estadistica2008/alumnos_gral.xls'],
                    ['periodo' => '2008-2009', 'archivo' => 'alumnos_lic.xls', 'ruta' => 'estadistica2008/alumnos_lic.xls'],
                    ['periodo' => '2009-2010', 'archivo' => 'alumnos_gral.xls', 'ruta' => 'estadistica2010/alumnos_gral.xls'],
                    ['periodo' => '2009-2010', 'archivo' => 'alumnos_lic.xls', 'ruta' => 'estadistica2010/alumnos_lic.xls'],
                    ['periodo' => '2010-2011', 'archivo' => 'alumnos_gral.xls', 'ruta' => 'estadistica2011/alumnos_gral.xls'],
                    ['periodo' => '2010-2011', 'archivo' => 'alumnos_lic.xls', 'ruta' => 'estadistica2011/alumnos_lic.xls'],
                    ['periodo' => '2011-2012', 'archivo' => 'alumnos_gral.xls', 'ruta' => 'estadistica2012/alumnos_gral.xls'],
                    ['periodo' => '2011-2012', 'archivo' => 'alumnos_lic.xls', 'ruta' => 'estadistica2012/alumnos_lic.xls'],
                    ['periodo' => '2012-2013', 'archivo' => 'alumnos_gral.xlsx', 'ruta' => 'estadistica2012/alumnos_gral.xlsx'],
                    ['periodo' => '2012-2013', 'archivo' => 'alumnos_lic.xlsx', 'ruta' => 'estadistica2012/alumnos_lic.xlsx'],
                    ['periodo' => '2013-2014', 'archivo' => 'alumnos_gral.xlsx', 'ruta' => 'estadistica2013/alumnos_gral.xlsx'],
                    ['periodo' => '2013-2014', 'archivo' => 'alumnos_lic.xlsx', 'ruta' => 'estadistica2013/alumnos_lic.xlsx'],
                    ['periodo' => '2014-2015', 'archivo' => 'alumnos_gral.xlsx', 'ruta' => 'estadistica2014/alumnos_gral.xlsx'],
                    ['periodo' => '2014-2015', 'archivo' => 'alumnos_lic.xlsx', 'ruta' => 'estadistica2014/alumnos_lic.xlsx'],
                ],
            ],
            [
                'titulo' => 'Estadísticas de docentes',
                'descripcion' => 'Información histórica del personal docente.',
                'documentos' => [
                    ['periodo' => '2007-2008', 'archivo' => 'docentes0708.xls', 'ruta' => 'estadistica/docentes0708.xls'],
                    ['periodo' => '2008-2009', 'archivo' => 'docentes0809a.xls', 'ruta' => 'estadistica/docentes0809a.xls'],
                    ['periodo' => '2009-2010', 'archivo' => 'docentes_lic.xls', 'ruta' => 'estadistica2010/docentes_lic.xls'],
                    ['periodo' => '2010-2011', 'archivo' => 'docentes_lic.xls', 'ruta' => 'estadistica2011/docentes_lic.xls'],
                    ['periodo' => '2011-2012', 'archivo' => 'docentes_lic.xls', 'ruta' => 'estadistica2012/docentes_lic.xls'],
                    ['periodo' => '2012-2013', 'archivo' => 'docentes20122.xlsx', 'ruta' => 'estadistica2012/docentes20122.xlsx'],
                    ['periodo' => '2013-2014', 'archivo' => 'docentes2013.xlsx', 'ruta' => 'estadistica2013/docentes2013.xlsx'],
                ],
            ],
            [
                'titulo' => 'Estadísticas de personal',
                'descripcion' => 'Información histórica de funciones, contratación, dedicación y nivel de estudios.',
                'documentos' => [
                    ['periodo' => '2008-2009', 'archivo' => 'personal_funcion.xls', 'ruta' => 'estadistica2008/personal_funcion.xls'],
                    ['periodo' => '2008-2009', 'archivo' => 'personal_contratacion.xls', 'ruta' => 'estadistica2008/personal_contratacion.xls'],
                    ['periodo' => '2008-2009', 'archivo' => 'personal_dedicacion.xls', 'ruta' => 'estadistica2008/personal_dedicacion.xls'],
                    ['periodo' => '2008-2009', 'archivo' => 'personal_nivel.xls', 'ruta' => 'estadistica2008/personal_nivel.xls'],
                    ['periodo' => '2010-2011', 'archivo' => 'personal_funcion.xls', 'ruta' => 'estadistica2011/personal_funcion.xls'],
                    ['periodo' => '2010-2011', 'archivo' => 'personal_funcion2.xls', 'ruta' => 'estadistica2011/personal_funcion2.xls'],
                    ['periodo' => '2010-2011', 'archivo' => 'personal_contratacion.xls', 'ruta' => 'estadistica2011/personal_contratacion.xls'],
                    ['periodo' => '2010-2011', 'archivo' => 'personal_dedicacion.xls', 'ruta' => 'estadistica2011/personal_dedicacion.xls'],
                    ['periodo' => '2010-2011', 'archivo' => 'personal_nivel.xls', 'ruta' => 'estadistica2011/personal_nivel.xls'],
                    ['periodo' => '2011-2012', 'archivo' => 'personal_funcion.xls', 'ruta' => 'estadistica2012/personal_funcion.xls'],
                    ['periodo' => '2011-2012', 'archivo' => 'personal_funcion2.xls', 'ruta' => 'estadistica2012/personal_funcion2.xls'],
                    ['periodo' => '2011-2012', 'archivo' => 'personal_contratacion.xls', 'ruta' => 'estadistica2012/personal_contratacion.xls'],
                    ['periodo' => '2011-2012', 'archivo' => 'personal_dedicacion.xls', 'ruta' => 'estadistica2012/personal_dedicacion.xls'],
                    ['periodo' => '2011-2012', 'archivo' => 'personal_nivel.xls', 'ruta' => 'estadistica2012/personal_nivel.xls'],
                ],
            ],
        ];
    @endphp

    @foreach($catalogos as $catalogo)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">{{ $catalogo['titulo'] }}</h5>
            </div>

            <div class="card-body">
                <p class="text-muted">{{ $catalogo['descripcion'] }}</p>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Documento</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($catalogo['documentos'] as $documento)
                                @php
                                    $existe = file_exists(public_path('files/' . $documento['ruta']));
                                    $archivo = strtolower($documento['archivo']);

                                    if ($catalogo['titulo'] === 'Estadísticas generales') {
                                        $nombreDocumento = 'Estadística general';
                                    } elseif ($catalogo['titulo'] === 'Estadísticas de alumnos') {
                                        if (str_contains($archivo, 'gral')) {
                                            $nombreDocumento = 'Matrícula general';
                                        } elseif (str_contains($archivo, 'lic')) {
                                            $nombreDocumento = 'Matrícula por licenciatura';
                                        } else {
                                            $nombreDocumento = 'Matrícula de alumnos';
                                        }
                                    } elseif ($catalogo['titulo'] === 'Estadísticas de docentes') {
                                        $nombreDocumento = 'Estadística de docentes';
                                    } else {
                                        $nombreDocumento = match (true) {
                                            str_contains($archivo, 'funcion2') => 'Personal por función (complementario)',
                                            str_contains($archivo, 'funcion') => 'Personal por función',
                                            str_contains($archivo, 'contratacion') => 'Personal por tipo de contratación',
                                            str_contains($archivo, 'dedicacion') => 'Personal por dedicación',
                                            str_contains($archivo, 'nivel') => 'Personal por nivel de estudios',
                                            default => 'Estadística de personal',
                                        };
                                    }
                                @endphp

                                <tr>
                                    <td>{{ $documento['periodo'] }}</td>
                                    <td>{{ $nombreDocumento }} — Ciclo escolar {{ $documento['periodo'] }}</td>
                                    <td>
                                        @if($existe)
                                            <span class="badge bg-success">Disponible</span>
                                        @else
                                            <span class="badge bg-secondary">Archivo no disponible</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($existe)
                                            <a href="{{ asset('files/' . $documento['ruta']) }}"
                                               class="btn btn-sm btn-primary"
                                               target="_blank">
                                                Descargar
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                No disponible
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
