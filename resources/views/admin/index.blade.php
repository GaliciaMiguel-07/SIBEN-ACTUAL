@extends('layouts.siben.siben')

@section('titulo', 'Administrador — SIBEN')

@section('content')

<div id="area_contenido" style="margin-left:170px; padding:10px;">

    <a href="{{ route('dashboard') }}"
       style="display:inline-block; margin-bottom:12px; padding:6px 12px; background:#611232; color:#fff; text-decoration:none; border-radius:4px;">
        &larr; Inicio
    </a>

    <h3>Administrador</h3>

    <table class="tabla-opciones">
        <thead>
            <tr>
                <th>Opción</th>
                <th class="columna-acceso">Acceso</th>
            </tr>
        </thead>
        <tbody>
            @foreach($menu as $seccion => $opciones)
                {{-- Encabezado de sección (Alumnos / Personal) --}}
                <tr>
                    <td class="seccion-titulo" colspan="2">{{ $seccion }}</td>
                </tr>
                {{-- Opciones de la sección --}}
                @foreach($opciones as $opcion)
                    <tr>
                        <td>{{ $opcion['titulo'] }}</td>
                        <td class="columna-acceso">
                            @if(Route::has($opcion['ruta']))
                                <a href="{{ route($opcion['ruta']) }}" class="btn-acceso">Acceder</a>
                            @else
                                <span class="btn-acceso btn-deshabilitado">Acceder</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

</div>

@endsection

@push('styles')
<style>
    .tabla-opciones { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #ddd; font-size: 15px; }
    .tabla-opciones thead th { background: #611232; color: #fff; font-weight: 600; text-align: left; padding: 12px 15px; border: 1px solid #611232; }
    .tabla-opciones thead th.columna-acceso { text-align: center; }
    .tabla-opciones tbody td { padding: 12px 15px; border: 1px solid #ddd; vertical-align: middle; }
    .tabla-opciones tbody tr:nth-child(even) { background: #f7f7f7; }
    .tabla-opciones tbody tr:hover { background: #eee; }
    .seccion-titulo { background: #f7f7f7 !important; font-weight: 700; color: #611232; }
    .tabla-opciones td.columna-acceso { width: 120px; text-align: center; }
    .btn-acceso { display: inline-block; padding: 7px 16px; background: #611232; color: #fff !important; border: 1px solid #611232; border-radius: 4px; text-decoration: none !important; font-size: 14px; font-weight: 600; }
    .btn-acceso:hover { background: #4d0e27; border-color: #4d0e27; }
    .btn-deshabilitado { background: #999; border-color: #999; cursor: not-allowed; }
</style>
@endpush