@extends('layouts.siben.siben')

@section('titulo', 'Edición de Personal — SIBEN')

@section('content')

<div id="area_contenido" style="margin-left:170px; padding:10px;">

    <a href="{{ route('admin.personal.busqueda', ['entidad' => $entidad_id, 'escuela' => $p->id_escuela]) }}"
       style="display:inline-block; margin-bottom:12px; padding:6px 12px; background:#611232; color:#fff; text-decoration:none; border-radius:4px;">
        &larr; Regresar a la lista
    </a>

    <div class="panel-titulo">Edición de Personal (registro #{{ $p->id }})</div>

    @if($errors->any())
        <div class="alerta-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.personal.actualizar') }}" id="formEdicion">
        @csrf

        {{-- Identificadores ocultos --}}
        <input type="hidden" name="id" value="{{ $p->id }}">
        <input type="hidden" name="id_entidad" value="{{ $entidad_id }}">

        {{-- Escuela (solo lectura) --}}
        <table class="datos">
            <tr><th colspan="2">Escuela</th></tr>
            <tr>
                <td>Entidad:</td>
                <td>{{ $entidades[$entidad_id] ?? '' }}</td>
            </tr>
            <tr>
                <td>Escuela:</td>
                <td>{{ $escuela->escuela ?? '' }}</td>
            </tr>
        </table>

        <br>

        {{-- Datos básicos --}}
        <table class="datos">
            <tr><th colspan="4">Información Personal</th></tr>
            <tr>
                <td>Apellido Paterno</td>
                <td><input type="text" name="apellido_paterno" class="texto" value="{{ old('apellido_paterno', $p->apellido_paterno) }}" required></td>
                <td>Apellido Materno</td>
                <td><input type="text" name="apellido_materno" class="texto" value="{{ old('apellido_materno', $p->apellido_materno) }}"></td>
            </tr>
            <tr>
                <td>Nombre(s)</td>
                <td colspan="3"><input type="text" name="nombre" class="texto" size="35" value="{{ old('nombre', $p->nombre) }}" required></td>
            </tr>
            <tr>
                <td>CURP</td>
                <td><input type="text" name="curp" class="texto" size="25" maxlength="18" value="{{ old('curp', $p->curp) }}" required></td>
                <td>Edad</td>
                <td><input type="number" name="edad" min="0" max="100" value="{{ old('edad', $p->edad) }}" required></td>
            </tr>
            <tr>
                <td>Género</td>
                <td>
                    <select name="genero">
                        <option value="h" @selected(old('genero', $p->genero) === 'h')>Hombre</option>
                        <option value="m" @selected(old('genero', $p->genero) === 'm')>Mujer</option>
                    </select>
                </td>
                <td>Correo electrónico</td>
                <td><input type="email" name="correo_electronico" value="{{ old('correo_electronico', $p->correo_electronico) }}"></td>
            </tr>
            <tr>
                <td>Función</td>
                <td colspan="3">
                    <select name="id_funcion" id="id_funcion" required>
                        <option value="">Seleccione</option>
                        @foreach($funciones as $id => $funcion)
                            <option value="{{ $id }}" @selected((string)old('id_funcion', $p->id_funcion) === (string)$id)>{{ $funcion }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
        </table>

        {{-- Sección docente (solo función 1-6) --}}
        <div id="seccionDocente" style="display:none;">
            <br>
            <table class="datos">
                <tr><th colspan="2">Nivel máximo de estudios</th></tr>
                <tr>
                    <td colspan="2">
                        <select name="id_nivel_estudio">
                            <option value="">Seleccione</option>
                            @foreach($niveles as $id => $nivel)
                                <option value="{{ $id }}" @selected((string)old('id_nivel_estudio', $d->id_nivel_estudio ?? '') === (string)$id)>{{ $nivel }}</option>
                            @endforeach
                        </select>
                        <select name="titulo" id="titulo">
                            <option value="s" @selected(old('titulo', $d->titulo ?? 's') === 's')>Titulado</option>
                            <option value="n" @selected(old('titulo', $d->titulo ?? 's') === 'n')>Sin título</option>
                        </select>
                    </td>
                </tr>
                <tr id="filaTitulacion">
                    <th colspan="2">Fecha y tema de titulación</th>
                </tr>
                <tr id="campoTitulacion">
                    <td colspan="2">
                        <input type="date" name="fecha_titulacion"
                               value="{{ old('fecha_titulacion', ($d->fecha_titulacion ?? '') !== '0000-00-00' ? $d->fecha_titulacion : '') }}">
                        <input type="text" name="tema_titulacion" size="40" placeholder="Tema de titulación"
                               value="{{ old('tema_titulacion', $d->tema_titulacion ?? '') }}">
                    </td>
                </tr>
                <tr><th colspan="2">Escuela de Egreso</th></tr>
                <tr><td colspan="2"><input type="text" name="escuela_egreso" size="40" value="{{ old('escuela_egreso', $d->escuela_egreso ?? '') }}"></td></tr>
                <tr><th colspan="2">Área de Conocimiento y Disciplina</th></tr>
                <tr>
                    <td colspan="2">
                        <select name="id_area_conocimiento">
                            <option value="">Seleccione</option>
                            @foreach($areas->groupBy('id_campo') as $campo => $grupo)
                                <optgroup label="{{ $grupo->first()->campo }}">
                                    @foreach($grupo as $area)
                                        <option value="{{ $area->id }}" @selected((string)old('id_area_conocimiento', $d->id_area_conocimiento ?? '') === (string)$area->id)>{{ $area->disciplina }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </td>
                </tr>

                @php
                    $lang = ($d && !empty($d->lengua_indigena)) ? 's' : 'n';
                    $inv  = ($d && !empty($d->proyecto_investigacion)) ? 's' : 'n';
                @endphp

                <tr><th colspan="2">¿Habla alguna lengua indígena?</th></tr>
                <tr>
                    <td colspan="2">
                        <select name="lengua" id="lengua">
                            <option value="n" @selected(old('lengua', $lang) === 'n')>No</option>
                            <option value="s" @selected(old('lengua', $lang) === 's')>Sí</option>
                        </select>
                        <input type="text" name="lengua_indigena" id="campoLengua" size="50" placeholder="Lengua indígena"
                               style="display:none;" value="{{ old('lengua_indigena', $d->lengua_indigena ?? '') }}">
                    </td>
                </tr>
                <tr><th colspan="2">¿Participa en algún proyecto de investigación?</th></tr>
                <tr>
                    <td colspan="2">
                        <select name="investigacion" id="investigacion">
                            <option value="n" @selected(old('investigacion', $inv) === 'n')>No</option>
                            <option value="s" @selected(old('investigacion', $inv) === 's')>Sí</option>
                        </select>
                        <input type="text" name="proyecto_investigacion" id="campoProyecto" size="40" placeholder="Proyecto de investigación"
                               style="display:none;" value="{{ old('proyecto_investigacion', $d->proyecto_investigacion ?? '') }}">
                    </td>
                </tr>
                <tr><th colspan="2">¿Ha realizado alguna estancia?</th></tr>
                <tr>
                    <td colspan="2">
                        <select name="estancia_investigacion">
                            <option value="n" @selected(old('estancia_investigacion', $d->estancia_investigacion ?? 'n') === 'n')>No</option>
                            <option value="s" @selected(old('estancia_investigacion', $d->estancia_investigacion ?? 'n') === 's')>Sí</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Antigüedad en esta Escuela</th>
                    <th>Antigüedad como Docente Normalista</th>
                </tr>
                <tr>
                    <td>
                        <select name="antiguedad_escuela">
                            @for($i = 0; $i <= 50; $i++)
                                <option value="{{ $i }}" @selected((string)old('antiguedad_escuela', $antEscuela) === (string)$i)>{{ $i }} años</option>
                            @endfor
                        </select>
                    </td>
                    <td>
                        <select name="antiguedad_docente">
                            @for($i = 0; $i <= 50; $i++)
                                <option value="{{ $i }}" @selected((string)old('antiguedad_docente', $antDocente) === (string)$i)>{{ $i }} años</option>
                            @endfor
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <br>

        {{-- Discapacidad (común a todos) --}}
        <table class="datos">
            <tr><th colspan="3">Discapacidad</th></tr>
            <tr>
                <td>¿El personal sufre alguna discapacidad?</td>
                <td colspan="2">
                    <select name="discapacidad" id="discapacidad">
                        <option value="" @selected(old('discapacidad', $p->discapacidad ?? '') === '')>Seleccione</option>
                        <option value="n" @selected(old('discapacidad', $p->discapacidad ?? '') === 'n')>No</option>
                        <option value="s" @selected(old('discapacidad', $p->discapacidad ?? '') === 's')>Sí</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tipo de discapacidad</td>
                <td colspan="2">
                    <select name="tipo_discapacidad" id="campoDiscapacidad" style="display:none;">
                        <option value="">Seleccione</option>
                        @foreach($discapacidades as $id => $disc)
                            <option value="{{ $id }}" @selected((string)old('tipo_discapacidad', $p->tipo_discapacidad ?? '') === (string)$id)>{{ $disc }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
        </table>

        <br>
        <center>
            <button type="submit">Guardar cambios</button>
            &nbsp;
            <a href="{{ route('admin.personal.busqueda', ['entidad' => $entidad_id, 'escuela' => $p->id_escuela]) }}"
               style="padding:6px 14px; background:#6c757d; color:#fff; text-decoration:none; border-radius:4px;">Cancelar</a>
        </center>
    </form>

</div>

<script>
    // 1) Mostrar/ocultar sección docente según función (1-6)
    function esDocente() {
        const v = parseInt(document.getElementById('id_funcion').value);
        document.getElementById('seccionDocente').style.display = (v >= 1 && v <= 6) ? '' : 'none';
    }
    document.getElementById('id_funcion').addEventListener('change', esDocenteapse);

    // 2) Titulación solo si es titulado
    function esTitulado() {
        const t = document.getElementById('titulo').value === 's';
        document.getElementById('filaTitulacion').style.display = t ? '' : 'none';
        document.getElementById('campoTitulacion').style.display = t ? '' : 'none';
    }
    document.getElementById('titulo').addEventListener('change', esTitulado);

    // 3) Lengua / Proyecto / Discapacidad
    function condicionales() {
        document.getElementById('campoLengua').style.display =
            document.getElementById('lengua').value === 's' ? '' : 'none';
        document.getElementById('campoProyecto').style.display =
            document.getElementById('investigacion').value === 's' ? '' : 'none';
        document.getElementById('campoDiscapacidad').style.display =
            document.getElementById('discapacidad').value === 's' ? '' : 'none';
    }
    document.getElementById('lengua').addEventListener('change', condicionales);
    document.getElementById('investigacion').addEventListener('change', condicionales);
    document.getElementById('discapacidad').addEventListener('change', condicionalesvalid);

    // 4) Mayúsculas en entradas de texto y CURP
    document.querySelectorAll('input.texto').forEach(el => {
        el.addEventListener('blur', () => { el.value = el.value.trim().toUpperCase(); });
    });

    // 5) Inicializar el estado visible al cargar
    esDocente();
    esTitulado();
    condicionales();
</script>
@endsection

<style>
    .panel-titulo { font-size: 22px; font-weight: 600; color: #611232; border-bottom: 3px solid #611232; padding-bottom: 8px; margin-bottom: 20px; }
    table.datos { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
    table.datos th { background: #611232; color: #fff; padding: 8px 12px; text-align: left; border: 1px solid #611232; }
    table.datos td { padding: 8px 12px; border: 1px solid #ddd; }
    .alerta-error { background: #f8d7da; color: #721c24; padding: 10px 14px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 15px; }
</style>