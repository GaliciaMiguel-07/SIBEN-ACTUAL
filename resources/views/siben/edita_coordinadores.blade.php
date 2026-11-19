
@extends('layouts.siben.siben')

@section('titulo', 'Editar coordinadores')

@section('content')
<div class="contenedor" style="max-width:1000px; margin:0 auto; padding:32px 16px; width:100%;">

    <h2 class="patria">Asignar coordinadores por licenciatura</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los siguientes datos:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('siben.guardaCoordinadores') }}">
        @csrf

        @forelse ($escuelaLicenciaturas as $item)
            <div style="margin:20px 0; padding:16px; border:1px solid var(--neutro300); border-radius:8px;">
                <label for="coordinador_{{ $item->id_licenciatura }}">
                    <strong>{{ $item->licenciatura?->licenciatura ?? 'Licenciatura sin nombre' }}</strong>
                </label>

                <select
                    id="coordinador_{{ $item->id_licenciatura }}"
                    name="coordinadores[{{ $item->id_licenciatura }}]"
                    class="form-control"
                    style="margin-top:8px;"
                >
                    <option value="">Sin coordinador asignado</option>

                    @foreach ($coordinadores as $persona)
                        <option
                            value="{{ $persona->id }}"
                            @selected(old(
                                'coordinadores.'.$item->id_licenciatura,
                                $item->id_personal
                            ) == $persona->id && $persona->status === 'a')
                        >
                            {{ $persona->nombre }}
                            {{ $persona->apellido_paterno }}
                            {{ $persona->apellido_materno }}
                        </option>
                    @endforeach
                </select>
            </div>
        @empty
            <div class="alert alert-info">
                No hay licenciaturas registradas para esta escuela.
            </div>
        @endforelse

        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:24px;">
            @if ($escuelaLicenciaturas->isNotEmpty())
                <button type="submit" class="boton__primario" style="padding:10px 20px;">
                    Guardar coordinadores
                </button>
            @endif

            <a href="{{ route('siben.directoresEscuela') }}"
               class="boton__secundario"
               style="padding:10px 20px; text-decoration:none;">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
