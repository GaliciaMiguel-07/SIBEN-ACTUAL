@php
    $usuario = auth()->user();
    $nivel   = $usuario->nivel  ?? 1;
    $nombre  = $usuario->nombre ?? '';

    // Consulta directa a la base de datos para evitar depender del nombre del Modelo
    $entidadObj = \Illuminate\Support\Facades\DB::table('cat_entidades')
                    ->where('id_renapo', $usuario->id_renapo ?? '')
                    ->first();

    // Obtener el nombre de la entidad (busca la columna 'entidad' o 'nombre')
    $nombreEntidad = $entidadObj->entidad ?? $entidadObj->nombre ?? '';

    // Obtener la escuela si existe relación en el usuario
    $nombreEscuela = $usuario->escuela->escuela ?? '';
@endphp

<div id="pleca_superior">

    <img
        src="{{ url('/images/pleca_superior2020.jpg') }}"
        alt="SEP - Sistema de Información Básica de Escuelas Normales"
        style="width:100%;"
    >

    @if ($nivel > 0)
        <img
            src="{{ url('/images/pleca_superior_subtitulo' . $nivel . '.jpg') }}"
            alt="Nivel {{ $nivel }}"
            style="width:100%;"
        >
    @endif

    <div id="info_usuario" style="text-align:right; padding:4px 10px; font-size:12px;">

        @if ($nivel == 1)

            <strong>{{ $nombre }}</strong>

        @elseif ($nivel == 2)

            <strong>Entidad:</strong> {{ $nombreEntidad }}
            &nbsp;|&nbsp;
            <strong>{{ $nombre }}</strong>

        @elseif ($nivel >= 3 && $nivel <= 5)

            <strong>Escuela:</strong> {{ $nombreEscuela }}
            &nbsp;|&nbsp;
            <strong>Entidad:</strong> {{ $nombreEntidad }}
            &nbsp;|&nbsp;
            <strong>{{ $nombre }}</strong>

        @else

            <strong>{{ $nombre }}</strong>

        @endif

        <div style="margin-top:3px;">
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:none;border:none;padding:0;color:inherit;text-decoration:underline;cursor:pointer;font:inherit;">
                    Cerrar sesión
                </button>
            </form>
        </div>

    </div>

</div>