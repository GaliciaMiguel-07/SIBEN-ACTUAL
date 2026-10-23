@extends('layouts.siben.siben')

@section('titulo', 'Administrador — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 900px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    <!-- Botón de inicio -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">
            &larr; Inicio
        </a>
    </div>

    <!-- Tarjeta contenedora con el estilo oficial (.buscar01) -->
    <div class="buscar01" style="margin-bottom: 24px;">
        
        <div class="buscar01__hgroup mb--24" style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 16px;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">Administrador</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="tabla-opciones" style="width: 100%; border-collapse: collapse; text-align: left; background: #fff; border: 1px solid var(--neutro400); font-size: 15px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--neutro400);">
                        <th style="background: var(--principal, #811331); color: #fff; font-weight: 600; text-align: left; padding: 12px 15px;">Opción</th>
                        <th style="background: var(--principal, #811331); color: #fff; font-weight: 600; text-align: center; padding: 12px 15px; width: 130px;">Acceso</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menu as $seccion => $opciones)
                        {{-- Encabezado de sección (Alumnos / Personal) --}}
                        <tr>
                            <td colspan="2" style="background: var(--neutro200); font-weight: 700; color: var(--principal, #811331); padding: 12px 15px; border-top: 1px solid var(--neutro300); border-bottom: 1px solid var(--neutro300);">
                                {{ $seccion }}
                            </td>
                        </tr>
                        {{-- Opciones de la sección --}}
                        @foreach($opciones as $opcion)
                            <tr style="border-bottom: 1px solid var(--neutro300);">
                                <td style="padding: 14px 15px; color: var(--neutro800); vertical-align: middle;">
                                    {{ $opcion['titulo'] }}
                                </td>
                                <td style="padding: 14px 15px; text-align: center; vertical-align: middle;">
                                    @if(Route::has($opcion['ruta']))
                                        <a href="{{ route($opcion['ruta']) }}" class="boton__primario" style="padding: 6px 16px; font-size: 0.9rem; text-decoration: none; display: inline-block;">
                                            Acceder
                                        </a>
                                    @else
                                        <span style="display: inline-block; padding: 6px 16px; background: var(--neutro500); color: #fff; border-radius: 4px; font-size: 0.9rem; cursor: not-allowed;">
                                            Acceder
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('styles')
<link href="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd-guinda.css" rel="stylesheet">
<style>
    .tabla-opciones tbody tr:hover {
        background-color: var(--neutro100);
    }
</style>
@endpush

@push('scripts')
<script src="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd.js"></script>
@endpush