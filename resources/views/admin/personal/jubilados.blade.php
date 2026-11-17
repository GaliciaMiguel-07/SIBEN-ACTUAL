
@extends('layouts.siben.siben')

@section('title', 'Jubilados — SIBEN')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Personal Jubilado</h4>
        </div>

        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4 mb-3">
                    <label for="id_entidad">Entidad</label>
                    <select id="id_entidad" class="form-control">
                        <option value="">Seleccione una entidad</option>
                        @foreach($entidades as $id => $entidad)
                            <option value="{{ $id }}"
                                {{ (string)$entidad_id === (string)$id ? 'selected' : '' }}>
                                {{ $entidad }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5 mb-3">
                    <label for="id_escuela">Escuela</label>
                    <select id="id_escuela" class="form-control" disabled>
                        <option value="">Seleccione primero una entidad</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <button type="button" id="btnContinuar"
                        class="btn btn-primary btn-block" disabled>
                        Continuar
                    </button>
                </div>
            </div>

            <div id="mensajeJubilados" class="mt-3"></div>
            <div id="contenedorJubilados" class="mt-3"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const entidad = document.getElementById('id_entidad');
    const escuela = document.getElementById('id_escuela');
    const continuar = document.getElementById('btnContinuar');
    const contenedor = document.getElementById('contenedorJubilados');
    const mensaje = document.getElementById('mensajeJubilados');

    const urlEscuelas = @json(route('admin.personal.escuelas'));
    const urlLista = @json(route('admin.personal.jubilados.lista'));
    const urlActivar = @json(route('admin.personal.jubilados.activar'));
    const token = @json(csrf_token());

    async function cargarEscuelas(escuelaSeleccionada = '') {
        escuela.innerHTML = '<option value="">Cargando escuelas...</option>';
        escuela.disabled = true;
        continuar.disabled = true;
        contenedor.innerHTML = '';
        mensaje.innerHTML = '';

        if (!entidad.value) {
            escuela.innerHTML = '<option value="">Seleccione primero una entidad</option>';
            return;
        }

        try {
            const respuesta = await fetch(
                `${urlEscuelas}?entidad=${encodeURIComponent(entidad.value)}`
            );

            if (!respuesta.ok) throw new Error('No fue posible cargar las escuelas.');

            const escuelas = await respuesta.json();

            escuela.innerHTML = '<option value="">Seleccione una escuela</option>';

            Object.entries(escuelas).forEach(([id, nombre]) => {
                const opcion = document.createElement('option');
                opcion.value = id;
                opcion.textContent = nombre;

                if (String(id) === String(escuelaSeleccionada)) {
                    opcion.selected = true;
                }

                escuela.appendChild(opcion);
            });

            escuela.disabled = false;
            continuar.disabled = !escuela.value;

            if (Object.keys(escuelas).length === 0) {
                escuela.innerHTML = '<option value="">No hay escuelas disponibles</option>';
            }
        } catch (error) {
            escuela.innerHTML = '<option value="">Error al cargar escuelas</option>';
            mensaje.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
        }
    }

    async function cargarJubilados() {
        if (!escuela.value) {
            mensaje.innerHTML = '<div class="alert alert-warning">Seleccione una escuela.</div>';
            return;
        }

        continuar.disabled = true;
        mensaje.innerHTML = '<div class="alert alert-info">Consultando personal jubilado...</div>';
        contenedor.innerHTML = '';

        try {
            const respuesta = await fetch(
                `${urlLista}?escuela=${encodeURIComponent(escuela.value)}`
            );

            const html = await respuesta.text();

            if (!respuesta.ok) {
                throw new Error(html || 'No fue posible consultar los jubilados.');
            }

            contenedor.innerHTML = html;
            mensaje.innerHTML = '';
        } catch (error) {
            mensaje.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
        } finally {
            continuar.disabled = false;
        }
    }

    entidad.addEventListener('change', () => cargarEscuelas());

    escuela.addEventListener('change', function () {
        continuar.disabled = !escuela.value;
        contenedor.innerHTML = '';
        mensaje.innerHTML = '';
    });

    continuar.addEventListener('click', cargarJubilados);

    contenedor.addEventListener('click', async function (event) {
        const boton = event.target.closest('.btn-reactivar-jubilado');
        if (!boton) return;

        const idPersonal = boton.dataset.id;

        if (!confirm('¿Desea reactivar a este trabajador?')) return;

        boton.disabled = true;
        boton.textContent = 'Procesando...';

        try {
            const respuesta = await fetch(urlActivar, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    id_personal: idPersonal,
                    escuela: escuela.value
                })
            });

            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.ok) {
                throw new Error(resultado.mensaje || 'No fue posible reactivar al trabajador.');
            }

            mensaje.innerHTML =
                `<div class="alert alert-success">${resultado.mensaje}</div>`;

            await cargarJubilados();
        } catch (error) {
            mensaje.innerHTML =
                `<div class="alert alert-danger">${error.message}</div>`;
            boton.disabled = false;
            boton.textContent = 'Reactivar';
        }
    });

    if (entidad.value) {
        cargarEscuelas(@json($escuela_id));
    }
});
</script>
@endsection