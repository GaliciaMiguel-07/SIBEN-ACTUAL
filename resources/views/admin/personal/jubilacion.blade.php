@extends('layouts.siben.siben')

@section('titulo', 'Jubilar Personal — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 1000px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    {{-- Regresar al panel --}}
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.index') }}"
           class="boton__secundario"
           style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">
            &larr; Panel
        </a>
    </div>

    <div class="buscar01" style="margin-bottom: 24px; padding: 24px; background: #fff; border: 1px solid var(--neutro400); border-radius: 4px;">

        <div style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 20px;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">
                Jubilación y bajas de Personal
            </h3>

            <p style="color: var(--neutro600); margin: 8px 0 0; font-size: 0.9rem;">
                Seleccione una escuela para consultar al personal disponible para este movimiento.
            </p>
        </div>

        {{-- Escuela --}}
        <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">

            <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                Escuela
            </div>

            <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; background-color: #fff;">

                <div>
                    <label for="id_entidad" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">
                        Entidad:
                    </label>

                    <select name="id_entidad" id="id_entidad"
                            style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Seleccione</option>

                        @foreach($entidades as $id => $entidad)
                            <option value="{{ $id }}" @selected((int)$entidad_id === (int)$id)>
                                {{ $entidad }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="id_escuela" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">
                        Escuela:
                    </label>

                    <select name="id_escuela" id="id_escuela" disabled
                            style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
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
                    <label for="buscaTexto" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">
                        Buscar (nombre / apellidos / CURP):
                    </label>

                    <input type="text" id="buscaTexto"
                           style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                </div>

                <div style="flex: 1; min-width: 220px;">
                    <label for="buscaFuncion" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">
                        Función:
                    </label>

                    <select id="buscaFuncion"
                            style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Todas las funciones</option>

                        @foreach($funciones as $id => $funcion)
                            <option value="{{ $id }}">{{ $funcion }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="button"
                            onclick="cargarListaJubilacion()"
                            class="boton__primario"
                            style="padding: 8px 20px; font-size: 0.9rem; cursor: pointer;">
                        Buscar
                    </button>
                </div>

            </div>
        </div>

        {{-- Resultados --}}
        <div id="contenedorLista" style="margin-top: 20px;">
            <p style="color: var(--neutro600); text-align: center; padding: 20px;">
                Seleccione una escuela para consultar el personal disponible.
            </p>
        </div>

    </div>
</div>

{{-- Modal de jubilación --}}
<div id="modalJubilacion"
     style="display: none; position: fixed; z-index: 1000; inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center; justify-content: center;">

    <div style="background: white; width: 90%; max-width: 500px;
                border-radius: 5px; overflow: hidden;">

        <div style="background: #611232; color: white; padding: 14px 20px;
                    font-weight: 600; display: flex;
                    justify-content: space-between; align-items: center;">

            <span>Fecha de jubilación</span>

            <button type="button"
                    onclick="cerrarModalJubilacion()"
                    style="background: none; border: none; color: white;
                           font-size: 22px; cursor: pointer;">
                &times;
            </button>
        </div>

        <div style="padding: 24px;">

            <p id="nombrePersonalJubilacion"
               style="margin-top: 0; font-weight: 600;">
            </p>

            <input type="hidden" id="idPersonalJubilacion">

            <div id="mensajeJubilacion"
                style="display:none; margin-bottom:16px; padding:10px; border-radius:4px;">
            </div>

            <div style="display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 12px;
                        margin-bottom: 24px;">

                <div>
                    <label for="anioJubilacion">Año:</label>
                    <select id="anioJubilacion"
                            style="width: 100%; padding: 8px;">
                        <option value="">Año</option>
                    </select>
                </div>

                <div>
                    <label for="mesJubilacion">Mes:</label>
                    <select id="mesJubilacion"
                            style="width: 100%; padding: 8px;">
                        <option value="">Mes</option>
                    </select>
                </div>

                <div>
                    <label for="diaJubilacion">Día:</label>
                    <select id="diaJubilacion"
                            style="width: 100%; padding: 8px;">
                        <option value="">Día</option>
                    </select>
                </div>

            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">

                <button type="button"
                        class="boton__secundario"
                        onclick="cerrarModalJubilacion()">
                    Cancelar
                </button>

                <button type="button"
                        id="btnGuardarJubilacion"
                        class="boton__primario"
                        disabled>
                    Guardar
                </button>

            </div>

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

    const urlListaJubilacion = '{{ route('admin.personal.jubilacion.lista') }}';
    const urlGuardarJubilacion = '{{ route('admin.personal.jubilacion.guardar') }}';
    const tokenCSRF = '{{ csrf_token() }}';

    {{-- Cargar escuelas de la entidad seleccionada --}}
    function cargarEscuelas(entidad, seleccionada = null) {

        const escuela = document.getElementById('id_escuela');
        const contenedor = document.getElementById('contenedorLista');

        if (!entidad) {
            escuela.innerHTML = '<option value="">Primero seleccione la entidad</option>';
            escuela.disabled = true;

            contenedor.innerHTML =
                '<p style="text-align:center;padding:20px;">Seleccione una escuela para consultar el personal.</p>';

            return;
        }

        escuela.innerHTML = '<option value="">Cargando...</option>';
        escuela.disabled = true;

        fetch('{{ route('admin.personal.escuelas') }}?entidad=' + encodeURIComponent(entidad))
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                return response.json();
            })
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
                    cargarListaJubilacion();
                }
            })
            .catch(error => {

                escuela.innerHTML = '<option value="">Error al cargar escuelas</option>';
                escuela.disabled = true;

                contenedor.innerHTML =
                    '<p style="color:#721c24;padding:12px;">No fue posible cargar las escuelas: ' +
                    error.message + '</p>';
            });
    }

    {{-- Consultar personal --}}
    function cargarListaJubilacion() {

        const escuela = document.getElementById('id_escuela').value;
        const texto = document.getElementById('buscaTexto').value.trim();
        const funcion = document.getElementById('buscaFuncion').value;
        const contenedor = document.getElementById('contenedorLista');

        if (!escuela) {
            contenedor.innerHTML =
                '<p style="text-align:center;padding:20px;">Primero seleccione una escuela.</p>';

            return;
        }

        contenedor.innerHTML =
            '<p style="text-align:center;padding:20px;">Consultando personal...</p>';

        const params = new URLSearchParams();

        params.append('escuela', escuela);

        if (texto) {
            params.append('busca[texto]', texto);
        }

        if (funcion) {
            params.append('busca[funcion]', funcion);
        }

        fetch(urlListaJubilacion + '?' + params.toString())
            .then(response => {

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                return response.text();
            })
            .then(html => {
                contenedor.innerHTML = html;
            })
            .catch(error => {

                contenedor.innerHTML =
                    '<p style="color:#721c24;background:#f8d7da;padding:12px;border:1px solid #f5c6cb;border-radius:4px;">' +
                    'Error al consultar el personal: ' + error.message +
                    '</p>';
            });
    }

    {{-- Eventos de entidad y escuela --}}
    document.getElementById('id_entidad').addEventListener('change', function () {
        cargarEscuelas(this.value);
    });

    document.getElementById('id_escuela').addEventListener('change', function () {
        cargarListaJubilacion();
    });

    {{-- Carga inicial --}}
    const entidadInicial = '{{ $entidad_id }}';
    const escuelaInicial = '{{ $escuela_id }}';

    if (entidadInicial) {
        cargarEscuelas(entidadInicial, escuelaInicial);
    }

    {{-- Elementos del modal --}}
    const modalJubilacion = document.getElementById('modalJubilacion');
    const idPersonalJubilacion = document.getElementById('idPersonalJubilacion');
    const nombrePersonalJubilacion = document.getElementById('nombrePersonalJubilacion');

    const anioJubilacion = document.getElementById('anioJubilacion');
    const mesJubilacion = document.getElementById('mesJubilacion');
    const diaJubilacion = document.getElementById('diaJubilacion');
    const btnGuardarJubilacion = document.getElementById('btnGuardarJubilacion');

    {{-- Abrir modal --}}
    function abrirModalJubilacion(id, nombre) {

        idPersonalJubilacion.value = id;
        nombrePersonalJubilacion.textContent = nombre;

        document.getElementById('mensajeJubilacion').style.display = 'none';

        modalJubilacion.style.display = 'flex';

        validarFechaJubilacion();
    }

    {{-- Cerrar modal --}}
    function cerrarModalJubilacion() {

        modalJubilacion.style.display = 'none';

        idPersonalJubilacion.value = '';
        nombrePersonalJubilacion.textContent = '';

        anioJubilacion.value = '';
        mesJubilacion.value = '';
        diaJubilacion.value = '';

        validarFechaJubilacion();
    }

    {{-- Habilitar Guardar solamente cuando la fecha esté completa --}}
    function validarFechaJubilacion() {

        btnGuardarJubilacion.disabled = !(
            idPersonalJubilacion.value &&
            anioJubilacion.value &&
            mesJubilacion.value &&
            diaJubilacion.value
        );
    }

    {{-- Cargar años --}}
    const anioActual = new Date().getFullYear();

    for (let anio = anioActual; anio >= 1900; anio--) {

        const opcion = document.createElement('option');

        opcion.value = anio;
        opcion.textContent = anio;

        anioJubilacion.appendChild(opcion);
    }

    {{-- Cargar meses --}}
    for (let mes = 1; mes <= 12; mes++) {

        const opcion = document.createElement('option');

        opcion.value = String(mes).padStart(2, '0');
        opcion.textContent = String(mes).padStart(2, '0');

        mesJubilacion.appendChild(opcion);
    }

    {{-- Cargar días según el mes y el año --}}
    function cargarDiasJubilacion() {

        const diaSeleccionado = diaJubilacion.value;

        diaJubilacion.innerHTML = '<option value="">Día</option>';

        if (!anioJubilacion.value || !mesJubilacion.value) {
            validarFechaJubilacion();
            return;
        }

        const ultimoDia = new Date(
            Number(anioJubilacion.value),
            Number(mesJubilacion.value),
            0
        ).getDate();

        for (let dia = 1; dia <= ultimoDia; dia++) {

            const opcion = document.createElement('option');

            opcion.value = String(dia).padStart(2, '0');
            opcion.textContent = String(dia).padStart(2, '0');

            if (opcion.value === diaSeleccionado) {
                opcion.selected = true;
            }

            diaJubilacion.appendChild(opcion);
        }

        validarFechaJubilacion();
    }

    {{-- Eventos de la fecha --}}
    anioJubilacion.addEventListener('change', cargarDiasJubilacion);
    mesJubilacion.addEventListener('change', cargarDiasJubilacion);

    diaJubilacion.addEventListener('change', validarFechaJubilacion);
    
    // Mostrar mensajes en el modal.
    function mostrarMensajeJubilacion(mensaje, correcto = false) {
        const contenedor = document.getElementById('mensajeJubilacion');

        contenedor.textContent = mensaje;
        contenedor.style.display = 'block';
        contenedor.style.backgroundColor = correcto ? '#d4edda' : '#f8d7da';
        contenedor.style.color = correcto ? '#155724' : '#721c24';
        contenedor.style.border = correcto
            ? '1px solid #c3e6cb'
            : '1px solid #f5c6cb';
    }

    // Guardar jubilación.
    async function guardarJubilacion() {

        const idPersonal = idPersonalJubilacion.value;
        const escuela = document.getElementById('id_escuela').value;

        const anio = anioJubilacion.value;
        const mes = mesJubilacion.value;
        const dia = diaJubilacion.value;

        if (!idPersonal || !escuela || !anio || !mes || !dia) {
            mostrarMensajeJubilacion('Seleccione una fecha completa.');
            return;
        }

        const fecha = `${anio}-${mes}-${dia}`;

        btnGuardarJubilacion.disabled = true;
        btnGuardarJubilacion.textContent = 'Guardando...';

        document.getElementById('mensajeJubilacion').style.display = 'none';

        try {

            const respuesta = await fetch(urlGuardarJubilacion, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': tokenCSRF
                },
                body: JSON.stringify({
                    id_personal: idPersonal,
                    escuela: escuela,
                    fecha_jubilacion: fecha
                })
            });

            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.ok) {
                throw new Error(
                    resultado.mensaje || 'No fue posible guardar la jubilación.'
                );
            }

            mostrarMensajeJubilacion(resultado.mensaje, true);

            alert(resultado.mensaje);

            cerrarModalJubilacion();

            // Actualizar la tabla: la persona ya no debe aparecer.
            cargarListaJubilacion();

        } catch (error) {

            mostrarMensajeJubilacion(error.message);

        } finally {

            btnGuardarJubilacion.textContent = 'Guardar';
            validarFechaJubilacion();
        }
    }

    // Evento del botón Guardar.
    btnGuardarJubilacion.addEventListener('click', guardarJubilacion);


</script>

@endpush