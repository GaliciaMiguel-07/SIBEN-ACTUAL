@extends('layouts.siben.siben')

@section('titulo', 'Administrador — SIBEN')

@section('content')

<div style="text-align:center; margin-top:30px;">

    <h2>Administrador</h2>

    <table class="datos2" style="width:auto; margin:20px auto;">

        @foreach ($menu as $grupo => $opciones)

            <tr>
                <th colspan="2">{{ $grupo }}</th>
            </tr>

            @foreach ($opciones as [$etiqueta, $ruta])

                <tr>
                    <td style="text-align:left;">
                        @if (Route::has($ruta))
                            <a href="{{ route($ruta) }}">{{ $etiqueta }}</a>
                        @else
                            <span style="color:#999;">{{ $etiqueta }}</span>
                        @endif
                    </td>
                </tr>

            @endforeach

        @endforeach

    </table>

</div>

@endsection