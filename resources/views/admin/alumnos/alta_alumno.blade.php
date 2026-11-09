
{{-- Formulario vacío que el código original conserva expresamente --}}
<form id="alta" name="alta"></form>

<form id="altaInfoAlumnos" name="altaInfoAlumnos" method="POST">
    @csrf

    @php
        $datosAlumno = data_get($alumno ?? [], 'Alumno', []);
        $datosBachillerato = data_get($alumno ?? [], 'AlumnoBachillerato', []);
        $valorAlumno = fn($campo, $default = '') => old("Alumno.$campo", $datosAlumno[$campo] ?? $default);
        $valorBachillerato = fn($campo, $default = '') => old("AlumnoBachillerato.$campo", $datosBachillerato[$campo] ?? $default);
        $opciones = $opcionesCreacion ?? [];
    @endphp

    {{-- Información académica --}}
    <table class="datos">
        <tr>
            <th colspan="8">Información del Alumno</th>
        </tr>
        <tr>
            <td>Licenciatura:</td>
            <td>{{ $licenciatura[$opciones['id_licenciatura']] ?? '' }}</td>

            <td>Especialidad:</td>
            <td>{{ $especialidad[$opciones['id_especialidad']] ?? '' }}</td>

            <td>Modalidad:</td>
            <td>{{ $modalidad[$opciones['modalidad']] ?? '' }}</td>

            <td>Turno:</td>
            <td>{{ $turno[$opciones['turno']] ?? '' }}</td>
        </tr>
        <tr>
            <td>Semestre:</td>
            <td>{{ $opciones['semestre'] ?? '' }}</td>

            <td>Grupo:</td>
            <td>{{ $opciones['grupo'] ?? '' }}</td>

            <td>Matrícula:</td>
            <td>
                <input type="text"
                       id="RegistroMatricula"
                       name="Registro[matricula]"
                       size="12"
                       maxlength="12"
                       voluntario
                       onblur="this.value=this.value.trim().toUpperCase(); if(typeof testMat === 'function') testMat(this.id);">
            </td>
            <td colspan="2"></td>
        </tr>
    </table>

    <br><br>

    {{-- Información personal --}}
    <table class="datos">
        <tr>
            <th colspan="6">Información Personal</th>
        </tr>
        <tr>
            <td>Primer Apellido:</td>
            <td>
                <input type="text" id="AlumnoApellidoPaterno"
                       name="Alumno[apellido_paterno]"
                       value="{{ $valorAlumno('apellido_paterno') }}"
                       nombre="Apellido Paterno"
                       onblur="this.value=this.value.trim().toUpperCase();">
            </td>

            <td>Segundo Apellido:</td>
            <td>
                <input type="text" id="AlumnoApellidoMaterno"
                       name="Alumno[apellido_materno]"
                       value="{{ $valorAlumno('apellido_materno') }}"
                       nombre="Apellido Materno"
                       voluntario
                       onblur="this.value=this.value.trim().toUpperCase(); if(typeof testApellido === 'function') testApellido(this.id);">
            </td>

            <td>Nombre:</td>
            <td>
                <input type="text" id="AlumnoNombre"
                       name="Alumno[nombre]"
                       value="{{ $valorAlumno('nombre') }}"
                       nombre="Nombre"
                       onblur="this.value=this.value.trim().toUpperCase();">
            </td>
        </tr>
        <tr>
            <td>CURP:</td>
            <td>
                <input type="text" id="AlumnoCurp"
                       name="Alumno[curp]"
                       size="18"
                       maxlength="18"
                       value="{{ $valorAlumno('curp') }}"
                       nombre="Curp"
                       onblur="this.value=this.value.trim().toUpperCase(); if(typeof testCurp === 'function') testCurp(this.id); if(typeof curp2datos === 'function') curp2datos(this.value, 'AlumnoEdad', 'AlumnoGenero', 'AlumnoIdRenapo');">
            </td>

            <td>Género:</td>
            <td>
                <select id="AlumnoGenero" name="Alumno[genero]" nombre="Genero">
                    <option value="">Seleccione</option>
                    @foreach(($generos ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('genero') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>Edad:</td>
            <td>
                <input type="number" id="AlumnoEdad"
                       name="Alumno[edad]"
                       size="5"
                       value="{{ $valorAlumno('edad') }}"
                       nombre="Edad">
            </td>
        </tr>
        <tr>
            <td>Entidad Nacimiento:</td>
            <td>
                <select id="AlumnoIdRenapo" name="Alumno[id_renapo]"
                        nombre="Entidad Nacimiento"
                        onchange="if(typeof testEntidad === 'function') testEntidad(this.id, 'AlumnoCurp');">
                    <option value="">Seleccione</option>
                    @foreach(($entidad_nacimiento ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('id_renapo') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>Nacionalidad:</td>
            <td>
                <select id="AlumnoNacionalidad" name="Alumno[nacionalidad]" nombre="Nacionalidad">
                    <option value="">Seleccione</option>
                    @foreach(($nacionalidad ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('nacionalidad') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>Documentación:</td>
            <td>
                <select id="AlumnoIdDocumento" name="Alumno[id_documento]" nombre="Documento">
                    <option value="">Seleccione</option>
                    @foreach(($documentos ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('id_documento') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td><b>Municipio Nacimiento:</b></td>
            <td>
                <select id="AlumnoMunicipioNacimiento"
                        name="Alumno[municipio_nacimiento]"
                        nombre="Municipio de nacimiento">
                    <option value="">Seleccione</option>
                    @foreach(($municipio_nacimiento ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('municipio_nacimiento') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>
        </tr>
    </table>

    <br>

    {{-- Información de bachillerato --}}
    <table class="datos">
        <tr>
            <th colspan="6">Información de Bachillerato</th>
        </tr>
        <tr>
            <td><b>Entidad bachillerato:</b></td>
            <td>
                <select id="AlumnoBachilleratoIdEntidad"
                        name="AlumnoBachillerato[id_entidad]"
                        nombre="Entidad">
                    <option value="">Seleccione</option>
                    @foreach(($entidades ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorBachillerato('id_entidad') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td><b>Municipio Bachillerato:</b></td>
            <td>
                <select id="AlumnoBachilleratoIdMunicipio"
                        name="AlumnoBachillerato[id_municipio]"
                        nombre="Municipio">
                    <option value="">Seleccione</option>
                    @foreach(($municipios ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorBachillerato('id_municipio') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td><b>Nombre Bachillerato:</b></td>
            <td>
                <select id="AlumnoBachilleratoIdBachillerato"
                        name="AlumnoBachillerato[id_bachillerato]"
                        nombre="Alumno Bachillerato"
                        onchange="if(typeof datosOcultos3 === 'function') datosOcultos3(this.id, 'otro');">
                    <option value="">Seleccione</option>
                    @foreach(($bachilleratos ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorBachillerato('id_bachillerato') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td><b>Promedio:</b></td>
            <td>
                <input type="text" id="AlumnoBachilleratoPromedio"
                       name="AlumnoBachillerato[promedio]"
                       size="8"
                       value="{{ $valorBachillerato('promedio') }}">
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <div class="otro" style="display:none">
                    ¿Cuál?:
                    <input type="text" id="AlumnoBachilleratoOtro"
                           name="AlumnoBachillerato[otro]"
                           value="{{ $valorBachillerato('otro') }}"
                           nombre="otro"
                           onblur="this.value=this.value.trim().toUpperCase();">
                </div>
            </td>
        </tr>
    </table>

    <br>

    {{-- Discapacidad o aptitud sobresaliente --}}
    <table class="datos">
        <tr>
            <th colspan="3">Discapacidad o Aptitud sobresaliente</th>
        </tr>
        <tr>
            <td>¿El alumno sufre alguna discapacidad o aptitud sobresaliente?</td>
            <td colspan="2">
                <select id="AlumnoDiscapacidad"
                        name="Alumno[discapacidad]"
                        nombre="Discapacidad"
                        onchange="if(typeof datosOcultos === 'function') datosOcultos(this.id, 'discapacidad', 1, 1);">
                    <option value="">Seleccione</option>
                    <option value="s" @selected($valorAlumno('discapacidad') == 's')>Sí</option>
                    <option value="n" @selected($valorAlumno('discapacidad') == 'n')>No</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div class="discapacidad">
                    ¿Cuál?:
                    <select id="AlumnoTipoDiscapacidad"
                            name="Alumno[tipo_discapacidad]"
                            nombre="Tipo Discapacidad">
                        <option value="">Seleccione</option>
                        @foreach(($discapacidad ?? []) as $valor => $texto)
                            <option value="{{ $valor }}" @selected($valorAlumno('tipo_discapacidad') == $valor)>
                                {{ $texto }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </td>
        </tr>
    </table>

    {{-- Información SIGED --}}
    <table class="datos">
        <tr>
            <th colspan="4">Información SIGED</th>
        </tr>
        <tr>
            <td>Correo electrónico:</td>
            <td>
                <input type="text" id="AlumnoCorreo"
                       name="Alumno[correo]"
                       value="{{ $valorAlumno('correo') }}"
                       nombre="Correo electrónico"
                       voluntario>
            </td>

            <td>Número telefónico:</td>
            <td>
                <input type="text" id="AlumnoTelefono"
                       name="Alumno[telefono]"
                       size="10"
                       maxlength="10"
                       minlength="10"
                       value="{{ $valorAlumno('telefono') }}"
                       nombre="Número telefónico"
                       voluntario>
            </td>
        </tr>
        <tr>
            <td>País Nacimiento:</td>
            <td>
                <select id="AlumnoPais" name="Alumno[pais]" nombre="País Nacimiento">
                    <option value="">Seleccione</option>
                    @foreach(($paises ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('pais') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>Entidad Nacimiento:</td>
            <td>
                <select id="AlumnoEntidadNal" name="Alumno[entidad_nal]" nombre="Entidad de Nacimiento">
                    <option value="">Seleccione</option>
                    @foreach(($entidadesnal ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('entidad_nal') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td>País procedencia:</td>
            <td>
                <select id="AlumnoPaisProcedencia" name="Alumno[pais_procedencia]" nombre="País procedencia">
                    <option value="">Seleccione</option>
                    @foreach(($paises ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('pais_procedencia') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>Entidad procedencia:</td>
            <td>
                <select id="AlumnoEntidadProcedencia" name="Alumno[entidad_procedencia]" nombre="Entidad procedencia">
                    <option value="">Seleccione</option>
                    @foreach(($entidadesnal3 ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('entidad_procedencia') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="4"><b>Domicilio</b></td>
        </tr>
        <tr>
            <td>Entidad del domicilio del alumno:</td>
            <td>
                <select id="AlumnoEntidadDomicilio" name="Alumno[entidad_domicilio]" nombre="Entidad domicilio">
                    <option value="">Seleccione</option>
                    @foreach(($entidades ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('entidad_domicilio') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>Municipio del domicilio del alumno:</td>
            <td>
                <select id="AlumnoMunicipioDomicilio" name="Alumno[municipio_domicilio]" nombre="Municipio domicilio">
                    <option value="">Seleccione</option>
                    @foreach(($municipios2 ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('municipio_domicilio') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td>CP del domicilio:</td>
            <td>
                <input type="text" id="AlumnoCp"
                       name="Alumno[cp]"
                       size="5"
                       maxlength="5"
                       minlength="5"
                       value="{{ $valorAlumno('cp') }}"
                       nombre="Código Postal">
            </td>
        </tr>
        <tr>
            <td>Segunda lengua:</td>
            <td>
                <select id="AlumnoSegundaLengua" name="Alumno[segunda_lengua]" nombre="Segunda lengua">
                    <option value="">Seleccione</option>
                    @foreach(($lenguas ?? []) as $valor => $texto)
                        <option value="{{ $valor }}" @selected($valorAlumno('segunda_lengua') == $valor)>
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>Antecedente académico:</td>
            <td>
                <select id="AlumnoAntecedente" name="Alumno[antecedente]" nombre="Antecedente">
                    <option value="">Seleccione</option>
                    <option value="1" @selected($valorAlumno('antecedente') == '1')>Sí</option>
                    <option value="2" @selected($valorAlumno('antecedente') == '2')>No</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Fecha de ingreso a educación superior 1er semestre:</td>
            <td>
                <input type="date" id="AlumnoFecha"
                       name="Alumno[fecha]"
                       min="1980-01-01"
                       max="2025-12-31"
                       value="{{ $valorAlumno('fecha') }}"
                       nombre="Fecha de ingreso">
            </td>

            <td>Origen de estudios precedentes:</td>
            <td>
                <select id="AlumnoOrigen" name="Alumno[origen]" nombre="Origen">
                    <option value="">Seleccione</option>
                    <option value="1" @selected($valorAlumno('origen') == '1')>Nacionales</option>
                    <option value="2" @selected($valorAlumno('origen') == '2')>Extranjeros</option>
                </select>
            </td>
        </tr>
    </table>

    {{-- Valores académicos elegidos en opciones_alta_alumno --}}
    <input type="hidden" name="Registro[id_licenciatura]" value="{{ $opciones['id_licenciatura'] ?? '' }}">
    <input type="hidden" name="Registro[id_especialidad]" value="{{ $opciones['id_especialidad'] ?? '' }}">
    <input type="hidden" name="Registro[modalidad]" value="{{ $opciones['modalidad'] ?? '' }}">
    <input type="hidden" name="Registro[semestre]" value="{{ $opciones['semestre'] ?? '' }}">
    <input type="hidden" name="Registro[turno]" value="{{ $opciones['turno'] ?? '' }}">
    <input type="hidden" name="Registro[grupo]" value="{{ $opciones['grupo'] ?? '' }}">

    <div style="text-align:center;">
        @if(!empty($opciones))
            <button type="button" id="guardarAlta" class="boton__primario">
                Guardar
            </button>
        @endif
    </div>
</form>

<br><br>

<table id="listaAlumnos" class="datos">
    <tr>
        <th>Nombre</th>
        <th>Primer Apellido</th>
        <th>Segundo Apellido</th>
        <th>CURP</th>
    </tr>
</table>

<script>
(function () {
    const formulario = document.getElementById('altaInfoAlumnos');

    if (!formulario) return;

    const csrf = formulario.querySelector('input[name="_token"]').value;

    // Ocultar campos según los valores iniciales.
    const discapacidad = document.getElementById('AlumnoDiscapacidad');
    if (discapacidad && discapacidad.value !== 's') {
        document.querySelectorAll('#altaInfoAlumnos div.discapacidad')
            .forEach(elemento => elemento.style.display = 'none');
    }

    const bachillerato = document.getElementById('AlumnoBachilleratoIdBachillerato');
    if (bachillerato && bachillerato.value !== '0') {
        document.querySelectorAll('#altaInfoAlumnos div.otro')
            .forEach(elemento => elemento.style.display = 'none');
    }

    // Campos opcionales originales.
    ['AlumnoCorreo', 'AlumnoTelefono', 'AlumnoApellidoMaterno', 'RegistroMatricula']
        .forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) elemento.setAttribute('voluntario', 'true');
        });

    // Peticiones para actualizar los combos dependientes.
    async function actualizarCombo(origen, destino, endpoint) {
        const campoOrigen = document.getElementById(origen);
        const campoDestino = document.getElementById(destino);

        if (!campoOrigen || !campoDestino) return;

        const datos = new FormData(formulario);

        try {
            if (typeof muestraOcupado === 'function') muestraOcupado();

            const respuesta = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: datos
            });

            if (!respuesta.ok) {
                throw new Error('Error al consultar las opciones (' + respuesta.status + ').');
            }

            campoDestino.innerHTML = '<option value="">Seleccione</option>' +
                await respuesta.text();

        } catch (error) {
            console.error(error);
            alert(error.message);
        } finally {
            if (typeof ocultaOcupado === 'function') ocultaOcupado();
        }
    }

    // Bachillerato: entidad -> municipio -> bachillerato.
    document.getElementById('AlumnoBachilleratoIdEntidad')
        ?.addEventListener('change', () => {
            actualizarCombo(
                'AlumnoBachilleratoIdEntidad',
                'AlumnoBachilleratoIdMunicipio',
                '{{ url('/alumnos/municipiosBachilleratos') }}'
            );
        });

    document.getElementById('AlumnoBachilleratoIdMunicipio')
        ?.addEventListener('change', () => {
            actualizarCombo(
                'AlumnoBachilleratoIdMunicipio',
                'AlumnoBachilleratoIdBachillerato',
                '{{ url('/alumnos/bachilleratoOpciones') }}'
            );
        });

    // Nacimiento: entidad RENAPO -> municipio.
    document.getElementById('AlumnoIdRenapo')
        ?.addEventListener('change', () => {
            actualizarCombo(
                'AlumnoIdRenapo',
                'AlumnoMunicipioNacimiento',
                '{{ url('/alumnos/municipiosNacimiento') }}'
            );
        });

    // Domicilio: entidad -> municipio.
    document.getElementById('AlumnoEntidadDomicilio')
        ?.addEventListener('change', () => {
            actualizarCombo(
                'AlumnoEntidadDomicilio',
                'AlumnoMunicipioDomicilio',
                '{{ url('/alumnos/municipiosNacimiento6') }}'
            );
        });

    // País de nacimiento -> entidad nacional.
    document.getElementById('AlumnoPais')
        ?.addEventListener('change', () => {
            actualizarCombo(
                'AlumnoPais',
                'AlumnoEntidadNal',
                '{{ url('/alumnos/municipiosNacimiento3') }}'
            );
        });

    // País de procedencia -> entidad de procedencia.
    document.getElementById('AlumnoPaisProcedencia')
        ?.addEventListener('change', () => {
            actualizarCombo(
                'AlumnoPaisProcedencia',
                'AlumnoEntidadProcedencia',
                '{{ url('/alumnos/municipiosNacimiento5') }}'
            );
        });

    // Agregar el alumno recién guardado a la tabla.
    async function listaAlumnos(parametros) {
        const respuesta = await fetch('{{ url('/alumnos/renglonesAlta') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: parametros
        });

        if (!respuesta.ok) {
            throw new Error('No se pudo actualizar la lista de alumnos.');
        }

        const html = await respuesta.text();
        document.getElementById('listaAlumnos')
            .insertAdjacentHTML('beforeend', html);
    }

    // Guardar alta.
    document.getElementById('guardarAlta')?.addEventListener('click', async function () {
        const parametros = new FormData(formulario);

        // Se conserva la validación global que utilizaba el sistema original.
        if (typeof test === 'function' && !test('altaInfoAlumnos')) {
            return;
        }

        try {
            if (typeof muestraOcupado === 'function') muestraOcupado();

            const respuesta = await fetch('{{ route('alumnos.altaAlumno') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: parametros
            });

            if (!respuesta.ok) {
                throw new Error(await respuesta.text());
            }

            // Guardamos los datos antes de limpiar el formulario.
            await listaAlumnos(parametros);

            formulario.reset();

            // Reactivar los checkboxes, como hacía el código original.
            document.querySelectorAll('#altaInfoAlumnos input[type="checkbox"]')
                .forEach(caja => caja.disabled = false);

        } catch (error) {
            console.error(error);
            alert(error.message);
        } finally {
            if (typeof ocultaOcupado === 'function') ocultaOcupado();
        }
    });
})();
</script>
