
@extends('layouts.siben.siben')

@section('titulo', 'Directivos de la Escuela')

@section('content')
<div class="contenedor" style="max-width:1000px; margin:0 auto; padding:32px 16px; width:100%;">

    <div style="border-bottom:1px solid var(--neutro400); padding-bottom:12px; margin-bottom:24px;">
        <h2 class="patria" style="font-size:1.8rem; margin:0;">Directivos de la Escuela</h2>
        <p style="margin:8px 0 0;">{{ $escuela->escuela }}</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <section style="margin-bottom:30px;">
        <h3>Director de la escuela</h3>

        @if ($directivos->isEmpty())
            <div class="alert alert-warning">No hay un director activo registrado.</div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="padding:10px; text-align:left;">Nombre</th>
                            <th style="padding:10px; text-align:left;">Correo electrónico</th>
                            <th style="padding:10px; text-align:left;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($directivos as $director)
                            <tr style="border-top:1px solid var(--neutro300);">
                                <td style="padding:10px;">
                                    {{ $director->nombre }}
                                    {{ $director->apellido_paterno }}
                                    {{ $director->apellido_materno }}
                                </td>
                                <td style="padding:10px;">{{ $director->correo_electronico }}</td>
                                <td style="padding:10px;">
                                    @if ($puedeEditar)
                                        <form method="POST"
                                            action="{{ route('siben.desactivaDirector', ['id' => $director->id]) }}"
                                            onsubmit="return confirm('¿Deseas desactivar a este director?');">
                                            @csrf
                                            <button type="submit" class="boton__secundario" style="padding:6px 12px;">
                                                Desactivar
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section style="margin-bottom:30px;">
        <h3>Coordinadores por licenciatura</h3>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="padding:10px; text-align:left;">Licenciatura</th>
                        <th style="padding:10px; text-align:left;">Coordinador</th>
                        <th style="padding:10px; text-align:left;">Correo electrónico</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($licenciaturas as $licenciatura)
                        <tr style="border-top:1px solid var(--neutro300);">
                            <td style="padding:10px;">{{ $licenciatura['nombre'] }}</td>
                            <td style="padding:10px;">{{ $licenciatura['coordinador'] }}</td>
                            <td style="padding:10px;">{{ $licenciatura['correo'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="padding:10px;">No hay licenciaturas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    
    <div style="display:flex; flex-wrap:wrap; gap:12px; justify-content:center; margin-top:24px;">
        @if ($puedeEditar)
            <a href="{{ route('siben.editaCoordinadores') }}"
            class="boton__primario"
            style="padding:9px 18px; text-decoration:none;">
                Editar coordinadores
            </a>

            @if ($directivos->isEmpty())
                <a href="{{ route('siben.busquedaDirector') }}"
                class="boton__primario"
                style="padding:9px 18px; text-decoration:none;">
                    Activar director
                </a>
            @endif
        @endif

        <a href="{{ route('siben.menu_datos') }}"
        class="boton__secundario"
        style="padding:9px 18px; text-decoration:none;">
            Regresar
        </a>
    </div>
</div>
@endsection