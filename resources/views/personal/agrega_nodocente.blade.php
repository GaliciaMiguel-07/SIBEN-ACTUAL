@extends('layouts.siben.siben')

@section('titulo', 'Agregar personal')

@section('content')
<section class="contenedor" style="max-width: 1100px; margin: 0 auto; padding: 24px 16px;">
    <h3 style="text-align:center; color:var(--principal); margin-bottom:14px;">AGREGAR PERSONAL</h3>

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
