<div id="cabecera">

    {{-- Pleca superior --}}
    <img
        src="{{ url('/images/pleca_superior2020.jpg') }}"
        alt="SIBEN"
    >

    {{-- Subtítulo dependiendo del nivel --}}
    <img
        src="{{ url('/images/pleca_superior_subtitulo' . ($nivel ?? '') . '.jpg') }}"
        alt=""
    >

    <div>
        <h4 style="text-align:right;">

            @if(isset($nombreEscuela) && $nombreEscuela)
                {{ $nombreEscuela }} -
            @endif

            @if(($nivel ?? '') == 2)
                Supervisor Estatal -
            @elseif(($nivel ?? '') == 1)
                {{ $nombre ?? '' }}
            @endif

            @if(isset($nombreEntidad))
                {{ $nombreEntidad }}
            @endif

            @if(($nivel ?? '') != 1)
                <br>
                {{ $nombre ?? '' }}
            @endif

        </h4>
    </div>

</div>