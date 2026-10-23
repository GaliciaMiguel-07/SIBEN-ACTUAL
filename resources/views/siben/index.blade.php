@extends('layouts.siben.siben')

@section('titulo', 'Inicio — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 900px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    {{-- Mensajes flash de acciones (2FA, cambio de contraseña, etc.) --}}
    @if (session('ok'))
        <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
            {{ session('ok') }}
        </div>
    @endif
    @if (session('error'))
        <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Botón de regreso (si aplica) --}}
    @isset($anterior)
        <div style="margin-bottom: 20px;">
            <a href="{{ $anterior }}" class="boton__secundario" style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">&larr; Regresar</a>
        </div>
    @endisset

    {{-- Inclusión del menú de opciones del sistema --}}
    @include('layouts.siben.menu')

    {{-- Enlace de ayuda institucional para niveles distintos de 1 --}}
    @if (($nivel ?? 0) != 0 && $nivel != 1)
        <div style="text-align: right; margin-top: 24px; margin-bottom: 20px;">
            <a href="http://www.siben.sep.gob.mx/menu_ayuda/index.php"
               target="_blank"
               style="color: var(--principal, #811331); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                <span style="border: 1px solid var(--principal, #811331); border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 18px; font-size: 0.85rem;">?</span> Ayuda
            </a>
        </div>
    @endif

</div>

@endsection