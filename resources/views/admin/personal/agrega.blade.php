@extends('layouts.siben.siben')

@section('titulo', 'Altas de Personal — SIBEN')

@section('content')


<div id="area_contenido" style="margin-left:170px; padding:10px;">

    <a href="{{ route('admin.index') }}"
       style="display:inline-block; margin-bottom:12px; padding:6px 12px; background:#611232; color:#fff; text-decoration:none; border-radius:4px;">
        &larr; Panel
    </a>

<div class="panel-titulo">Altas de Personal</div>

@if(session('success'))
    <div class="alerta-exito">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alerta-error">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.personal.guardar') }}" id="formPersonal">
    @csrf

    {{-- Escuela --}}
    <table class="datos">
        <tr><th colspan="2">Escuela</th></tr>
        <tr>
            <td>Entidad:</td>
            <td>
                <select name="id_entidad" id="id_entidad" required>
                    <option value="">Seleccione</option>
                    @foreach($entidades as $id => $entidad)
                        <option value="{{ $id }}">{{ $entidad }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td>Escuela:</td>
            <td>
                <select name="id_escuela" id="id_escuela" disabled required>
                    <option value="">Primero seleccione la entidad</option>
                </select>
            </td>
        </tr>
    </table>

    <br>

    {{-- Datos básicos --}}
    <table class="datos">
        <tr><th colspan="4">Información Personal</th></tr>
        <tr>
            <td>Apellido Paterno</td>
            <td><input type="text" name="apellido_paterno" class="texto" required></td>
            <td>Apellido Materno</td>
            <td><input type="text" name="apellido_materno" class="texto"></td>
        </tr>
        <tr>
            <td>Nombre(s)</td>
            <td colspan="3"><input type="text" name="nombre" class="texto" size="35" required></td>
        </tr>
        <tr>
            <td>CURP</td>
            <td><input type="text" name="curp" id="curp" class="texto" size="25" maxlength="18" required></td>
            <td>Edad</td>
            <td><input type="number" name="edad" min="0" max="100" required></td>
        </tr>
        <tr>
            <td>Género</td>
            <td>
                <select name="genero">
                    <option value="h">Hombre</option>
                    <option value="m" selected>Mujer</option>
                </select>
            </td>
            <td>Correo electrónico</td>
            <td><input type="email" name="correo_electronico"></td>
        </tr>
        <tr>
            <td>Función</td>
            <td colspan="3">
                <select name="id_funcion" id="id_funcion" required>
                    <option value="">Seleccione</option>
                    @foreach($funciones as $id => $funcion)
                        <option value="{{ $id }}">{{ $funcion }}</option>
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
                            <option value="{{ $id }}">{{ $nivel }}</option>
                        @endforeach
                    </select>
                    <select name="titulo" id="titulo">
                        <option value="s">Titulado</option>
                        <option value="n">Sin título</option>
                    </select>
                </td>
            </tr>
            <tr id="filaTitulacion">
                <th colspan="2">Fecha y tema de titulación</th>
            </tr>
            <tr id="campoTitulacion">
                <td colspan="2">
                    <input type="date" name="fecha_titulacion">
                    <input type="text" name="tema_titulacion" size="40" placeholder="Tema de titulación">
                </td>
            </tr>
            <tr><th colspan="2">Escuela de Egreso</th></tr>
            <tr><td colspan="2"><input type="text" name="escuela_egreso" size="40"></td></tr>
            <tr><th colspan="2">Área de Conocimiento y Disciplina</th></tr>
            <tr>
                <td colspan="2">
                    <select name="id_area_conocimiento">
                        <option value="">Seleccione</option>
                        @foreach($areas->groupBy('id_campo') as $campo => $grupo)
                            <optgroup label="{{ $grupo->first()->campo }}">
                                @foreach($grupo as $area)
                                    <option value="{{ $area->id }}">{{ $area->disciplina }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr><th colspan="2">¿Habla alguna lengua indígena?</th></tr>
            <tr>
                <td colspan="2">
                    <select name="lengua" id="lengua">
                        <option value="n">No</option>
                        <option value="s">Sí</option>
                    </select>
                    <input type="text" name="lengua_indigena" id="campoLengua" size="50" placeholder="Lengua indígena" style="display:none;">
                </td>
            </tr>
            <tr><th colspan="2">¿Participa en algún proyecto de investigación?</th></tr>
            <tr>
                <td colspan="2">
                    <select name="investigacion" id="investigacion">
                        <option value="n">No</option>
                        <option value="s">Sí</option>
                    </select>
                    <input type="text" name="proyecto_investigacion" id="campoProyecto" size="40" placeholder="Proyecto de investigación" style="display:none;">
                </td>
            </tr>
            <tr><th colspan="2">¿Ha realizado alguna estancia?</th></tr>
            <tr>
                <td colspan="2">
                    <select name="estancia_investigacion">
                        <option value="n">No</option>
                        <option value="s">Sí</option>
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
                            <option value="{{ $i }}">{{ $i }} años</option>
                        @endfor
                    </select>
                </td>
                <td>
                    <select name="antiguedad_docente">
                        @for($i = 0; $i <= 50; $i++)
                            <option value="{{ $i }}">{{ $i }} años</option>
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
                    <option value="">Seleccione</option>
                    <option value="n">No</option>
                    <option value="s">Sí</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Tipo de discapacidad</td>
            <td colspan="2">
                <select name="tipo_discapacidad" id="campoDiscapacidad" style="display:none;">
                    <option value="">Seleccione</option>
                    @foreach($discapacidades as $id => $disc)
                        <option value="{{ $id }}">{{ $disc }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
    </table>

    <br>
    <center><button type="submit">Guardar</button></center>
</form>

<script>
    // 1) Escuela dependiente de la entidad
    document.getElementById('id_entidad').addEventListener('change', function () {
        const escuela = document.getElementById('id_escuela');
        escuela.innerHTML = '<option value="">Cargando…</option>';
        escuela.disabled = true;

        const entidad = this.value;
        if (!entidad) { escuela.innerHTML = '<option value="">Primero seleccione la entidad</option>'; return; }

        fetch('{{ route('admin.personal.escuelas') }}?entidad=' + entidad)
            .then(r => r.json())
            .then(data => {
                let opciones = '<option value="">Seleccione</option>';
                for (const [id, nombre] of Object.entries(data)) {
                    opciones += '<option value="' + id + '">' + nombre + '</option>';
                }
                escuela.innerHTML = opciones;
                escuela.disabled = false;
            });
    });

    // 2) Mostrar/ocultar sección docente según función (1-6)
    function esDocente() {
        const v = parseInt(document.getElementById('id_funcion').value);
        document.getElementById('seccionDocente').style.display = (v >= 1 && v <= 6) ? '' : 'none';
    }
    document.getElementById('id_funcion').addEventListener('change', esDocente);
    esDocente();

    // 3) Titulación solo si es titulado
    document.getElementById('titulo').addEventListener('change', function () {
        const esTitulado = this.value === 's';
        document.getElementById('filaTitulacion').style.display = esTitulado ? '' : 'none';
        document.getElementById('campoTitulacion').style.display = esTitulado ? '' : 'none';
    });

    // 4) Lengua / Proyecto / Discapacidad
    document.getElementById('lengua').addEventListener('change', function () {
        document.getElementById('campoLengua').style.display = this.value === 's' ? '' : 'none';
    });
    document.getElementById('investigacion').addEventListener('change', function () {
        document.getElementById('campoProyecto').style.display = this.value === 's' ? '' : 'none';
    });
    document.getElementById('discapacidad').addEventListener('change', function () {
        document.getElementById('campoDiscapacidad').style.display = this.value === 's' ? '' : 'none';
    });

    // 5) Mayúsculas en entradas de texto (como el original) y CURP
    document.querySelectorAll('input.texto').forEach(el => {
        el.addEventListener('blur', () => { el.value = el.value.trim().toUpperCase(); });
    });
</script>
</div>
@endsection

<style>
    .panel-titulo { font-size: 22px; font-weight: 600; color: #611232; border-bottom: 3px solid #611232; padding-bottom: 8px; margin-bottom: 20px; }
    table.datos { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
    table.datos th { background: #611232; color: #fff; padding: 8px 12px; text-align: left; border: 1px solid #611232; }
    table.datos td { padding: 8px 12px; border: 1px solid #ddd; }
    .alerta-exito { background: #d4edda; color: #155724; padding: 10px 14px; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 15px; }
    .alerta-error { background: #f8d7da; color: #721c24; padding: 10px 14px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 15px; }
</style>