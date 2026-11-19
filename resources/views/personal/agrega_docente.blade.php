@extends('layouts.siben.siben')

@section('titulo', 'Agregar docente')

@section('content')
<section class="contenedor" style="max-width: 1200px; margin: 0 auto; padding: 24px 16px;">
    <h3 style="text-align:center; color:var(--principal); margin-bottom:14px;">AGREGAR DOCENTE</h3>

    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 14px;">
            <ul style="margin:0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('personal.panel.guardar') }}">
        @csrf
        <input type="hidden" name="id_funcion" value="{{ $idFuncion }}">

        <table class="buscar01" style="width:100%; border-collapse: collapse; border:2px solid #b5164d; margin-bottom: 14px;">
            <tr><th colspan="8" style="text-align:left; padding:6px; border-bottom:2px solid #b5164d;">Información Personal</th></tr>
            <tr>
                <td style="padding:6px;">Primer Apellido</td>
                <td style="padding:6px;"><input type="text" name="apellido_paterno" class="form-control" required onblur="this.value=this.value.trim().toUpperCase()"></td>
                <td style="padding:6px;">Segundo Apellido</td>
                <td style="padding:6px;"><input type="text" name="apellido_materno" class="form-control" onblur="this.value=this.value.trim().toUpperCase()"></td>
                <td style="padding:6px;">Nombre(s)</td>
                <td colspan="3" style="padding:6px;"><input type="text" name="nombre" class="form-control" required onblur="this.value=this.value.trim().toUpperCase()"></td>
            </tr>
            <tr>
                <td style="padding:6px;">CURP</td>
                <td style="padding:6px;"><input type="text" name="curp" class="form-control" required maxlength="20" onblur="this.value=this.value.trim().toUpperCase()"></td>
                <td style="padding:6px;">Edad</td>
                <td style="padding:6px;"><input type="number" name="edad" class="form-control" min="0" max="100" required></td>
                <td style="padding:6px;">Género</td>
                <td style="padding:6px;">
                    <select name="genero" class="form-control" required>
                        <option value="H">Hombre</option>
                        <option value="M">Mujer</option>
                    </select>
                </td>
                <td style="padding:6px;">Correo electrónico</td>
                <td style="padding:6px;"><input type="email" name="correo_electronico" class="form-control" onblur="this.value=this.value.trim().toLowerCase()"></td>
            </tr>
        </table>

        <table class="buscar01" style="width:100%; border-collapse: collapse; border:2px solid #b5164d; margin-bottom: 14px;">
            <tr><th colspan="6" style="text-align:left; padding:6px; border-bottom:2px solid #b5164d;">Estudios realizados</th></tr>
            <tr>
                <td style="padding:6px;">Nivel de estudio</td>
                <td style="padding:6px;">
                    <select name="id_nivel_estudio" class="form-control" required>
                        <option value="">Seleccione</option>
                        @foreach($nivelesEstudio as $id => $nivel)
                            <option value="{{ $id }}">{{ $nivel }}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding:6px;">Titulado</td>
                <td style="padding:6px;">
                    <select name="titulado" id="titulado" class="form-control" required>
                        <option value="s">Sí</option>
                        <option value="n">No</option>
                    </select>
                </td>
                <td style="padding:6px;">Fecha de titulación</td>
                <td style="padding:6px;"><input type="date" name="fecha_titulacion" id="fecha_titulacion" class="form-control"></td>
            </tr>
            <tr>
                <td style="padding:6px;">Tema de titulación</td>
                <td colspan="2" style="padding:6px;"><input type="text" name="tema_titulacion" id="tema_titulacion" class="form-control"></td>
                <td style="padding:6px;">Escuela de egreso</td>
                <td colspan="2" style="padding:6px;"><input type="text" name="escuela_egreso" class="form-control" onblur="this.value=this.value.trim().toUpperCase()"></td>
            </tr>
            <tr>
                <td style="padding:6px;">Área de conocimiento</td>
                <td style="padding:6px;">
                    <select name="area_conocimiento" class="form-control" id="area_conocimiento">
                        <option value="">Seleccione</option>
                        @foreach($areasConocimiento as $id => $area)
                            <option value="{{ $id }}">{{ $area }}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding:6px;">Disciplina</td>
                <td style="padding:6px;">
                    <select name="disciplina" class="form-control" required>
                        <option value="">Seleccione</option>
                        @foreach($disciplinas as $id => $disciplina)
                            <option value="{{ $id }}">{{ $disciplina }}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding:6px;">Nivel SNI</td>
                <td style="padding:6px;">
                    <select name="id_nivel_sni" class="form-control">
                        <option value="0">Seleccione</option>
                        @foreach($nivelesSni as $id => $nivelSni)
                            <option value="{{ $id }}">{{ $nivelSni }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
        </table>

        <table class="buscar01" style="width:100%; border-collapse: collapse; border:2px solid #b5164d; margin-bottom: 14px; max-width: 860px; margin-left:auto; margin-right:auto;">
            <tr><th colspan="4" style="text-align:left; padding:6px; border-bottom:2px solid #b5164d;">Lengua Indígena</th></tr>
            <tr>
                <td style="padding:6px;">¿Habla alguna lengua indígena?</td>
                <td style="padding:6px; width:190px;">
                    <select name="habla_lengua" id="habla_lengua" class="form-control">
                        <option value="n">No</option>
                        <option value="s">Sí</option>
                    </select>
                </td>
                <td style="padding:6px;">¿Cuál?</td>
                <td style="padding:6px;"><input type="text" name="lengua_indigena" id="lengua_indigena" class="form-control" onblur="this.value=this.value.trim()"></td>
            </tr>
        </table>

        <table class="buscar01" style="width:100%; border-collapse: collapse; border:2px solid #b5164d; margin-bottom: 14px; max-width: 860px; margin-left:auto; margin-right:auto;">
            <tr><th colspan="4" style="text-align:left; padding:6px; border-bottom:2px solid #b5164d;">Investigaciones realizadas</th></tr>
            <tr>
                <td style="padding:6px;">¿Participa en algún proyecto de investigación?</td>
                <td style="padding:6px; width:190px;">
                    <select name="investigacion" id="investigacion" class="form-control">
                        <option value="n">No</option>
                        <option value="s">Sí</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="padding:6px;">Proyecto de investigación en el que participa</td>
                <td colspan="3" style="padding:6px;"><input type="text" name="proyecto_investigacion" id="proyecto_investigacion" class="form-control" maxlength="128"></td>
            </tr>
            <tr>
                <td style="padding:6px;">¿Ha realizado alguna estancia?</td>
                <td style="padding:6px;">
                    <select name="estancia_investigacion" class="form-control">
                        <option value="n">No</option>
                        <option value="s">Sí</option>
                    </select>
                </td>
            </tr>
        </table>

        <table class="buscar01" style="width:100%; border-collapse: collapse; border:2px solid #b5164d; margin-bottom: 14px; max-width: 860px; margin-left:auto; margin-right:auto;">
            <tr><th colspan="4" style="text-align:left; padding:6px; border-bottom:2px solid #b5164d;">Antigüedad del docente</th></tr>
            <tr>
                <td style="padding:6px;">Antigüedad en esta Escuela (año)</td>
                <td style="padding:6px; width:190px;">
                    <select name="antiguedad_escuela" class="form-control">
                        <option value="">Seleccione</option>
                        @foreach($anios as $anio)
                                <option value="{{ $anio }}">{{ $anio }}</option>
                            @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td style="padding:6px;">Antigüedad como Docente Normalista (año)</td>
                <td style="padding:6px;">
                    <select name="antiguedad_docente" class="form-control">
                        <option value="">Seleccione</option>
                        @foreach($anios as $anio)
                                <option value="{{ $anio }}">{{ $anio }}</option>
                            @endforeach
                    </select>
                </td>
            </tr>
        </table>

        <table class="buscar01" style="width:100%; border-collapse: collapse; border:2px solid #b5164d; margin-bottom: 14px; max-width: 860px; margin-left:auto; margin-right:auto;">
            <tr><th colspan="4" style="text-align:left; padding:6px; border-bottom:2px solid #b5164d;">Discapacidad</th></tr>
            <tr>
                <td style="padding:6px;">¿El docente/directivo sufre alguna discapacidad?</td>
                <td style="padding:6px; width:190px;">
                    <select name="discapacidad" id="discapacidad" class="form-control">
                        <option value="">Seleccione</option>
                        <option value="n">No</option>
                        <option value="s">Sí</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="padding:6px;">¿Cuál?</td>
                <td style="padding:6px;">
                    <select name="tipo_discapacidad" id="tipo_discapacidad" class="form-control">
                        <option value="0">Seleccione</option>
                        @foreach($discapacidades as $idDisc => $nombreDisc)
                            <option value="{{ $idDisc }}">{{ $nombreDisc }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
        </table>

        <div style="display:flex; justify-content:center; gap:10px; margin-top: 16px;">
            <a href="{{ route('personal.panel.agrega') }}" class="boton__secundario" style="text-decoration:none;">Anterior</a>
            <button type="submit" class="boton__primario">Guardar</button>
            <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration:none;">Inicio</a>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
(function () {
    const titulado = document.getElementById('titulado');
    const fecha = document.getElementById('fecha_titulacion');
    const tema = document.getElementById('tema_titulacion');
    const habla = document.getElementById('habla_lengua');
    const lengua = document.getElementById('lengua_indigena');

    function toggleTitulacion() {
        const mostrar = titulado.value === 's';
        fecha.disabled = !mostrar;
        tema.disabled = !mostrar;
        if (!mostrar) {
            fecha.value = '';
            tema.value = '';
        }
    }

    function toggleLengua() {
        const mostrar = habla.value === 's';
        lengua.disabled = !mostrar;
        if (!mostrar) {
            lengua.value = '';
        }
    }

    const investigacion = document.getElementById('investigacion');
    const proyecto = document.getElementById('proyecto_investigacion');
    const discapacidad = document.getElementById('discapacidad');
    const tipoDiscapacidad = document.getElementById('tipo_discapacidad');

    function toggleProyecto() {
        if (!investigacion) return;
        const mostrar = investigacion.value === 's';
        proyecto.disabled = !mostrar;
        if (!mostrar) {
            proyecto.value = '';
        }
    }

    function toggleDiscapacidad() {
        if (!discapacidad) return;
        const mostrar = discapacidad.value === 's';
        tipoDiscapacidad.disabled = !mostrar;
        if (!mostrar) {
            tipoDiscapacidad.value = '0';
        }
    }

    titulado.addEventListener('change', toggleTitulacion);
    habla.addEventListener('change', toggleLengua);
    investigacion.addEventListener('change', toggleProyecto);
    discapacidad.addEventListener('change', toggleDiscapacidad);
    toggleTitulacion();
    toggleLengua();
    toggleProyecto();
    toggleDiscapacidad();
})();
</script>
@endpush
