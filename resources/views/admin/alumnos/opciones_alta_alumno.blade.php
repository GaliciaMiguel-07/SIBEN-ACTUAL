@extends('layouts.siben.siben')

@section('content')

<section class="contenedor" style="max-width: 950px; margin: 0 auto; padding: 24px 16px;">

    <header style="margin-bottom: 24px;">
        <h1 class="h2r patria" style="color: var(--neutro900); font-size: 1.75rem;">
            Opciones de Alta para Alumnos
        </h1>
    </header>

    <div class="buscar01" style="padding: 20px; overflow-x: auto;">

        <form id="OpcionesAltaAlumno"
              method="POST"
              action="{{ route('alumnos.altaAlumno') }}">

            @csrf

            <table class="datos" style="width: 100%; border-collapse: collapse;">

                <tbody>

                    <tr>
                        <th colspan="3" style="text-align: center;">
                            Opciones de Alta para Alumnos
                        </th>
                    </tr>

                    <tr>
                        <td style="width: 25%;">Licenciatura:</td>
                        <td colspan="2">
                            <select
                                name="Vuelta[id_licenciatura]"
                                id="VueltaIdLicenciatura"
                                nombre="Licenciatura"
                                class="form-select"
                                required>
                                <option value="">Seleccione</option>

                                @foreach ($licenciaturas as $id => $nombre)
                                    <option value="{{ $id }}">
                                        {{ $nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Especialidad:</td>
                        <td>
                            <select
                                name="Vuelta[id_especialidad]"
                                id="VueltaIdEspecialidad"
                                nombre="Especialidad"
                                class="form-select"
                                required>
                                <option value="">Seleccione</option>
                            </select>
                        </td>
                        <td style="width: 50px; text-align: center;">
                            <span id="especialidad" class="indicador-carga" hidden>
                                Cargando...
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>Modalidad:</td>
                        <td>
                            <select
                                name="Vuelta[modalidad]"
                                id="VueltaModalidad"
                                nombre="Modalidad"
                                class="form-select"
                                required>
                                <option value="">Seleccione</option>
                            </select>
                        </td>
                        <td style="text-align: center;">
                            <span id="modalidad" class="indicador-carga" hidden>
                                Cargando...
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>Semestre:</td>
                        <td colspan="2">
                            <select
                                name="Vuelta[semestre]"
                                id="VueltaSemestre"
                                nombre="Semestre"
                                class="form-select"
                                required>
                                <option value="">Seleccione</option>

                                @foreach ($semestres as $id => $nombre)
                                    <option value="{{ $id }}">
                                        {{ $nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Turno:</td>
                        <td>
                            <select
                                name="Vuelta[turno]"
                                id="VueltaTurno"
                                nombre="Turno"
                                class="form-select"
                                required>
                                <option value="">Seleccione</option>
                            </select>
                        </td>
                        <td style="text-align: center;">
                            <span id="turno" class="indicador-carga" hidden>
                                Cargando...
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>Grupo:</td>
                        <td colspan="2">
                            <input
                                type="text"
                                name="Vuelta[grupo]"
                                id="VueltaGrupo"
                                nombre="grupo"
                                maxlength="10"
                                class="form-control"
                                onblur="this.value = this.value.trim().toUpperCase();">
                        </td>
                    </tr>

                </tbody>

            </table>

            <div style="text-align: center; margin-top: 24px;">
                <button
                    type="button"
                    id="ButtonAlta"
                    class="boton__primario"
                    onclick="peticionCreacion()">
                    Crear Grupo
                </button>
            </div>

        </form>

    </div>

    {{-- Ventana superpuesta para mostrar el formulario de alta --}}
    <div id="overoverlay"
         style="
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0,0,0,.65);
            padding: 40px 15px;
            overflow-y: auto;
         ">

        <div style="
            max-width: 900px;
            min-height: 330px;
            margin: 0 auto;
            background: white;
            border: 2px solid black;
         ">

            <div style="
                text-align: right;
                padding: 8px 12px;
                background: #333;
                color: white;
            ">
                <button type="button"
                        onclick="ocultaOverlay()"
                        style="background: none; border: none; color: white; cursor: pointer;">
                    [Cerrar]
                </button>
            </div>

            <div id="overlay"
                 style="padding: 15px; overflow: auto;">
            </div>

        </div>
    </div>

</section>

@endsection

@push('scripts')
<script>
    const formularioAlta = document.getElementById('OpcionesAltaAlumno');

    // CORRECCIÓN APLICADA AQUÍ: Se agregan los helpers {{ url() }} para generar la ruta completa
    // Solución definitiva para las rutas en JavaScript
    // Solución definitiva usando route() de Laravel
    const urlsAlta = {
        especialidad: '{{ route("alumnos.especialidadOpciones") }}',
        modalidad: '{{ route("alumnos.modalidadOpciones") }}',
        turno: '{{ route("alumnos.turnoOpciones") }}'
    };
    function parametrosFormulario() {
        return new URLSearchParams(new FormData(formularioAlta));
    }

    async function actualizarOpciones(url, selectId, indicadorId) {
        const select = document.getElementById(selectId);
        const indicador = document.getElementById(indicadorId);

        indicador.hidden = false;
        select.disabled = true;

        try {
            const respuesta = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formularioAlta.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: parametrosFormulario().toString()
            });

            if (!respuesta.ok) {
                throw new Error('No se pudieron cargar las opciones.');
            }

            select.innerHTML = await respuesta.text();

        } catch (error) {
            console.error(error);
            alert(error.message);
        } finally {
            indicador.hidden = true;
            select.disabled = false;
        }
    }

    document.getElementById('VueltaIdLicenciatura')
        .addEventListener('change', async function () {
            document.getElementById('VueltaIdEspecialidad').innerHTML =
                '<option value="">Seleccione</option>';

            document.getElementById('VueltaModalidad').innerHTML =
                '<option value="">Seleccione</option>';

            document.getElementById('VueltaTurno').innerHTML =
                '<option value="">Seleccione</option>';

            if (this.value) {
                await actualizarOpciones(
                    urlsAlta.especialidad,
                    'VueltaIdEspecialidad',
                    'especialidad'
                );
            }
        });

    document.getElementById('VueltaIdEspecialidad')
        .addEventListener('change', async function () {
            document.getElementById('VueltaModalidad').innerHTML =
                '<option value="">Seleccione</option>';

            document.getElementById('VueltaTurno').innerHTML =
                '<option value="">Seleccione</option>';

            if (this.value) {
                await actualizarOpciones(
                    urlsAlta.modalidad,
                    'VueltaModalidad',
                    'modalidad'
                );
            }
        });

    document.getElementById('VueltaModalidad')
        .addEventListener('change', async function () {
            document.getElementById('VueltaTurno').innerHTML =
                '<option value="">Seleccione</option>';

            if (this.value) {
                await actualizarOpciones(
                    urlsAlta.turno,
                    'VueltaTurno',
                    'turno'
                );
            }
        });

    function revisarElementos() {
        const campos = [
            ['VueltaIdLicenciatura', 'Licenciatura'],
            ['VueltaIdEspecialidad', 'Especialidad'],
            ['VueltaModalidad', 'Modalidad'],
            ['VueltaSemestre', 'Semestre'],
            ['VueltaTurno', 'Turno']
        ];

        for (const [id, nombre] of campos) {
            const campo = document.getElementById(id);

            if (!campo.value) {
                alert('Por favor, elija ' + nombre);
                campo.focus();
                return false;
            }
        }

        const grupo = document.getElementById('VueltaGrupo');

        if (
            !grupo.hasAttribute('oculto') &&
            !grupo.hasAttribute('voluntario') &&
            grupo.closest('tr').style.display !== 'none' &&
            !grupo.value.trim()
        ) {
            alert('Por favor, escriba grupo');
            grupo.focus();
            return false;
        }

        return true;
    }

    async function peticionCreacion() {
        if (!revisarElementos()) {
            return;
        }

        const overlay = document.getElementById('overlay');
        const ventana = document.getElementById('overoverlay');
        const boton = document.getElementById('ButtonAlta');

        overlay.innerHTML = '<p style="text-align:center;">Cargando formulario...</p>';
        ventana.style.display = 'block';
        boton.disabled = true;

        try {
            const respuesta = await fetch(formularioAlta.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formularioAlta.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(formularioAlta)
            });

            if (!respuesta.ok) {
                throw new Error('No se pudo cargar el formulario de alta.');
            }

            overlay.innerHTML = await respuesta.text();

        } catch (error) {
            console.error(error);
            overlay.innerHTML =
                '<p style="color:red; text-align:center;">' +
                error.message +
                '</p>';
        } finally {
            boton.disabled = false;
        }
    }

    function ocultaOverlay() {
        document.getElementById('overoverlay').style.display = 'none';
        document.getElementById('overlay').innerHTML = '';
    }
</script>
@endpush