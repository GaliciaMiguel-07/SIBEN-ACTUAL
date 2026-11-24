@extends('layouts.siben.siben')

@section('titulo', 'Elegir Escuela — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    <div style="margin-bottom: 20px;">
        <a href="{{ $anterior ?? route('alumnos.index') }}"
           class="boton__secundario"
           style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">
            &larr; Regresar
        </a>
    </div>

    <div style="margin-bottom: 24px; padding: 24px; background: #fff; border: 1px solid var(--neutro400); border-radius: 4px;">

        <div style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 20px;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">
                Seleccione una escuela para editar alumnos
            </h3>
        </div>

        @if($escuelas->isEmpty())
            <p style="color: var(--neutro600); padding: 20px; text-align: center;">
                No hay escuelas disponibles para su entidad.
            </p>
        @else
            <div style="border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">

                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Escuelas disponibles
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="padding: 10px 16px; text-align: left; border-bottom: 2px solid var(--neutro400); color: var(--neutro800); font-weight: 600;">Escuela</th>
                                <th style="padding: 10px 16px; text-align: left; border-bottom: 2px solid var(--neutro400); color: var(--neutro800); font-weight: 600;">Clave</th>
                                <th style="padding: 10px 16px; text-align: center; border-bottom: 2px solid var(--neutro400); color: var(--neutro800); font-weight: 600;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($escuelas as $escuela)
                                @php
                                    $id     = $escuela->id;
                                    $nombre = $escuela->escuela;
                                    $clave  = $escuela->escuela_key;
                                    $urlDest = route('alumnos.busquedaAlumnos', ['opc' => 'e', 'id_escuela' => $id]);
                                @endphp
                                <tr style="border-bottom: 1px solid var(--neutro300); {{ $loop->even ? 'background:#fafafa;' : 'background:#fff;' }}">
                                    <td style="padding: 10px 16px; color: var(--neutro900);">{{ $nombre }}</td>
                                    <td style="padding: 10px 16px; color: var(--neutro700); font-family: monospace;">{{ $clave }}</td>
                                    <td style="padding: 10px 16px; text-align: center;">
                                        <a href="{{ $urlDest }}"
                                           class="boton__primario"
                                           style="text-decoration: none; padding: 5px 18px; font-size: 0.85rem; display: inline-block;">
                                            Ir &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        @endif

    </div>

</div>

@endsection
