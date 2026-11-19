
@extends('layouts.siben.siben')

@section('titulo', 'Activar director')

@section('content')
<div class="contenedor" style="max-width:1000px; margin:0 auto; padding:32px 16px; width:100%;">

    <h2 class="patria">Activar director de la escuela</h2>

    @if ($directorActivo)
        <div class="alert alert-warning">
            Esta escuela ya tiene un director activo. Para activar otro, primero debe resolverse la situación del director actual.
        </div>
    @else
        <div class="alert alert-info">
            Selecciona al integrante del personal que será registrado como director.
        </div>

        @forelse ($personal as $persona)
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; padding:14px 0; border-bottom:1px solid var(--neutro300);">
                <div>
                    <strong>
                        {{ $persona->nombre }}
                        {{ $persona->apellido_paterno }}
                        {{ $persona->apellido_materno }}
                    </strong>
                    <div>{{ $persona->curp }}</div>
                    <small>{{ $persona->status === 'a' ? 'Activo' : 'Inactivo' }}</small>
                </div>

                <form method="POST" action="{{ route('siben.guardaDirector', ['id' => $persona->id]) }}"
                      onsubmit="return confirm('¿Confirmas activar a esta persona como director de la escuela?');">
                    @csrf
                    <button type="submit" class="boton__primario" style="padding:8px 16px;">
                        Activar
                    </button>
                </form>
            </div>
        @empty
            <div class="alert alert-info">
                No hay personal disponible para activar como director.
            </div>
        @endforelse
    @endif

    <div style="display:flex; justify-content:center; margin-top:24px;">
        <a href="{{ route('siben.directoresEscuela') }}"
           class="boton__secundario"
           style="padding:10px 20px; text-decoration:none;">
            Regresar
        </a>
    </div>
</div>
@endsection
