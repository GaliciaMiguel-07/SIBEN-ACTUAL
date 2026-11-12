@extends('layouts.siben.siben')

@section('titulo', 'Edición de Alumnos — SIBEN')

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
                Edición de Alumnos
            </h3>
        </div>

        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        {{-- SECCIÓN: BÚSQUEDA --}}
        <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">

            <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                Búsqueda
            </div>

            <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; background-color: #fff;">

                <div>
                    <label for="captura" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Captura:</label>
                    <select id="captura" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        @foreach($capturas as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="id_entidad" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Entidad:</label>
                    <select id="id_entidad" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Seleccione</option>
                        @foreach($entidades as $id => $entidad)
                            <option value="{{ $id }}" @selected((int)$entidad_id === (int)$id)>{{ $entidad }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="id_escuela" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Escuela:</label>
                    <select id="id_escuela" disabled style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Seleccione primero una entidad</option>
                    </select>
                </div>

                <div>
                    <label for="id_licenciatura" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Licenciatura:</label>
                    <select id="id_licenciatura" disabled style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Seleccione</option>
                    </select>
                </div>

                <div>
                    <label for="id_especialidad" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Especialidad:</label>
                    <select id="id_especialidad" disabled style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Seleccione</option>
                    </select>
                </div>

                <div>
                    <label for="modalidad" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Modalidad:</label>
                    <select id="modalidad" disabled style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Seleccione</option>
                    </select>
                </div>

                <div>
                    <label for="turno" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Turno:</label>
                    <select id="turno" disabled style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Seleccione</option>
                    </select>
                </div>

                <div>
                    <label for="semestre" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Semestre:</label>
                    <select id="semestre" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Seleccione</option>
                        @for($i = 1; $i <= 13; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="grupo" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Grupo:</label>
                    <input type="text" id="grupo" maxlength="5"
                           onblur="this.value = this.value.trim().toUpperCase();"
                           style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; box-sizing: border-box;">
                </div>

                <div>
                    <label for="status" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Estatus:</label>
                    <select id="status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        <option value="">Seleccione</option>
                        @foreach($status as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="curp" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">CURP:</label>
                    <input type="text" id="curp" maxlength="18"
                           onblur="this.value = this.value.trim().toUpperCase();"
                           style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; box-sizing: border-box;">
                </div>

                <div>
                    <label for="nid" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">NID:</label>
                    <input type="text" id="nid" maxlength="10"
                           style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; box-sizing: border-box;">
                </div>

            </div>

            <div style="padding: 0 16px 16px; background-color: #fff;">
                <button type="button" id="btnBuscar" class="boton__primario" style="padding: 8px 24px; font-size: 0.9rem; cursor: pointer;">
                    Buscar
                </button>
            </div>

        </div>

        <div id="contenedorLista" style="margin-top: 20px;">
            <p style="color: var(--neutro600); text-align: center; padding: 20px;">
                Seleccione una escuela y presione Buscar para ver a sus alumnos.
            </p>
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
document.addEventListener('DOMContentLoaded', function () {
    const entidad      = document.getElementById('id_entidad');
    const escuela      = document.getElementById('id_escuela');
    const licenciatura = document.getElementById('id_licenciatura');
    const especialidad = document.getElementById('id_especialidad');
    const modalidad    = document.getElementById('modalidad');
    const turno        = document.getElementById('turno');
    const btnBuscar    = document.getElementById('btnBuscar');
    const contenedor   = document.getElementById('contenedorLista');

    const urlEscuelas = '{{ route('admin.alumnos.escuelas') }}';
    const urlOferta   = '{{ route('admin.alumnos.oferta') }}';
    const urlLista    = '{{ route('admin.alumnos.lista') }}';

    const modalidades = { e: 'Escolarizada', m: 'Mixta', i: 'Intensiva' };
    const turnos      = { m: 'Matutino', v: 'Vespertino', n: 'Nocturno', d: 'Discontinuo' };

    let ofertaActual = [];

    function limpiarSelect(select, texto) {
        select.innerHTML = '<option value="">' + texto + '</option>';
        select.disabled = true;
    }

    function llenarSelect(select, opciones, texto) {
        limpiarSelect(select, texto);
        opciones.forEach(function (opcion) {
            const elemento = document.createElement('option');
            elemento.value = opcion.id;
            elemento.textContent = opcion.nombre;
            select.appendChild(elemento);
        });
        select.disabled = opciones.length === 0;
    }

    function opcionesUnicas(datos, campoId, campoNombre) {
        const opciones = new Map();
        datos.forEach(function (registro) {
            opciones.set(registro[campoId], registro[campoNombre]);
        });
        return Array.from(opciones, function (par) {
            return { id: par[0], nombre: par[1] };
        });
    }

    function cargarEscuelas(seleccionada) {
        limpiarSelect(escuela, 'Cargando escuelas...');
        limpiarSelect(licenciatura, 'Seleccione una licenciatura');
        limpiarSelect(especialidad, 'Seleccione una especialidad');
        limpiarSelect(modalidad, 'Seleccione una modalidad');
        limpiarSelect(turno, 'Seleccione un turno');
        ofertaActual = [];

        if (!entidad.value) {
            limpiarSelect(escuela, 'Seleccione primero una entidad');
            return;
        }

        fetch(urlEscuelas + '?entidad=' + encodeURIComponent(entidad.value))
            .then(function (r) {
                if (!r.ok) { throw new Error('No se pudieron consultar las escuelas'); }
                return r.json();
            })
            .then(function (datos) {
                const opciones = Object.entries(datos).map(function (par) {
                    return { id: par[0], nombre: par[1] };
                });
                llenarSelect(escuela, opciones, opciones.length ? 'Seleccione una escuela' : 'No hay escuelas disponibles');

                if (seleccionada) {
                    escuela.value = String(seleccionada);
                    if (escuela.value) {
                        cargarOferta();
                    }
                }
            })
            .catch(function (error) {
                console.error(error);
                limpiarSelect(escuela, 'Error al cargar escuelas');
            });
    }

    function cargarOferta() {
        limpiarSelect(licenciatura, 'Cargando licenciaturas...');
        limpiarSelect(especialidad, 'Seleccione una especialidad');
        limpiarSelect(modalidad, 'Seleccione una modalidad');
        limpiarSelect(turno, 'Seleccione un turno');
        ofertaActual = [];

        if (!escuela.value) {
            limpiarSelect(licenciatura, 'Seleccione primero una escuela');
            return;
        }

        fetch(urlOferta + '?escuela=' + encodeURIComponent(escuela.value))
            .then(function (r) {
                if (!r.ok) { throw new Error('No se pudo consultar la oferta educativa'); }
                return r.json();
            })
            .then(function (oferta) {
                ofertaActual = oferta;
                const opciones = opcionesUnicas(ofertaActual, 'id_licenciatura', 'licenciatura');
                llenarSelect(
                    licenciatura,
                    opciones,
                    opciones.length ? 'Seleccione una licenciatura' : 'No hay licenciaturas disponibles'
                );
            })
            .catch(function (error) {
                console.error(error);
                limpiarSelect(licenciatura, 'Error al cargar licenciaturas');
            });
    }

    function cargarEspecialidades() {
        const registros = ofertaActual.filter(function (registro) {
            return String(registro.id_licenciatura) === licenciatura.value;
        });

        limpiarSelect(especialidad, 'Seleccione una especialidad');
        limpiarSelect(modalidad, 'Seleccione una modalidad');
        limpiarSelect(turno, 'Seleccione un turno');

        llenarSelect(
            especialidad,
            opcionesUnicas(registros, 'id_especialidad', 'especialidad'),
            'Seleccione una especialidad'
        );
    }

    function cargarModalidadTurno() {
        const registros = ofertaActual.filter(function (registro) {
            return String(registro.id_licenciatura) === licenciatura.value &&
                   String(registro.id_especialidad) === especialidad.value;
        });

        llenarSelect(
            modalidad,
            opcionesUnicas(registros, 'modalidad', 'modalidad').map(function (opcion) {
                return { id: opcion.id, nombre: modalidades[opcion.id] || opcion.nombre };
            }),
            'Seleccione una modalidad'
        );

        llenarSelect(
            turno,
            opcionesUnicas(registros, 'turno', 'turno').map(function (opcion) {
                return { id: opcion.id, nombre: turnos[opcion.id] || opcion.nombre };
            }),
            'Seleccione un turno'
        );
    }

    function buscar() {
        if (!escuela.value) {
            contenedor.innerHTML = '<p style="color:#721c24;background:#f8d7da;padding:12px 16px;border:1px solid #f5c6cb;border-radius:4px;">Seleccione primero una escuela.</p>';
            return;
        }

        contenedor.innerHTML = '<p style="text-align:center;padding:20px;">Buscando alumnos...</p>';

        const campos = {
            captura: document.getElementById('captura').value,
            id_escuela: escuela.value,
            id_licenciatura: licenciatura.value,
            id_especialidad: especialidad.value,
            modalidad: modalidad.value,
            turno: turno.value,
            semestre: document.getElementById('semestre').value,
            grupo: document.getElementById('grupo').value.trim(),
            status: document.getElementById('status').value,
            curp: document.getElementById('curp').value.trim(),
            nid: document.getElementById('nid').value.trim()
        };

        const parametros = new URLSearchParams();
        Object.keys(campos).forEach(function (clave) {
            if (campos[clave] !== '' && campos[clave] !== null) {
                parametros.append(clave, campos[clave]);
            }
        });

        fetch(urlLista + '?' + parametros.toString())
            .then(function (r) {
                if (!r.ok) { throw new Error('HTTP ' + r.status); }
                return r.text();
            })
            .then(function (html) {
                contenedor.innerHTML = html;
            })
            .catch(function (error) {
                contenedor.innerHTML = '<p style="color:#721c24;background:#f8d7da;padding:12px 16px;border:1px solid #f5c6cb;border-radius:4px;">Error al buscar alumnos: ' + error.message + '.</p>';
            });
    }

    entidad.addEventListener('change', function () { cargarEscuelas(null); });
    escuela.addEventListener('change', cargarOferta);
    licenciatura.addEventListener('change', cargarEspecialidades);
    especialidad.addEventListener('change', cargarModalidadTurno);
    btnBuscar.addEventListener('click', buscar);

    // Si venimos de una edición, se recupera la entidad y la escuela de la URL.
    const entidadInicial = '{{ $entidad_id }}';
    const escuelaInicial = '{{ $escuela_id }}';
    if (entidadInicial && entidadInicial !== '0') {
        cargarEscuelas(escuelaInicial && escuelaInicial !== '0' ? escuelaInicial : null);
    }
});
</script>
@endpush
