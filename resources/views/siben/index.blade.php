@extends('layouts.siben.siben')

@section('content')

    @if(isset($mensajes))
        <table class="datos2" style="width:450px;">
            @foreach($mensajes as $mensaje)
                <tr>
                    <td style="font-size:2em;">
                        {!! nl2br(e($mensaje['Mensaje']['mensaje'] ?? $mensaje->mensaje ?? '')) !!}
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    @if($nivel >= 3)
        <br>
        <br>
    @endif

    @if($nivel != 1)
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

        <div style="text-align:right;">
            <span style="color:#cc1543;">
                <a
                    href="http://www.siben.sep.gob.mx/menu_ayuda/index.php"
                    target="_blank"
                    style="color:#cc1543;"
                >
                    <b>?</b> Ayuda
                </a>
            </span>
        </div>
    @endif

    <a href="{{route('usuarios.logout')}}">Cerrar sesión</a>

@endsection