@extends('layouts.siben.siben')

@section('titulo', 'Edición/Activar Personal — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 1000px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    <!-- Botón de regreso al panel -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.index') }}" class="boton__secundario" style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">
            &larr; Panel
        </a>
    </div>

    <!-- Tarjeta contenedora principal -->
    <div class="buscar01" style="margin-bottom: 24px; padding: 24px; background: #fff; border: 1px solid var(--neutro400); border-radius: 4px;">

        <div style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 20px;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">Edición / Activar de Personal</h3>
        </div>

        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Escuela --}}
        <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">
            <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                Escuela
            </div>
            <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; background-color: #fff;">
                <div>
                    <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Entidad:</label>
                    <select name="id_entidad" id="id_entidad" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Seleccione</option>
                        @foreach($entidades as $id => $entidad)
                            <option value="{{ $id }}" @selected((int)$entidad_id === (int)$id)>{{ $entidad }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Escuela:</label>
                    <select name="id_escuela" id="id_escuela" disabled style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Primero seleccione la entidad</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">
            <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                Filtros de búsqueda
            </div>
            <div style="padding: 16px; display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; background-color: #fff;">
                <div style="flex: 1; min-width: 240px;">
                    <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Buscar (nombre / apellidos / CURP):</label>
                    <input type="text" id="buscaTexto" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                </div>
                <div style="flex: 1; min-width: 220px;">
                    <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Función:</label>
                    <select id="buscaFuncion" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Todas las funciones</option>
                        @foreach($funciones as $id => $funcion)
                            <option value="{{ $id }}">{{ $funcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="button" onclick="cargarLista()" class="boton__primario" style="padding: 8px 20px; font-size: 0.9rem; cursor: pointer;">Buscar</button>
                </div>
            </div>
        </div>

        <div id="contenedorLista" style="margin-top: 20px;">
            <p style="color: var(--neutro600); text-align: center; padding: 20px;">Seleccione una escuela para ver su personal.</p>
        </div>

    </div>

</div>

@endsection

@push('styles')
<link href="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd-guinda.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd.js"></script>
<script>
    const TOKEN    = '{{ csrf_token() }}';
    const urlLista = '{{ route('admin.personal.lista') }}';

    // Cargar escuelas de una entidad y (opcional) preseleccionar una
    function cargarEscuelas(entidad, seleccionada) {
        const escuela = document.getElementById('id_escuela');
        if (!entidad) {
            escuela.innerHTML = '<option value="">Primero seleccione la entidad</option>';
            escuela.disabled = true;
            document.getElementById('contenedorLista').innerHTML = '<p style="color: var(--neutro600); text-align: center; padding: 20px;">Seleccione una escuela para ver su personal.</p>';
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

        document.getElementById('contenedorLista').innerHTML = '<p style="text-align: center; padding: 20px;">Cargando personal…</p>';

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
                    '<p style="color:#721c24;background:#f8d7da;padding:12px;border:1px solid #f5c6cb;border-radius:4px;">' +
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
@endpush