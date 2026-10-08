@extends('layouts.siben.siben')

@section('titulo', 'Edición/Activar Personal — SIBEN')

@section('content')

<div id="area_contenido" style="margin-left:170px; padding:10px;">

    <a href="{{ route('admin.index') }}"
       style="display:inline-block; margin-bottom:12px; padding:6px 12px; background:#611232; color:#fff; text-decoration:none; border-radius:4px;">
        &larr; Panel
    </a>

    <div class="panel-titulo">Edición / Activar de Personal</div>

    @if(session('success'))
        <div class="alerta-exito">{{ session('success') }}</div>
    @endif

    {{-- Escuela --}}
    <table class="datos">
        <tr><th colspan="2">Escuela</th></tr>
        <tr>
            <td>Entidad:</td>
            <td>
                <select name="id_entidad" id="id_entidad">
                    <option value="">Seleccione</option>
                    @foreach($entidades as $id => $entidad)
                        <option value="{{ $id }}" @selected((int)$entidad_id === (int)$id)>{{ $entidad }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td>Escuela:</td>
            <td>
                <select name="id_escuela" id="id_escuela" disabled>
                    <option value="">Primero seleccione la entidad</option>
                </select>
            </td>
        </tr>
    </table>

    <br>

    {{-- Filtros --}}
    <table class="datos">
        <tr><th colspan="3">Filtros de búsqueda</th></tr>
        <tr>
            <td>Buscar (nombre / apellidos / CURP):</td>
            <td><input type="text" id="buscaTexto" size="30"></td>
            <td>
                <select id="buscaFuncion">
                    <option value="">Todas las funciones</option>
                    @foreach($funciones as $id => $funcion)
                        <option value="{{ $id }}">{{ $funcion }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <button type="button" onclick="cargarLista()">Buscar</button>
            </td>
        </tr>
    </table>

    <br>

    <div id="contenedorLista">
        <p>Seleccione una escuela para ver su personal.</p>
    </div>

</div>

<script>
    const TOKEN    = '{{ csrf_token() }}';
    const urlLista = '{{ route('admin.personal.lista') }}';

    // Cargar escuelas de una entidad y (opcional) preseleccionar una
    function cargarEscuelas(entidad, seleccionada) {
        const escuela = document.getElementById('id_escuela');
        if (!entidad) {
            escuela.innerHTML = '<option value="">Primero seleccione la entidad</option>';
            escuela.disabled = true;
            document.getElementById('contenedorLista').innerHTML = '<p>Seleccione una escuela para ver su personal.</p>';
            return;
        }
        escuela.innerHTML = '<option value="">Cargando…</option>';
        fetch('{{ route('admin.personal.escuelas') }}?entidad=' + entidad)
            .then(r => r.json())
            .then(data => {
                let opciones = '<option value="">Seleccione la escuela</option>';
                for (const [id, nombre] of Object.entries(data)) {
                    opciones += '<option value="' + id + '"' +
                        (String(seleccionada) === String(id) ? ' selected' : '') +
                        '>' + nombre + '</option>';
                }
                escuela.innerHTML = opciones;
                escuela.disabled = false;
                if (seleccionada && data[seleccionada]) {
                    cargarLista();
                }
            });
    }

    // Cargar la tabla del personal
    function cargarLista() {
        const escuela = document.getElementById('id_escuela').value;
        const texto   = document.getElementById('buscaTexto').value.trim();
        const funcion = document.getElementById('buscaFuncion').value;
        if (!escuela) return;

        document.getElementById('contenedorLista').innerHTML = '<p>Cargando personal…</p>';

        let params = 'escuela=' + encodeURIComponent(escuela);
        if (texto)   params += '&busca[texto]='   + encodeURIComponent(texto);
        if (funcion) params += '&busca[funcion]=' + encodeURIComponent(funcion);

        fetch(urlLista + '?' + params)
            .then(r => {
                if (!r.ok) { throw new Error('HTTP ' + r.status); }
                return r.text();
            })
            .then(html => { document.getElementById('contenedorLista').innerHTML = html; })
            .catch(e => {
                document.getElementById('contenedorLista').innerHTML =
                    '<p style="color:#721c24;background:#f8d7da;padding:8px;border:1px solid #f5c6cb;border-radius:4px;">' +
                    'Error al cargar la lista: ' + e.message + '. Revisa el archivo storage\\logs\\laravel.log y mándame la primera línea del último error.</p>';
            });
    }

    // Activar / desactivar y recargar la tabla
    function cambiaStatus(id) {
        fetch('{{ route('admin.personal.status') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': TOKEN, 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        })
        .then(r => r.json())
        .then(() => cargarLista());
    }

    // Eventos
    document.getElementById('id_entidad').addEventListener('change', function () {
        cargarEscuelas(this.value, null);
    });
    document.getElementById('id_escuela').addEventListener('change', function () {
        cargarLista();
    });

    // Carga inicial si venimos de una edición (entidad + escuela en la URL)
    const entidadInicial = '{{ $entidad_id }}';
    const escuelaInicial = '{{ $escuela_id }}';
    if (entidadInicial) {
        cargarEscuelas(entidadInicial, escuelaInicial);
    }
</script>

@endsection

<style>
    .panel-titulo { font-size: 22px; font-weight: 600; color: #611232; border-bottom: 3px solid #611232; padding-bottom: 8px; margin-bottom: 20px; }
    table.datos { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
    table.datos th { background: #611232; color: #fff; padding: 8px 12px; text-align: left; border: 1px solid #611232; }
    table.datos td { padding: 8px 12px; border: 1px solid #ddd; }
    .alerta-exito { background: #d4edda; color: #155724; padding: 10px 14px; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 15px; }
</style>