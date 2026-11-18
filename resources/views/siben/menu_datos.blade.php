
@extends('layouts.siben.siben')

@section('titulo', 'Datos Generales de la Escuela')

@section('content')
<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 900px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    <div class="buscar01" style="margin-bottom: 24px;">

        <div style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 20px;">
            <h2 class="patria" style="font-size: 1.8rem; margin: 0; color: var(--neutro900);">
                Datos Generales de la Escuela
            </h2>
        </div>

        @if (!empty($mensaje))
            <div class="alert alert-warning" role="status" style="margin-bottom: 20px;">
                {{ $mensaje }}
            </div>
        @endif

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid var(--neutro400);">
                    <th style="padding: 12px 16px;">Opción</th>
                    <th style="padding: 12px 16px; text-align: center; width: 160px;">Acceso</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($opcionesMenu as $opcion)
                    @php
                        $accion = $opcion[2] ?? '';

                        $rutas = [
                            'domicilioEscuela' => route('siben.domicilioEscuela'),
                            'datosEscuela' => route('siben.datosEscuela'),
                            'estructuraEscuela/v' => route('siben.estructuraEscuela', ['opc' => 'v']),
                            'directoresEscuela/v' => route('siben.directoresEscuela', ['opc' => 'v']),
                        ];

                        $url = $rutas[$accion] ?? null;
                    @endphp

                    <tr style="border-bottom: 1px solid var(--neutro300);">
                        <td style="padding: 14px 16px;">
                            {{ $opcion[1] ?? '' }}
                        </td>

                        <td style="padding: 14px 16px; text-align: center;">
                            @if ($url)
                                <a href="{{ $url }}"
                                   class="boton__primario"
                                   style="padding: 6px 16px; font-size: .9rem; text-decoration: none; display: inline-block;">
                                    Acceder
                                </a>
                            @else
                                <span class="text-muted">No disponible</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('siben.index') }}"
           class="boton__secundario"
           style="text-decoration: none; padding: 8px 18px; display: inline-block;">
            Regresar
        </a>
    </div>
</div>
@endsection
