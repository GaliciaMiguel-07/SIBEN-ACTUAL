@extends('layouts.siben.siben')

@section('titulo', 'Activar personal')

@section('content')
<section class="contenedor" style="max-width: 1100px; margin: 0 auto; padding: 24px 16px;">
    <h2 style="color: var(--principal); text-align:center; margin-bottom: 18px;">Activar personal</h2>

    @if(!empty($mensaje))
        <div class="alert alert-warning" style="margin-bottom: 14px;">{{ $mensaje }}</div>
    @endif
    @if(session('mensaje_exito'))
        <div class="alert alert-success" style="margin-bottom: 14px;">{{ session('mensaje_exito') }}</div>
    @endif

    <div class="buscar01" style="padding: 12px; margin-bottom: 16px;">
        <form method="POST" action="{{ route('personal.panel.index') }}">
            @csrf
            <table style="width:100%; border:2px solid #b5164d; border-collapse: collapse;">
                <tr>
                    <th colspan="6" style="text-align:left; padding:6px; border-bottom:2px solid #b5164d;">Busqueda de Personal</th>
                </tr>
                <tr>
                    <td style="padding:6px;">Primer Ap.:</td>
                    <td style="padding:6px;"><input type="text" name="busca[apellido_paterno]" value="{{ $criterios['apellido_paterno'] ?? '' }}" class="form-control" onblur="this.value=this.value.trim().toUpperCase()"></td>
                    <td style="padding:6px;">Segundo Ap.:</td>
                    <td style="padding:6px;"><input type="text" name="busca[apellido_materno]" value="{{ $criterios['apellido_materno'] ?? '' }}" class="form-control" onblur="this.value=this.value.trim().toUpperCase()"></td>
                    <td style="padding:6px;">Nombre:</td>
                    <td style="padding:6px;"><input type="text" name="busca[nombre]" value="{{ $criterios['nombre'] ?? '' }}" class="form-control" onblur="this.value=this.value.trim().toUpperCase()"></td>
                </tr>
                <tr>
                    <td style="padding:6px;">Función:</td>
                    <td style="padding:6px;">
                        <select name="busca[id_funcion]" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach($funciones as $id => $funcion)
                                <option value="{{ $id }}" {{ (string)($criterios['id_funcion'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $funcion }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding:6px;">CURP:</td>
                    <td style="padding:6px;"><input type="text" name="busca[curp]" value="{{ $criterios['curp'] ?? '' }}" class="form-control" onblur="this.value=this.value.trim().toUpperCase()"></td>
                    <td style="padding:6px;">Status:</td>
                    <td style="padding:6px;">
                        <select name="busca[status]" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach($statusCatalogo as $id => $status)
                                <option value="{{ $id }}" {{ (string)($criterios['status'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </table>

            <div style="text-align:center; margin-top:12px;">
                <button type="submit" class="boton__primario">Buscar</button>
            </div>
        </form>
    </div>

    @if(isset($resultados) && $resultados->count() > 0)
        <div style="overflow-x:auto;" class="buscar01">
            <table style="width:100%; border-collapse: collapse; border:2px solid #b5164d;">
                <thead>
                    <tr style="border-bottom:2px solid #b5164d;">
                        <th style="padding:6px;">No.</th>
                        <th style="padding:6px;">Nombre</th>
                        <th style="padding:6px;">Primer Apellido</th>
                        <th style="padding:6px;">Segundo Apellido</th>
                        <th style="padding:6px;">CURP</th>
                        <th style="padding:6px;">Edad</th>
                        <th style="padding:6px;">Género</th>
                        <th style="padding:6px;">Estado</th>
                        <th style="padding:6px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resultados as $i => $persona)
                        <tr style="border-bottom:1px solid #ddd;">
                            <td style="padding:6px;">{{ $i + 1 }}</td>
                            <td style="padding:6px;">{{ $persona->nombre }}</td>
                            <td style="padding:6px;">{{ $persona->apellido_paterno }}</td>
                            <td style="padding:6px;">{{ $persona->apellido_materno }}</td>
                            <td style="padding:6px;">{{ $persona->curp }}</td>
                            <td style="padding:6px;">{{ $persona->edad }}</td>
                            <td style="padding:6px; text-align:center;">{{ strtoupper($persona->genero) }}</td>
                            <td style="padding:6px; text-align:center; background-color:{{ $persona->status === 'a' ? '#1f8f3a' : '#d64141' }}; color:#fff;">
                                {{ strtoupper($persona->status) }}
                            </td>
                            <td style="padding:6px; text-align:center;">
                                @if((int)$nivel === 3)
                                    <a href="{{ route('personal.panel.edita', ['id' => $persona->id]) }}" class="boton__primario" style="text-decoration:none;">Editar</a>
                                @else
                                    <form method="POST" action="{{ route('personal.panel.status') }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="id_personal" value="{{ $persona->id }}">
                                        <input type="hidden" name="status" value="{{ $persona->status === 'a' ? 'x' : 'a' }}">
                                        <button type="submit" class="boton__primario">{{ $persona->status === 'a' ? 'Desactivar' : 'A' }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div style="display:flex; justify-content:center; gap:10px; margin-top:18px;">
        <a href="{{ route('personal.menu') }}" class="boton__secundario" style="text-decoration:none;">Anterior</a>
        <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration:none;">Inicio</a>
    </div>
</section>
@endsection
