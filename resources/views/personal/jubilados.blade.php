@extends('layouts.siben.siben')

@section('titulo', 'Lista de bajas de personal')

@section('content')
<section class="contenedor" style="max-width: 1250px; margin: 0 auto; padding: 24px 16px;">
    <h2 style="color: var(--principal); text-align:center; margin-bottom: 18px;">Lista de Bajas de Personal</h2>

    @if(session('mensaje_exito'))
        <div class="alert alert-success" style="margin-bottom: 14px;">{{ session('mensaje_exito') }}</div>
    @endif

    <div style="overflow-x:auto;" class="buscar01">
        <table style="width:100%; border-collapse: collapse; border:2px solid #b5164d;">
            <thead>
                <tr style="border-bottom:2px solid #b5164d;">
                    <th style="padding:6px;">No.</th>
                    <th style="padding:6px;">Nombre</th>
                    <th style="padding:6px;">Primer Apellido</th>
                    <th style="padding:6px;">Segundo Apellido</th>
                    <th style="padding:6px;">CURP</th>
                    <th style="padding:6px;">Función</th>
                    <th style="padding:6px;">Fecha Baja</th>
                    <th style="padding:6px;">Tipo</th>
                    <th style="padding:6px;">Pasar a Status X</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resultados as $i => $persona)
                    <tr style="border-bottom:1px solid #ddd;">
                        <td style="padding:6px;">{{ $i + 1 }}</td>
                        <td style="padding:6px;">{{ $persona->nombre }}</td>
                        <td style="padding:6px;">{{ $persona->apellido_paterno }}</td>
                        <td style="padding:6px;">{{ $persona->apellido_materno }}</td>
                        <td style="padding:6px;">{{ $persona->curp }}</td>
                        <td style="padding:6px;">{{ $funciones[$persona->id_funcion] ?? '' }}</td>
                        <td style="padding:6px;">{{ $fechas[$persona->id] ?? '' }}</td>
                        <td style="padding:6px;">{{ $tipos[$persona->status] ?? strtoupper($persona->status) }}</td>
                        <td style="padding:6px; text-align:center;">
                            <form method="POST" action="{{ route('personal.panel.reactivar') }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="id_personal" value="{{ $persona->id }}">
                                <button type="submit" class="boton__primario">SI</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding:10px; text-align:center;">No hay registros en lista de bajas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="display:flex; justify-content:center; gap:10px; margin-top:18px;">
        <a href="{{ route('personal.menu') }}" class="boton__secundario" style="text-decoration:none;">Anterior</a>
        <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration:none;">Inicio</a>
    </div>
</section>
@endsection
