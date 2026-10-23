@extends('layouts.siben.siben')

@section('titulo', 'Edición de Personal — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 1000px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    <!-- Botón de regreso a la lista -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.personal.busqueda', ['entidad' => $entidad_id, 'escuela' => $p->id_escuela]) }}" 
           class="boton__secundario" style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">
            &larr; Regresar a la lista
        </a>
    </div>

    <!-- Tarjeta contenedora principal -->
    <div class="buscar01" style="margin-bottom: 24px; padding: 24px; background: #fff; border: 1px solid var(--neutro400); border-radius: 4px;">

        <div style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 20px;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">
                Edición de Personal <span style="font-size: 0.95rem; color: var(--neutro600); font-weight: normal;">(Registro #{{ $p->id }})</span>
            </h3>
        </div>

        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.personal.actualizar') }}" id="formEdicion">
            @csrf

            {{-- Identificadores ocultos --}}
            <input type="hidden" name="id" value="{{ $p->id }}">
            <input type="hidden" name="id_entidad" value="{{ $entidad_id }}">

            {{-- Sección: Escuela (solo lectura) --}}
            <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">
                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Escuela
                </div>
                <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; background-color: var(--neutro100);">
                    <div>
                        <span style="display: block; font-size: 0.85rem; color: var(--neutro600); font-weight: 600;">Entidad:</span>
                        <span style="font-size: 0.95rem; color: var(--neutro900); font-weight: 600;">{{ $entidades[$entidad_id] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span style="display: block; font-size: 0.85rem; color: var(--neutro600); font-weight: 600;">Escuela:</span>
                        <span style="font-size: 0.95rem; color: var(--neutro900); font-weight: 600;">{{ $escuela->escuela ?? 'N/A' }}</span>
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
                        <input type="text" name="apellido_paterno" class="texto" value="{{ old('apellido_paterno', $p->apellido_paterno) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Apellido Materno</label>
                        <input type="text" name="apellido_materno" class="texto" value="{{ old('apellido_materno', $p->apellido_materno) }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Nombre(s) *</label>
                        <input type="text" name="nombre" class="texto" value="{{ old('nombre', $p->nombre) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">CURP *</label>
                        <input type="text" name="curp" class="texto" maxlength="18" value="{{ old('curp', $p->curp) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Edad *</label>
                        <input type="number" name="edad" min="0" max="100" value="{{ old('edad', $p->edad) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Género</label>
                        <select name="genero" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="h" @selected(old('genero', $p->genero) === 'h')>Hombre</option>
                            <option value="m" @selected(old('genero', $p->genero) === 'm')>Mujer</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Correo electrónico</label>
                        <input type="email" name="correo_electronico" value="{{ old('correo_electronico', $p->correo_electronico) }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Función *</label>
                        <select name="id_funcion" id="id_funcion" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="">Seleccione</option>
                            @foreach($funciones as $id => $funcion)
                                <option value="{{ $id }}" @selected((string)old('id_funcion', $p->id_funcion) === (string)$id)>{{ $funcion }}</option>
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
                                    <option value="{{ $id }}" @selected((string)old('id_nivel_estudio', $d->id_nivel_estudio ?? '') === (string)$id)>{{ $nivel }}</option>
                                @endforeach
                            </select>
                            <select name="titulo" id="titulo" style="width: 120px; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                                <option value="s" @selected(old('titulo', $d->titulo ?? 's') === 's')>Titulado</option>
                                <option value="n" @selected(old('titulo', $d->titulo ?? 's') === 'n')>Sin título</option>
                            </select>
                        </div>
                    </div>

                    <div id="campoTitulacion" style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Fecha y tema de titulación</label>
                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                            <input type="date" name="fecha_titulacion" value="{{ old('fecha_titulacion', ($d->fecha_titulacion ?? '') !== '0000-00-00' ? $d->fecha_titulacion : '') }}" style="padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <input type="text" name="tema_titulacion" placeholder="Tema de titulación" value="{{ old('tema_titulacion', $d->tema_titulacion ?? '') }}" style="flex: 1; min-width: 240px; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Escuela de Egreso</label>
                        <input type="text" name="escuela_egreso" value="{{ old('escuela_egreso', $d->escuela_egreso ?? '') }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Área de Conocimiento y Disciplina</label>
                        <select name="id_area_conocimiento" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="">Seleccione</option>
                            @foreach($areas->groupBy('id_campo') as $campo => $grupo)
                                <optgroup label="{{ $grupo->first()->campo }}">
                                    @foreach($grupo as $area)
                                        <option value="{{ $area->id }}" @selected((string)old('id_area_conocimiento', $d->id_area_conocimiento ?? '') === (string)$area->id)>{{ $area->disciplina }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    @php
                        $lang = ($d && !empty($d->lengua_indigena)) ? 's' : 'n';
                        $inv  = ($d && !empty($d->proyecto_investigacion)) ? 's' : 'n';
                    @endphp

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">¿Habla alguna lengua indígena?</label>
                        <div style="display: flex; gap: 8px;">
                            <select name="lengua" id="lengua" style="width: 90px; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                                <option value="n" @selected(old('lengua', $lang) === 'n')>No</option>
                                <option value="s" @selected(old('lengua', $lang) === 's')>Sí</option>
                            </select>
                            <input type="text" name="lengua_indigena" id="campoLengua" placeholder="Especificar lengua indígena" value="{{ old('lengua_indigena', $d->lengua_indigena ?? '') }}" style="display:none; flex: 1; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">¿Participa en proyecto de investigación?</label>
                        <div style="display: flex; gap: 8px;">
                            <select name="investigacion" id="investigacion" style="width: 90px; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                                <option value="n" @selected(old('investigacion', $inv) === 'n')>No</option>
                                <option value="s" @selected(old('investigacion', $inv) === 's')>Sí</option>
                            </select>
                            <input type="text" name="proyecto_investigacion" id="campoProyecto" placeholder="Proyecto de investigación" value="{{ old('proyecto_investigacion', $d->proyecto_investigacion ?? '') }}" style="display:none; flex: 1; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">¿Ha realizado alguna estancia?</label>
                        <select name="estancia_investigacion" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="n" @selected(old('estancia_investigacion', $d->estancia_investigacion ?? 'n') === 'n')>No</option>
                            <option value="s" @selected(old('estancia_investigacion', $d->estancia_investigacion ?? 'n') === 's')>Sí</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Antigüedad en esta Escuela</label>
                        <select name="antiguedad_escuela" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @for($i = 0; $i <= 50; $i++)
                                <option value="{{ $i }}" @selected((string)old('antiguedad_escuela', $antEscuela) === (string)$i)>{{ $i }} años</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Antigüedad como Docente Normalista</label>
                        <select name="antiguedad_docente" style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @for($i = 0; $i <= 50; $i++)
                                <option value="{{ $i }}" @selected((string)old('antiguedad_docente', $antDocente) === (string)$i)>{{ $i }} años</option>
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
                            <option value="" @selected(old('discapacidad', $p->discapacidad ?? '') === '')>Seleccione</option>
                            <option value="n" @selected(old('discapacidad', $p->discapacidad ?? '') === 'n')>No</option>
                            <option value="s" @selected(old('discapacidad', $p->discapacidad ?? '') === 's')>Sí</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Tipo de discapacidad</label>
                        <select name="tipo_discapacidad" id="campoDiscapacidad" style="display:none; width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="">Seleccione</option>
                            @foreach($discapacidades as $id => $disc)
                                <option value="{{ $id }}" @selected((string)old('tipo_discapacidad', $p->tipo_discapacidad ?? '') === (string)$id)>{{ $disc }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div style="display: flex; justify-content: center; gap: 15px; margin-top: 30px;">
                <button type="submit" class="boton__primario" style="padding: 10px 24px; font-size: 0.95rem; cursor: pointer;">
                    Guardar cambios
                </button>
                <a href="{{ route('admin.personal.busqueda', ['entidad' => $entidad_id, 'escuela' => $p->id_escuela]) }}"
                   class="boton__secundario" style="text-decoration: none; padding: 10px 24px; font-size: 0.95rem; display: inline-block;">
                    Cancelar
                </a>
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
    // 1) Mostrar/ocultar sección docente según función (1-6)
    function esDocente() {
        const v = parseInt(document.getElementById('id_funcion').value);
        document.getElementById('seccionDocente').style.display = (v >= 1 && v <= 6) ? 'block' : 'none';
    }
    document.getElementById('id_funcion').addEventListener('change', esDocente);

    // 2) Titulación solo si es titulado
    function esTitulado() {
        const t = document.getElementById('titulo').value === 's';
        document.getElementById('campoTitulacion').style.display = t ? 'block' : 'none';
    }
    document.getElementById('titulo').addEventListener('change', esTitulado);

    // 3) Lengua / Proyecto / Discapacidad
    function condicionales() {
        document.getElementById('campoLengua').style.display =
            document.getElementById('lengua').value === 's' ? 'inline-block' : 'none';
        document.getElementById('campoProyecto').style.display =
            document.getElementById('investigacion').value === 's' ? 'inline-block' : 'none';
        document.getElementById('campoDiscapacidad').style.display =
            document.getElementById('discapacidad').value === 's' ? 'block' : 'none';
    }
    document.getElementById('lengua').addEventListener('change', condicionales);
    document.getElementById('investigacion').addEventListener('change', condicionales);
    document.getElementById('discapacidad').addEventListener('change', condicionales);

    // 4) Mayúsculas en entradas de texto y CURP
    document.querySelectorAll('input.texto').forEach(el => {
        el.addEventListener('blur', () => { el.value = el.value.trim().toUpperCase(); });
    });

    // 5) Inicializar el estado visible al cargar
    esDocente();
    esTitulado();
    condicionales();
</script>
@endpush