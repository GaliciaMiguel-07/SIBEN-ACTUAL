@extends('layouts.siben.siben')

@section('titulo', 'Consulta histórico — Administrador')

@section('content')
<section class="contenedor" style="max-width: 980px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">
    <div class="buscar01" style="margin-bottom: 20px;">
        <div class="buscar01__hgroup" style="border-bottom: 1px solid var(--neutro400); margin-bottom: 16px; padding-bottom: 8px;">
            <h3 style="margin: 0; color: var(--neutro900);">Administrador</h3>
        </div>

        @if(!empty($mensaje))
            <div class="alert alert-danger" role="alert" style="margin-bottom: 16px;">{{ $mensaje }}</div>
        @endif

        <form method="POST" action="{{ route('consultas_historico.admin.buscar') }}" style="max-width: 620px; margin: 0 auto 18px;">
            @csrf
            <table style="width: 100%; border-collapse: collapse; border: 1px solid var(--principal);">
                <tr>
                    <th colspan="2" style="padding: 8px; text-align: left; border-bottom: 1px solid var(--principal);">Ingrese al menos un criterio de búsqueda</th>
                </tr>
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
                <tr>
                    <td style="padding: 6px;">Num. Identificador:</td>
                    <td style="padding: 6px;"><input class="form-control" type="text" name="nid" value="{{ $criterios['nid'] ?? '' }}" onblur="this.value=this.value.trim().toUpperCase()"></td>
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
                    <td style="padding: 6px;">Curp:</td>
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
                            <th style="padding: 8px;">NID</th>
                            <th style="padding: 8px;">Primer Apellido</th>
                            <th style="padding: 8px;">Segundo Apellido</th>
                            <th style="padding: 8px;">Nombre</th>
                            <th style="padding: 8px;">Curp</th>
                            <th style="padding: 8px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $regresarParams = collect($criterios ?? [])->filter(function ($v) { return $v !== null && $v !== ''; })->toArray();
                            $urlRegresar = route('consultas_historico.admin.buscar', $regresarParams);
                        @endphp
                        @foreach($resultados as $index => $alumno)
                            <tr style="border-bottom: 1px solid var(--neutro300);">
                                <td style="padding: 8px;">{{ $index + 1 }}</td>
                                <td style="padding: 8px;">{{ $alumno->nid }}</td>
                                <td style="padding: 8px;">{{ $alumno->apellido_paterno }}</td>
                                <td style="padding: 8px;">{{ $alumno->apellido_materno }}</td>
                                <td style="padding: 8px;">{{ $alumno->nombre }}</td>
                                <td style="padding: 8px;">{{ $alumno->curp }}</td>
                                <td style="padding: 8px; text-align: center;">
                                    <a href="{{ route('consultas_historico.detalle', ['nid' => $alumno->nid, 'regresar' => $urlRegresar]) }}" class="boton__primario" style="text-decoration: none; display: inline-block;">Detalle</a>
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
        const idEntidad = this.value;
        escuela.innerHTML = '<option value="">Seleccione</option>';
        if (!idEntidad) return;

        try {
            const url = "{{ route('consultas_historico.admin.escuelas', ['id_entidad' => '__ID__']) }}".replace('__ID__', idEntidad);
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await response.json();
            Object.entries(data).forEach(([id, nombre]) => {
                const opt = document.createElement('option');
                opt.value = id;
                opt.textContent = nombre;
                escuela.appendChild(opt);
            });
        } catch (e) {
            console.error(e);
        }
    });
})();
</script>
@endpush
