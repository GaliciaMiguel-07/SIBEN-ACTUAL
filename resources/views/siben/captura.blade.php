@extends('layouts.siben.siben')

@section('titulo', 'Cambiar Captura — SIBEN')

@section('content')
<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 980px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    <div class="buscar01" style="margin-bottom: 24px;">
        <div class="buscar01__hgroup mb--24" style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 16px;">
            <h3 style="font-size: 2rem; font-weight: 700; margin: 0; color: var(--neutro800);">Administrador</h3>
        </div>

        <div style="text-align: center; margin-bottom: 24px;">
            <h2 class="patria" style="color: #007744; margin: 0;">Cambiar Captura</h2>
        </div>

        @if(session('ok'))
            <div class="alert alert-success" role="alert" style="margin-bottom: 18px;">{{ session('ok') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" role="alert" style="margin-bottom: 18px;">{{ session('error') }}</div>
        @endif

        <form method="GET" action="{{ route('siben.captura') }}" style="padding: 8px 0 14px 0; margin-bottom: 10px; text-align: center;">
            <label for="modulo" style="font-size: 2rem;">Modulo:</label>
            <select id="modulo" name="modulo" class="form-control" style="display: inline-block; width: 130px; margin-right: 16px;" onchange="this.form.submit()">
                <option value=""></option>
                <option value="l" {{ ($modulo ?? '') === 'l' ? 'selected' : '' }}>Licenciatura</option>
                <option value="p" {{ ($modulo ?? '') === 'p' ? 'selected' : '' }}>Posgrado</option>
            </select>

            <label for="id_entidad" style="font-size: 2rem;">Entidad:</label>
            <select id="id_entidad" name="id_entidad" class="form-control" style="display: inline-block; width: 220px;" onchange="this.form.submit()">
                <option value=""></option>
                @foreach($entidades as $id => $entidad)
                    <option value="{{ $id }}" {{ (string)($id_entidad ?? '') === (string)$id ? 'selected' : '' }}>{{ $entidad }}</option>
                @endforeach
            </select>
        </form>

        <div style="padding: 4px 8px 10px 8px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; border: 2px solid #bb133f;">
                <thead>
                    <tr style="border-bottom: 2px solid #bb133f;">
                        <th style="padding: 10px 8px; width: 70px;">No.</th>
                        <th style="padding: 10px 8px;">Nombre</th>
                        <th style="padding: 10px 8px; width: 150px;">Clave</th>
                        <th style="padding: 10px 8px; width: 140px; text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($escuelas as $index => $escuela)
                        @php $estaAbierta = (string)$escuela->captura === '1'; @endphp
                        <tr style="border-bottom: 1px solid var(--neutro300);">
                            <td style="padding: 10px 8px;">{{ $index + 1 }}</td>
                            <td style="padding: 10px 8px;">{{ $escuela->escuela }}</td>
                            <td style="padding: 10px 8px;">{{ $escuela->escuela_key }}</td>
                            <td style="padding: 10px 8px; text-align: center;">
                                <form method="POST" action="{{ route('siben.cambia_captura') }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="id_escuela" value="{{ $escuela->id }}">
                                    <input type="hidden" name="id_entidad" value="{{ $escuela->id_entidad }}">
                                    <input type="hidden" name="modulo" value="{{ $escuela->modulo }}">
                                    <button type="submit" class="boton__secundario" style="min-width: 92px;">
                                        {{ $estaAbierta ? 'Cerrar' : 'Abrir' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 18px 14px; color: var(--neutro600); text-align: center;">
                                @if(!empty($modulo) && !empty($id_entidad))
                                    No se encontraron escuelas activas para la entidad y módulo seleccionados.
                                @else
                                    Seleccione modulo y entidad para consultar escuelas.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="text-align: center; margin-top: 8px;">
        <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration: none; min-width: 110px; display: inline-block;">Inicio</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd.js"></script>
@endpush
