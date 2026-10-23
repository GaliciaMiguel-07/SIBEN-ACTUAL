@extends('layouts.siben.siben')

@section('titulo', 'Altas de Personal — SIBEN')

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
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">Altas de Personal</h3>
        </div>

        {{-- Mensajes flash de alertas --}}
        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.personal.guardar') }}" id="formPersonal">
            @csrf

            {{-- Sección: Escuela --}}
            <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">
                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Escuela
                </div>
                <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; background-color: #fff;">
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Entidad:</label>
                        <select name="id_entidad" id="id_entidad" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="">Seleccione</option>
                            @foreach($entidades as $id => $entidad)
                                <option value="{{ $id }}">{{ $entidad }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Escuela:</label>
                        <select name="id_escuela" id="id_escuela" disabled required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="">Primero seleccione la entidad</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Sección: Datos básicos --}}
            <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">
                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Información Personal
                </div>
                <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; background-color: #fff;">
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Apellido Paterno *</label>
                        <input type="text" name="apellido_paterno" class="texto" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Apellido Materno</label>
                        <input type="text" name="apellido_materno" class="texto" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Nombre(s) *</label>
                        <input type="text" name="nombre" class="texto" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">CURP *</label>
                        <input type="text" name="curp" id="curp" class="texto" maxlength="18" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Edad *</label>
                        <input type="number" name="edad" min="0" max="100" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Género</label>
                        <select name="genero" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="h">Hombre</option>
                            <option value="m" selected>Mujer</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Correo electrónico</label>
                        <input type="email" name="correo_electronico" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Función *</label>
                        <select name="id_funcion" id="id_funcion" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="">Seleccione</option>
                            @foreach($funciones as $id => $funcion)
                                <option value="{{ $id }}">{{ $funcion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Sección docente (solo función 1-6) --}}
            <div id="seccionDocente" style="display:none; margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">
                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Información Académica y Docente
                </div>
                <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; background-color: #fff;">
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Nivel máximo de estudios</label>
                        <div style="display: flex; gap: 8px;">
                            <select name="id_nivel_estudio" style="flex: 1; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                                <option value="">Seleccione</option>
                                @foreach($niveles as $id => $nivel)
                                    <option value="{{ $id }}">{{ $nivel }}</option>
                                @endforeach
                            </select>
                            <select name="titulo" id="titulo" style="width: 120px; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                                <option value="s">Titulado</option>
                                <option value="n">Sin título</option>
                            </select>
                        </div>
                    </div>

                    <div id="campoTitulacion" style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Fecha y tema de titulación</label>
                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                            <input type="date" name="fecha_titulacion" style="padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <input type="text" name="tema_titulacion" placeholder="Tema de titulación" style="flex: 1; min-width: 240px; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Escuela de Egreso</label>
                        <input type="text" name="escuela_egreso" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Área de Conocimiento y Disciplina</label>
                        <select name="id_area_conocimiento" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="">Seleccione</option>
                            @foreach($areas->groupBy('id_campo') as $campo => $grupo)
                                <optgroup label="{{ $grupo->first()->campo }}">
                                    @foreach($grupo as $area)
                                        <option value="{{ $area->id }}">{{ $area->disciplina }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">¿Habla alguna lengua indígena?</label>
                        <div style="display: flex; gap: 8px;">
                            <select name="lengua" id="lengua" style="width: 90px; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                                <option value="n">No</option>
                                <option value="s">Sí</option>
                            </select>
                            <input type="text" name="lengua_indigena" id="campoLengua" placeholder="Especificar lengua indígena" style="display:none; flex: 1; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">¿Participa en proyecto de investigación?</label>
                        <div style="display: flex; gap: 8px;">
                            <select name="investigacion" id="investigacion" style="width: 90px; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                                <option value="n">No</option>
                                <option value="s">Sí</option>
                            </select>
                            <input type="text" name="proyecto_investigacion" id="campoProyecto" placeholder="Proyecto de investigación" style="display:none; flex: 1; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">¿Ha realizado alguna estancia?</label>
                        <select name="estancia_investigacion" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="n">No</option>
                            <option value="s">Sí</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Antigüedad en esta Escuela</label>
                        <select name="antiguedad_escuela" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @for($i = 0; $i <= 50; $i++)
                                <option value="{{ $i }}">{{ $i }} años</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Antigüedad como Docente Normalista</label>
                        <select name="antiguedad_docente" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @for($i = 0; $i <= 50; $i++)
                                <option value="{{ $i }}">{{ $i }} años</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            {{-- Sección: Discapacidad --}}
            <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">
                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Discapacidad
                </div>
                <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; background-color: #fff;">
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">¿El personal sufre alguna discapacidad?</label>
                        <select name="discapacidad" id="discapacidad" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="">Seleccione</option>
                            <option value="n">No</option>
                            <option value="s">Sí</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Tipo de discapacidad</label>
                        <select name="tipo_discapacidad" id="campoDiscapacidad" style="display:none; width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="">Seleccione</option>
                            @foreach($discapacidades as $id => $disc)
                                <option value="{{ $id }}">{{ $disc }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Botón Guardar --}}
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="boton__primario" style="padding: 10px 30px; font-size: 1rem; cursor: pointer;">
                    Guardar
                </button>
            </div>
        </form>

    </div>

</div>

@endsection

@push('styles')
<link href="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd-guinda.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd.js"></script>
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
        document.getElementById('campoTitulacion').style.display = esTitulado ? '' : 'none';
    });

    // 4) Lengua / Proyecto / Discapacidad
    document.getElementById('lengua').addEventListener('change', function () {
        document.getElementById('campoLengua').style.display = this.value === 's' ? 'inline-block' : 'none';
    });
    document.getElementById('investigacion').addEventListener('change', function () {
        document.getElementById('campoProyecto').style.display = this.value === 's' ? 'inline-block' : 'none';
    });
    document.getElementById('discapacidad').addEventListener('change', function () {
        document.getElementById('campoDiscapacidad').style.display = this.value === 's' ? 'block' : 'none';
    });

    // 5) Mayúsculas en entradas de texto y CURP
    document.querySelectorAll('input.texto').forEach(el => {
        el.addEventListener('blur', () => { el.value = el.value.trim().toUpperCase(); });
    });
</script>
@endpush