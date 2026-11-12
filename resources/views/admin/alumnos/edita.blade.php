@extends('layouts.siben.siben')

@section('titulo', 'Edición de Alumno — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    {{-- Regresar a la búsqueda --}}
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.alumnos.busqueda', ['entidad' => $entidad_id, 'escuela' => $registro->id_escuela]) }}"
           class="boton__secundario"
           style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">
            &larr; Regresar a la búsqueda
        </a>
    </div>

    <div class="buscar01" style="margin-bottom: 24px; padding: 24px; background: #fff; border: 1px solid var(--neutro400); border-radius: 4px;">

        <div style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 20px;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">
                Edición de Alumno
                <span style="font-size: 0.95rem; color: var(--neutro600); font-weight: normal;">
                    (NID {{ $alumno->numero_identificador }})
                </span>
            </h3>
        </div>

        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.alumnos.actualizar') }}" id="formEdicionAlumno">
            @csrf

            <input type="hidden" name="nid" value="{{ $alumno->numero_identificador }}">
            <input type="hidden" name="id_registro" value="{{ $registro->id }}">
            <input type="hidden" name="id_entidad" value="{{ $entidad_id }}">

            {{-- SECCIÓN: ESCUELA (solo lectura) --}}
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
                    <div>
                        <span style="display: block; font-size: 0.85rem; color: var(--neutro600); font-weight: 600;">Periodo (captura):</span>
                        <span style="font-size: 0.95rem; color: var(--neutro900); font-weight: 600;">{{ $registro->captura }}</span>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN: INFORMACIÓN DEL ALUMNO --}}
            <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">
                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Información del Alumno
                </div>
                <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; background-color: #fff;">

                    <div>
                        <label for="id_licenciatura" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Licenciatura *</label>
                        <select name="id_licenciatura" id="id_licenciatura" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @foreach($licenciaturas as $id => $nombre)
                                <option value="{{ $id }}" @selected((int) old('id_licenciatura', $registro->id_licenciatura) === (int) $id)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_especialidad" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Especialidad *</label>
                        <select name="id_especialidad" id="id_especialidad" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @foreach($especialidades as $id => $nombre)
                                <option value="{{ $id }}" @selected((int) old('id_especialidad', $registro->id_especialidad) === (int) $id)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="modalidad" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Modalidad *</label>
                        <select name="modalidad" id="modalidad" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @foreach($modalidades as $id => $nombre)
                                <option value="{{ $id }}" @selected(old('modalidad', $registro->modalidad) === (string) $id)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="turno" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Turno *</label>
                        <select name="turno" id="turno" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @foreach($turnos as $id => $nombre)
                                <option value="{{ $id }}" @selected(old('turno', $registro->turno) === (string) $id)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="semestre" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Semestre *</label>
                        <select name="semestre" id="semestre" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @foreach($semestres as $valor)
                                <option value="{{ $valor }}" @selected((int) old('semestre', $registro->semestre) === (int) $valor)>{{ $valor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="grupo" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Grupo</label>
                        <input type="text" name="grupo" id="grupo" maxlength="5"
                               value="{{ old('grupo', $registro->grupo) }}"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="matricula" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Matrícula</label>
                        <input type="text" name="matricula" id="matricula" maxlength="14"
                               value="{{ old('matricula', $registro->matricula) }}"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="status" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Estatus *</label>
                        <select name="status" id="status" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @foreach($status as $id => $nombre)
                                <option value="{{ $id }}" @selected(old('status', $registro->status) === (string) $id)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- SECCIÓN: INFORMACIÓN PERSONAL --}}
            <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">
                <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                    Información Personal
                </div>
                <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; background-color: #fff;">

                    <div>
                        <label for="apellido_paterno" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Primer Apellido *</label>
                        <input type="text" name="apellido_paterno" id="apellido_paterno" required maxlength="60"
                               value="{{ old('apellido_paterno', $alumno->apellido_paterno) }}"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="apellido_materno" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Segundo Apellido</label>
                        <input type="text" name="apellido_materno" id="apellido_materno" maxlength="60"
                               value="{{ old('apellido_materno', $alumno->apellido_materno) }}"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="nombre" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Nombre(s) *</label>
                        <input type="text" name="nombre" id="nombre" required maxlength="60"
                               value="{{ old('nombre', $alumno->nombre) }}"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="curp" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">CURP *</label>
                        <input type="text" name="curp" id="curp" required maxlength="18"
                               value="{{ old('curp', $alumno->curp) }}"
                               onblur="this.value = this.value.trim().toUpperCase();"
                               style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="genero" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Género *</label>
                        <select name="genero" id="genero" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="H" @selected(old('genero', $alumno->genero) === 'H')>Masculino</option>
                            <option value="M" @selected(old('genero', $alumno->genero) === 'M')>Femenino</option>
                            <option value="X" @selected(old('genero', $alumno->genero) === 'X')>No especificado</option>
                        </select>
                    </div>

                    <div>
                        <label for="edad" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Edad *</label>
                        <input type="number" name="edad" id="edad" required min="1" max="99"
                               value="{{ old('edad', $alumno->edad) }}"
                               style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="id_renapo" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Entidad de Nacimiento *</label>
                        <select name="id_renapo" id="id_renapo" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @php
                                $renapoActual = old('id_renapo');
                                if ($renapoActual === null) {
                                    $renapoActual = \Illuminate\Support\Facades\DB::table('cat_entidades')
                                        ->where('id', $alumno->entidad_nacimiento)
                                        ->value('id_renapo');
                                }
                            @endphp
                            @foreach($entidadesRenapo as $clave => $nombre)
                                <option value="{{ $clave }}" @selected((string) $renapoActual === (string) $clave)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="nacionalidad" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Nacionalidad *</label>
                        <select name="nacionalidad" id="nacionalidad" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            <option value="m" @selected(old('nacionalidad', $alumno->nacionalidad) === 'm')>Mexicano</option>
                            <option value="e" @selected(old('nacionalidad', $alumno->nacionalidad) === 'e')>Extranjero</option>
                        </select>
                    </div>

                    <div>
                        <label for="id_documento" style="display: block; font-size: 0.9rem; color: var(--neutro800); font-weight: 600; margin-bottom: 6px;">Documentación *</label>
                        <select name="id_documento" id="id_documento" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--neutro400); border-radius: 4px; font-size: 0.9rem;">
                            @foreach($documentos as $id => $nombre)
                                <option value="{{ $id }}" @selected((int) old('id_documento', $alumno->id_documento) === (int) $id)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            <button type="submit" class="boton__primario" style="padding: 10px 28px; font-size: 0.95rem; cursor: pointer;">
                Guardar cambios
            </button>

        </form>
    </div>

</div>

@endsection
