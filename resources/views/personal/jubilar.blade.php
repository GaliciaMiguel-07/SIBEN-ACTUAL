@extends('layouts.siben.siben')

@section('titulo', 'Tipo de baja')

@section('content')
<section class="contenedor" style="max-width: 760px; margin: 0 auto; padding: 24px 16px;">
    <h3 style="text-align:center; color:var(--principal); margin-bottom:14px;">Seleccione el motivo de la Baja de Personal</h3>

    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 14px;">
            <ul style="margin:0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('personal.panel.guardarBaja') }}">
        @csrf
        <input type="hidden" name="id_personal" value="{{ $personal->id }}">

        <table class="buscar01" style="width:100%; border-collapse: collapse; border:2px solid #b5164d; margin-bottom: 14px;">
            <tr>
                <th style="text-align:left; padding:6px; border-bottom:2px solid #b5164d;">Tipo</th>
            </tr>
            <tr>
                <td style="padding:6px;">
                    <select name="tipo" class="form-control" required>
                        <option value="">Seleccione</option>
                        @foreach($tipos as $id => $texto)
                            <option value="{{ $id }}">{{ $texto }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th style="text-align:left; padding:6px; border-top:2px solid #b5164d; border-bottom:2px solid #b5164d;">Fecha Baja</th>
            </tr>
            <tr>
                <td style="padding:6px;"><input type="date" name="fecha_baja" class="form-control" required></td>
            </tr>
        </table>

        <div style="display:flex; justify-content:center; gap:10px; margin-top: 16px;">
            <a href="{{ route('personal.panel.jubilacion') }}" class="boton__secundario" style="text-decoration:none;">Anterior</a>
            <button type="submit" class="boton__primario">Guardar</button>
            <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration:none;">Inicio</a>
        </div>
    </form>
</section>
@endsection
