@extends('layouts.siben.siben')

@section('titulo', 'Captura de personal')

@section('content')
<section class="contenedor" style="max-width: 900px; margin: 0 auto; padding: 24px 16px; text-align:center;">
    <h2 style="color: var(--principal); margin-bottom: 16px;">Elija la función:</h2>

    @if(!empty($mensaje))
        <div class="alert alert-warning" style="margin-bottom: 14px;">{{ $mensaje }}</div>
    @endif

    <form method="POST" action="{{ route('personal.panel.agrega2') }}" style="display:inline-flex; gap:8px; align-items:center;">
        @csrf
        <select name="id_funcion" class="form-control" style="min-width: 360px;" required>
            <option value="">Seleccione</option>
            @foreach($funciones as $id => $funcion)
                <option value="{{ $id }}">{{ $funcion }}</option>
            @endforeach
        </select>
        <button type="submit" class="boton__primario">Agregar</button>
    </form>

    <div style="display:flex; justify-content:center; gap:10px; margin-top:26px;">
        <a href="{{ route('personal.menu') }}" class="boton__secundario" style="text-decoration:none;">Anterior</a>
        <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration:none;">Inicio</a>
    </div>
</section>
@endsection
