@extends('layouts.siben.siben')

@section('titulo', 'Directorio — Admin')

@section('content')
<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    <div style="margin-bottom: 20px;">
        <a href="{{ route('siben.index') }}" class="boton__secundario" style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">
            &larr; Inicio
        </a>
    </div>

    <div class="buscar01" style="margin-bottom: 24px; padding: 16px;">
        <h3 style="margin-top: 0; margin-bottom: 12px;">Directorio</h3>

        <form method="GET" action="{{ route('admin.directorio.index') }}" style="display: flex; gap: 10px; align-items: end; flex-wrap: wrap; margin-bottom: 12px;">
            <div>
                <label for="id_entidad" style="display:block; font-weight:600; margin-bottom: 6px;">Entidad</label>
                <select id="id_entidad" name="id_entidad" class="form-control" style="min-width: 260px;">
                    @foreach($entidades as $id => $nombre)
                        <option value="{{ $id }}" @selected((int)$idEntidad === (int)$id)>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="boton__primario" style="padding: 8px 16px;">Buscar</button>

            @if($idEntidad)
                <a href="{{ route('admin.directorio.exportar', ['id_entidad' => $idEntidad]) }}" class="boton__secundario" style="text-decoration:none; padding: 8px 16px;">
                    Descargar Excel
                </a>
            @endif
        </form>

        <p style="margin: 8px 0 0; color: #555; font-size: .92rem;">
            Puedes ajustar la jerarquía con <strong>Subir nivel</strong>/<strong>Bajar nivel</strong>. El Excel se genera con ese mismo orden.
        </p>
    </div>

    <div class="buscar01" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 980px;">
            <thead>
                <tr style="border-bottom: 2px solid var(--neutro400);">
                    <th style="text-align:left; padding: 10px;">Nombre</th>
                    <th style="text-align:left; padding: 10px;">Cargo</th>
                    <th style="text-align:left; padding: 10px;">Función</th>
                    <th style="text-align:left; padding: 10px;">Correo</th>
                    <th style="text-align:left; padding: 10px;">Dirección</th>
                    <th style="text-align:left; padding: 10px;">Teléfono</th>
                    <th style="text-align:center; padding: 10px; width: 240px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($directorio as $persona)
                    @php
                        $nombreCompleto = trim(implode(' ', array_filter([
                            $persona->titulo,
                            $persona->nombre,
                            $persona->apellido_paterno,
                            $persona->apellido_materno,
                        ])));
                    @endphp
                    <tr style="border-bottom: 1px solid var(--neutro300);">
                        <td style="padding: 10px; vertical-align: top;">{{ $nombreCompleto }}</td>
                        <td style="padding: 10px; vertical-align: top;">{{ $persona->cargo }}</td>
                        <td style="padding: 10px; vertical-align: top;">{{ $funciones[$persona->id_funcion] ?? 'Sin función' }}</td>
                        <td style="padding: 10px; vertical-align: top;">{{ $persona->correo_electronico }}</td>
                        <td style="padding: 10px; vertical-align: top;">{{ $persona->direccion }}</td>
                        <td style="padding: 10px; vertical-align: top;">({{ $persona->lada }}) {{ $persona->telefono }}</td>
                        <td style="padding: 10px;">
                            <div style="display:flex; gap:6px; justify-content:center; flex-wrap: wrap;">
                                <form method="POST" action="{{ route('admin.directorio.jerarquia', $persona->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="id_entidad" value="{{ $idEntidad }}">
                                    <input type="hidden" name="direccion" value="subir">
                                    <button type="submit" class="boton__secundario" style="padding: 4px 10px;">Subir nivel</button>
                                </form>

                                <form method="POST" action="{{ route('admin.directorio.jerarquia', $persona->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="id_entidad" value="{{ $idEntidad }}">
                                    <input type="hidden" name="direccion" value="bajar">
                                    <button type="submit" class="boton__secundario" style="padding: 4px 10px;">Bajar nivel</button>
                                </form>

                                <form method="POST" action="{{ route('admin.directorio.eliminar', $persona->id) }}" onsubmit="return confirm('¿Eliminar este registro del directorio?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id_entidad" value="{{ $idEntidad }}">
                                    <button type="submit" class="boton__primario" style="padding: 4px 10px; background:#b22222; border-color:#b22222;">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 16px; text-align: center; color: #666;">No hay registros para la entidad seleccionada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
