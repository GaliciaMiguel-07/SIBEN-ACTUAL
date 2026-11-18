@extends('layouts.siben.siben')

@section('titulo', 'Consulta personal')

@section('content')
<section class="contenedor" style="max-width: 980px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">
    <div class="buscar01" style="margin-bottom: 20px;">
        <div class="buscar01__hgroup" style="border-bottom: 1px solid var(--neutro400); margin-bottom: 16px; padding-bottom: 8px;">
            <h3 style="margin: 0; color: var(--neutro900);">Consulta personal</h3>
        </div>

        @if(!empty($mensaje))
            <div class="alert alert-danger" role="alert" style="margin-bottom: 16px;">{{ $mensaje }}</div>
        @endif

        <form method="POST" action="{{ route('consultas_personal.buscar') }}" style="max-width: 680px; margin: 0 auto 18px;">
            @csrf
            <table style="width: 100%; border-collapse: collapse; border: 1px solid var(--principal);">
                <tr>
                    <th colspan="2" style="padding: 8px; text-align: left; border-bottom: 1px solid var(--principal);">Ingrese al menos un criterio de búsqueda</th>
                </tr>

                @if($nivel === 1)
                    <tr>
                        <td style="padding: 6px; width: 38%;">Entidad:</td>
                        <td style="padding: 6px;">
                            <select id="id_entidad" name="id_entidad" class="form-control">
                                <option value="">Seleccione</option>
                                @foreach($entidades as $id => $entidad)
                                    <option value="{{ $id }}" {{ (string)($criterios['id_entidad'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $entidad }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px;">Escuela:</td>
                        <td style="padding: 6px;">
                            <select id="id_escuela" name="id_escuela" class="form-control">
                                <option value="">Seleccione</option>
                                @foreach($escuelas as $id => $escuela)
                                    <option value="{{ $id }}" {{ (string)($criterios['id_escuela'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $escuela }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @elseif($nivel === 2)
                    <tr>
                        <td style="padding: 6px; width: 38%;">Entidad:</td>
                        <td style="padding: 6px;">
                            <input type="text" class="form-control" value="{{ $entidadFija }}" readonly>
                            <input type="hidden" name="id_entidad" value="{{ $criterios['id_entidad'] }}">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px;">Escuela:</td>
                        <td style="padding: 6px;">
                            <select id="id_escuela" name="id_escuela" class="form-control">
                                <option value="">Seleccione</option>
                                @foreach($escuelas as $id => $escuela)
                                    <option value="{{ $id }}" {{ (string)($criterios['id_escuela'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $escuela }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td style="padding: 6px; width: 38%;">Entidad:</td>
                        <td style="padding: 6px;">
                            <input type="text" class="form-control" value="{{ $entidadFija }}" readonly>
                            <input type="hidden" name="id_entidad" value="{{ $criterios['id_entidad'] }}">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px;">Escuela:</td>
                        <td style="padding: 6px;">
                            <input type="text" class="form-control" value="{{ $escuelaFija }}" readonly>
                            <input type="hidden" name="id_escuela" value="{{ $criterios['id_escuela'] }}">
                        </td>
                    </tr>
                @endif

                <tr>
                    <td style="padding: 6px;">Función:</td>
                    <td style="padding: 6px;">
                        <select name="id_funcion" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach($funciones as $id => $funcion)
                                <option value="{{ $id }}" {{ (string)($criterios['id_funcion'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $funcion }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px;">Status:</td>
                    <td style="padding: 6px;">
                        <select name="status" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach($statusCatalogo as $id => $status)
                                <option value="{{ $id }}" {{ (string)($criterios['status'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px;">Primer Apellido:</td>
                    <td style="padding: 6px;"><input class="form-control" type="text" name="apellido_paterno" value="{{ $criterios['apellido_paterno'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td>
                </tr>
                <tr>
                    <td style="padding: 6px;">Segundo Apellido:</td>
                    <td style="padding: 6px;"><input class="form-control" type="text" name="apellido_materno" value="{{ $criterios['apellido_materno'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td>
                </tr>
                <tr>
                    <td style="padding: 6px;">Nombre(s):</td>
                    <td style="padding: 6px;"><input class="form-control" type="text" name="nombre" value="{{ $criterios['nombre'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td>
                </tr>
                <tr>
                    <td style="padding: 6px;">CURP:</td>
                    <td style="padding: 6px;"><input class="form-control" type="text" name="curp" value="{{ $criterios['curp'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td>
                </tr>
            </table>

            <div style="text-align: center; margin-top: 14px; display: flex; gap: 10px; justify-content: center;">
                <button type="submit" class="boton__primario">Buscar</button>
                <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration: none; display: inline-block;">Inicio</a>
            </div>
        </form>

        @if(isset($resultados) && $resultados->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; border: 1px solid var(--principal);">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--principal);">
                            <th style="padding: 8px;">No.</th>
                            <th style="padding: 8px;">ID</th>
                            <th style="padding: 8px;">Primer Apellido</th>
                            <th style="padding: 8px;">Segundo Apellido</th>
                            <th style="padding: 8px;">Nombre</th>
                            <th style="padding: 8px;">CURP</th>
                            <th style="padding: 8px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $regresar = request()->fullUrl();
                        @endphp
                        @foreach($resultados as $index => $fila)
                            <tr style="border-bottom: 1px solid var(--neutro300);">
                                <td style="padding: 8px;">{{ $index + 1 }}</td>
                                <td style="padding: 8px;">{{ $fila->id }}</td>
                                <td style="padding: 8px;">{{ $fila->apellido_paterno }}</td>
                                <td style="padding: 8px;">{{ $fila->apellido_materno }}</td>
                                <td style="padding: 8px;">{{ $fila->nombre }}</td>
                                <td style="padding: 8px;">{{ $fila->curp }}</td>
                                <td style="padding: 8px; text-align: center;">
                                    <a href="{{ route('consultas_personal.detalle', ['id' => $fila->id, 'regresar' => $regresar]) }}" class="boton__primario" style="text-decoration: none; display: inline-block;">Detalle</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    const entidad = document.getElementById('id_entidad');
    const escuela = document.getElementById('id_escuela');
    if (!entidad || !escuela) return;

    entidad.addEventListener('change', async function () {
        escuela.innerHTML = '<option value="">Seleccione</option>';
        if (!this.value) return;

        try {
            const url = "{{ route('consultas_personal.escuelas', ['id_entidad' => '__ID__']) }}".replace('__ID__', this.value);
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await response.json();

            Object.entries(data).forEach(([id, nombre]) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = nombre;
                escuela.appendChild(option);
            });
        } catch (error) {
            console.error(error);
        }
    });
})();
</script>
@endpush
