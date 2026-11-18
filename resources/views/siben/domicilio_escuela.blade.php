
@extends('layouts.siben.siben')

@section('titulo', $title)

@section('content')
<div class="contenedor" style="max-width: 950px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">

    <div class="buscar01" style="padding: 24px;">

        <h2 class="patria" style="color: var(--neutro900); font-size: 1.7rem; margin-bottom: 22px;">
            {{ $title }}
        </h2>

        @if (!$domicilioEscuela)
            <div class="alert alert-warning" role="status">
                La escuela todavía no tiene un domicilio registrado.
            </div>
        @else

            <h3 class="h5" style="margin-bottom: 14px;">Ubicación de la escuela</h3>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        @php
                            $campos = [
                                'Entidad federativa' => $domicilioEscuela->nom_ent,
                                'Municipio' => $domicilioEscuela->nom_mun,
                                'Localidad' => $localidad->localidad ?? $domicilioEscuela->nom_loc,
                                'Tipo de vialidad' => $domicilioEscuela->tipovial,
                                'Nombre de vialidad' => $domicilioEscuela->nomvial,
                                'Código postal' => $domicilioEscuela->cp,
                                'Tipo de asentamiento' => $domicilioEscuela->tipoasen,
                                'Nombre del asentamiento' => $domicilioEscuela->nomasen,
                                'Número exterior' => trim(($domicilioEscuela->numextnum1 ?? '') . ' ' . ($domicilioEscuela->numextnum2 ?? '') . ' ' . ($domicilioEscuela->numextalf1 ?? '')),
                                'Número interior' => trim(($domicilioEscuela->numintnum ?? '') . ' ' . ($domicilioEscuela->numintalf ?? '')),
                                'Referencia 1' => trim(($domicilioEscuela->tiporef1 ?? '') . ' ' . ($domicilioEscuela->nomref1 ?? '')),
                                'Referencia 2' => trim(($domicilioEscuela->tiporef2 ?? '') . ' ' . ($domicilioEscuela->nomref2 ?? '')),
                                'Referencia 3' => trim(($domicilioEscuela->tiporef3 ?? '') . ' ' . ($domicilioEscuela->nomref3 ?? '')),
                                'Descripción de ubicación' => $domicilioEscuela->descrubic,
                                'Seguridad social' => $seguridad_social->regimen ?? '',
                                'Latitud' => $domicilioEscuela->latitud,
                                'Longitud' => $domicilioEscuela->longitud,
                            ];
                        @endphp

                        @foreach ($campos as $etiqueta => $valor)
                            <tr style="border-bottom: 1px solid var(--neutro300);">
                                <th scope="row" style="padding: 11px 12px; width: 35%; text-align: left;">
                                    {{ $etiqueta }}
                                </th>
                                <td style="padding: 11px 12px;">
                                    {{ filled($valor) ? $valor : 'No registrado' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if (!empty($botones['escuela']))
            <div style="margin-top: 24px; text-align: center;">
                {!! $botones['escuela'] !!}
            </div>
        @endif

    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('siben.index') }}"
           class="boton__secundario"
           style="display: inline-block; padding: 8px 18px; text-decoration: none;">
            Regresar
        </a>
    </div>
</div>
@endsection
