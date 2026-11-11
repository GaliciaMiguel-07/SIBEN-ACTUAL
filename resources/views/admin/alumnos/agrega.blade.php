
@extends('layouts.siben.siben')

@section('titulo', 'Alta de Alumnos — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">

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
                Alta de Alumnos
            </h3>
        </div>

        {{-- Mensajes --}}
        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Formulario --}}
        <form id="formAltaAlumno" method="POST">

            @csrf

            {{-- SECCIÓN: ESCUELA --}}
            <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">

                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Escuela
                </div>

                <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">

                    <div>
                        <label for="id_entidad">Entidad:</label>
                        <select name="id_entidad" id="id_entidad" style="width: 100%; padding: 8px;">
                            <option value="">Seleccione</option>
                            @isset($entidadesId)
                                @foreach($entidadesId as $id => $entidad)
                                    <option value="{{ $id }}" {{ old('id_entidad') == $id ? 'selected' : '' }}>
                                        {{ $entidad }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div>
                        <label for="id_escuela">Escuela:</label>
                        <select name="id_escuela" id="id_escuela" disabled style="width: 100%; padding: 8px;">
                            <option value="">Seleccione primero una entidad</option>
                        </select>
                    </div>

                </div>
            </div>

            {{-- SECCIÓN: INFORMACIÓN DEL ALUMNO --}}
            <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">

                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Información del Alumno
                </div>

                <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">

                    <div>
                        <label for="id_licenciatura">Licenciatura:</label>
                        <select name="id_licenciatura" id="id_licenciatura" disabled style="width: 100%; padding: 8px;">
                            <option value="">Seleccione</option>
                        </select>
                    </div>

                    <div>
                        <label for="id_especialidad">Especialidad:</label>
                        <select name="id_especialidad" id="id_especialidad" disabled style="width: 100%; padding: 8px;">
                            <option value="">Seleccione</option>
                        </select>
                    </div>

                    <div>
                        <label for="modalidad">Modalidad:</label>
                        <select name="modalidad" id="modalidad" style="width: 100%; padding: 8px;">
                            <option value="">Seleccione</option>
                            @isset($modalidades)
                                @foreach($modalidades as $id => $modalidad)
                                    <option value="{{ $id }}">{{ $modalidad }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div>
                        <label for="turno">Turno:</label>
                        <select name="turno" id="turno" disabled style="width: 100%; padding: 8px;">
                            <option value="">Seleccione</option>
                        </select>
                    </div>

                    <div>
                        <label for="semestre">Semestre:</label>
                        <select name="semestre" id="semestre" style="width: 100%; padding: 8px;">
                            <option value="">Seleccione</option>
                            @isset($semestres)
                                @foreach($semestres as $semestre)
                                    <option value="{{ $semestre }}">{{ $semestre }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div>
                        <label for="grupo">Grupo:</label>
                        <input type="text" name="grupo" id="grupo"
                               value="{{ old('grupo') }}"
                               maxlength="5"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="matricula">Matrícula:</label>
                        <input type="text" name="matricula" id="matricula"
                               value="{{ old('matricula') }}"
                               maxlength="20"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px; box-sizing: border-box;">
                    </div>

                </div>
            </div>

            {{-- SECCIÓN: INFORMACIÓN PERSONAL --}}
            <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">

                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Información Personal
                </div>

                <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">

                    <div>
                        <label for="apellido_paterno">Primer Apellido:</label>
                        <input type="text" name="apellido_paterno" id="apellido_paterno"
                               value="{{ old('apellido_paterno') }}"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="apellido_materno">Segundo Apellido:</label>
                        <input type="text" name="apellido_materno" id="apellido_materno"
                               value="{{ old('apellido_materno') }}"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" id="nombre"
                               value="{{ old('nombre') }}"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="curp">CURP:</label>
                        <input type="text" name="curp" id="curp"
                               value="{{ old('curp') }}"
                               maxlength="18"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="genero">Género:</label>
                        <select name="genero" id="genero" style="width: 100%; padding: 8px;">
                            <option value="">Seleccione</option>
                            <option value="H">Masculino</option>
                            <option value="M">Femenino</option>
                            <option value="X">No especificado</option>
                        </select>
                    </div>

                    <div>
                        <label for="edad">Edad:</label>
                        <input type="number" name="edad" id="edad"
                               value="{{ old('edad') }}"
                               min="0"
                               style="width: 100%; padding: 8px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="id_renapo">Entidad de Nacimiento:</label>
                        <select name="id_renapo" id="id_renapo" style="width: 100%; padding: 8px;">
                            <option value="">Seleccione</option>
                            @isset($entidadesRenapo)
                                @foreach($entidadesRenapo as $id => $entidad)
                                    <option value="{{ $id }}">{{ $entidad }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div>
                        <label for="nacionalidad">Nacionalidad:</label>
                        <select name="nacionalidad" id="nacionalidad" style="width: 100%; padding: 8px;">
                            <option value="">Seleccione</option>
                            <option value="m">Mexicano</option>
                            <option value="e">Extranjero</option>
                        </select>
                    </div>

                    <div>
                        <label for="id_documento">Documentación:</label>
                        <select name="id_documento" id="id_documento" style="width: 100%; padding: 8px;">
                            <option value="">Seleccione</option>
                            @isset($documentos)
                                @foreach($documentos as $id => $documento)
                                    <option value="{{ $id }}">{{ $documento }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                </div>
            </div>

            {{-- Botón pendiente de conectar con el controlador --}}
            <button type="button" id="btnGuardar" class="boton__primario" disabled>
                Guardar
            </button>

        </form>
    </div>
</div>
<script>


document.addEventListener('DOMContentLoaded', function () {
    const entidad = document.getElementById('id_entidad');
    const escuela = document.getElementById('id_escuela');
    const licenciatura = document.getElementById('id_licenciatura');
    const especialidad = document.getElementById('id_especialidad');
    const modalidad = document.getElementById('modalidad');
    const turno = document.getElementById('turno');

    const semestre = document.getElementById('semestre');
    const curp = document.getElementById('curp');
    const edad = document.getElementById('edad');
    const genero = document.getElementById('genero');
    const entidadNacimiento = document.getElementById('id_renapo');

    const apellidoPaterno = document.getElementById('apellido_paterno');
    const apellidoMaterno = document.getElementById('apellido_materno');
    const nombre = document.getElementById('nombre');
    const nacionalidad = document.getElementById('nacionalidad');
    const documento = document.getElementById('id_documento');

    const btnGuardar = document.getElementById('btnGuardar');

    let ofertaActual = [];
    let curpConsultada = false;
    let curpDisponible = false;

    const modalidades = {
        e: 'Escolarizada',
        m: 'Mixta',
        i: 'Intensiva'
    };

    const turnos = {
        m: 'Matutino',
        v: 'Vespertino',
        n: 'Nocturno',
        d: 'Discontinuo'
    };

    // VALIDAR CAMPOS OBLIGATORIOS Y ESTADO DEL BOTÓN
    function actualizarEstadoGuardar() {
        const camposObligatorios = [
            entidad,
            escuela,
            licenciatura,
            especialidad,
            modalidad,
            turno,
            semestre,
            apellidoPaterno,
            nombre,
            curp,
            genero,
            edad,
            entidadNacimiento,
            nacionalidad,
            documento
        ];

        const camposCompletos = camposObligatorios.every(campo =>
            campo.value.trim() !== ''
        );

        const edadValida = Number(edad.value) > 0;

        btnGuardar.disabled = !(
            camposCompletos &&
            edadValida &&
            curpConsultada &&
            curpDisponible
        );
    }

    // LIMPIAR SELECT
    function limpiarSelect(select, texto) {
        select.innerHTML = `<option value="">${texto}</option>`;
        select.disabled = true;
        actualizarEstadoGuardar();
    }

    // LLENAR SELECT
    function llenarSelect(select, opciones, texto) {
        limpiarSelect(select, texto);

        opciones.forEach(opcion => {
            const elemento = document.createElement('option');
            elemento.value = opcion.id;
            elemento.textContent = opcion.nombre;
            select.appendChild(elemento);
        });

        select.disabled = opciones.length === 0;
        actualizarEstadoGuardar();
    }

    // ELIMINAR OPCIONES REPETIDAS
    function opcionesUnicas(datos, campoId, campoNombre) {
        const opciones = new Map();

        datos.forEach(registro => {
            opciones.set(
                registro[campoId],
                registro[campoNombre]
            );
        });

        return Array.from(opciones, ([id, nombre]) => ({
            id,
            nombre
        }));
    }

    // CARGAR ESPECIALIDADES
    function cargarEspecialidades() {
        const registros = ofertaActual.filter(registro =>
            String(registro.id_licenciatura) === licenciatura.value
        );

        limpiarSelect(especialidad, 'Seleccione una especialidad');
        limpiarSelect(modalidad, 'Seleccione una modalidad');
        limpiarSelect(turno, 'Seleccione un turno');

        llenarSelect(
            especialidad,
            opcionesUnicas(
                registros,
                'id_especialidad',
                'especialidad'
            ),
            'Seleccione una especialidad'
        );
    }

    // CARGAR MODALIDADES Y TURNOS
    function cargarModalidadTurno() {
        const registros = ofertaActual.filter(registro =>
            String(registro.id_licenciatura) === licenciatura.value &&
            String(registro.id_especialidad) === especialidad.value
        );

        llenarSelect(
            modalidad,
            opcionesUnicas(
                registros,
                'modalidad',
                'modalidad'
            ).map(opcion => ({
                id: opcion.id,
                nombre: modalidades[opcion.id] || opcion.nombre
            })),
            'Seleccione una modalidad'
        );

        llenarSelect(
            turno,
            opcionesUnicas(
                registros,
                'turno',
                'turno'
            ).map(opcion => ({
                id: opcion.id,
                nombre: turnos[opcion.id] || opcion.nombre
            })),
            'Seleccione un turno'
        );
    }

    // CAMBIO DE ENTIDAD: CARGAR ESCUELAS
    entidad.addEventListener('change', function () {
        limpiarSelect(escuela, 'Cargando escuelas...');
        limpiarSelect(licenciatura, 'Seleccione una licenciatura');
        limpiarSelect(especialidad, 'Seleccione una especialidad');
        limpiarSelect(modalidad, 'Seleccione una modalidad');
        limpiarSelect(turno, 'Seleccione un turno');

        ofertaActual = [];

        if (!this.value) {
            limpiarSelect(escuela, 'Seleccione primero una entidad');
            return;
        }

        fetch("{{ route('admin.alumnos.escuelas') }}?entidad=" + encodeURIComponent(this.value))
            .then(response => {
                if (!response.ok) {
                    throw new Error('No se pudieron consultar las escuelas');
                }
                return response.json();
            })
            .then(escuelas => {
                const opciones = Object.entries(escuelas).map(([id, nombre]) => ({
                    id,
                    nombre
                }));

                llenarSelect(
                    escuela,
                    opciones,
                    'Seleccione una escuela'
                );

                if (opciones.length === 0) {
                    limpiarSelect(escuela, 'No hay escuelas disponibles');
                }
            })
            .catch(error => {
                console.error(error);
                limpiarSelect(escuela, 'Error al cargar escuelas');
            });
    });

    // CAMBIO DE ESCUELA: CARGAR LICENCIATURAS
    escuela.addEventListener('change', function () {
        limpiarSelect(licenciatura, 'Cargando licenciaturas...');
        limpiarSelect(especialidad, 'Seleccione una especialidad');
        limpiarSelect(modalidad, 'Seleccione una modalidad');
        limpiarSelect(turno, 'Seleccione un turno');

        ofertaActual = [];

        if (!this.value) {
            limpiarSelect(licenciatura, 'Seleccione primero una escuela');
            return;
        }

        fetch("{{ route('admin.alumnos.oferta') }}?escuela=" + encodeURIComponent(this.value))
            .then(response => {
                if (!response.ok) {
                    throw new Error('No se pudo consultar la oferta educativa');
                }
                return response.json();
            })
            .then(oferta => {
                ofertaActual = oferta;

                const opciones = opcionesUnicas(
                    ofertaActual,
                    'id_licenciatura',
                    'licenciatura'
                );

                llenarSelect(
                    licenciatura,
                    opciones,
                    opciones.length
                        ? 'Seleccione una licenciatura'
                        : 'No hay licenciaturas disponibles'
                );
            })
            .catch(error => {
                console.error(error);
                limpiarSelect(licenciatura, 'Error al cargar licenciaturas');
            });
    });

    licenciatura.addEventListener('change', cargarEspecialidades);
    especialidad.addEventListener('change', cargarModalidadTurno);

    // ACTUALIZAR EL BOTÓN CUANDO CAMBIEN LOS CAMPOS
    const camposParaValidar = [
        entidad,
        escuela,
        licenciatura,
        especialidad,
        modalidad,
        turno,
        semestre,
        apellidoPaterno,
        apellidoMaterno,
        nombre,
        genero,
        edad,
        entidadNacimiento,
        nacionalidad,
        documento
    ];

    camposParaValidar.forEach(campo => {
        campo.addEventListener('input', actualizarEstadoGuardar);
        campo.addEventListener('change', actualizarEstadoGuardar);
    });

    // SI SE MODIFICA LA CURP, VOLVER A CONSULTARLA
    curp.addEventListener('input', function () {
        curpConsultada = false;
        curpDisponible = false;
        actualizarEstadoGuardar();
    });

    // CONSULTAR CURP
    curp.addEventListener('blur', function () {
        const valorCurp = this.value.trim().toUpperCase();
        this.value = valorCurp;

        curpConsultada = false;
        curpDisponible = false;
        actualizarEstadoGuardar();

        if (valorCurp.length !== 18) {
            return;
        }

        fetch("{{ route('admin.alumnos.curp') }}?curp=" + encodeURIComponent(valorCurp))
            .then(async response => {
                const datos = await response.json();

                if (!response.ok) {
                    throw new Error(
                        datos.message ||
                        datos.mensaje ||
                        'CURP no válida'
                    );
                }

                return datos;
            })
            .then(datos => {
                if (!datos.valida) {
                    curpConsultada = false;
                    curpDisponible = false;
                    actualizarEstadoGuardar();

                    alert(datos.mensaje || 'No fue posible validar la CURP.');
                    return;
                }

                const alumno = datos.alumno;

                edad.value = alumno.edad ?? '';
                genero.value = alumno.genero ?? '';
                entidadNacimiento.value = alumno.id_renapo ?? '';

                if (datos.existe) {
                    apellidoPaterno.value = alumno.apellido_paterno ?? '';
                    apellidoMaterno.value = alumno.apellido_materno ?? '';
                    nombre.value = alumno.nombre ?? '';
                    nacionalidad.value = alumno.nacionalidad ?? '';
                    documento.value = alumno.id_documento ?? '';

                    curpConsultada = true;
                    curpDisponible = false;

                    alert('El alumno ya existe. No se puede registrar nuevamente.');
                } else {
                    // Mantener los datos ya capturados por el usuario cuando la CURP es nueva.
                    // Solo se valida disponibilidad y se conservan nombre/apellidos intactos.
                    curpConsultada = true;
                    curpDisponible = true;
                }

                actualizarEstadoGuardar();
            })
            .catch(error => {
                curpConsultada = false;
                curpDisponible = false;
                actualizarEstadoGuardar();

                console.error('Error al consultar CURP:', error);
                alert(error.message || 'No fue posible consultar la CURP.');
            });
    });

    // ESTADO INICIAL
    actualizarEstadoGuardar();
});


</script>
@endsection
