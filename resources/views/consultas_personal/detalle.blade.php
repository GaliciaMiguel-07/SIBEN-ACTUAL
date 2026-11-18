@extends('layouts.siben.siben')

@section('titulo', 'Detalle de personal')

@section('content')
<section class="contenedor" style="max-width: 1020px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">
    <div class="buscar01" style="margin-bottom: 20px; overflow-x:auto;">
        <table style="width: 100%; border-collapse: collapse; border: 1px solid var(--principal); margin-bottom: 18px;">
            <tr>
                <td style="padding: 8px; width: 180px;">Entidad:</td>
                <td style="padding: 8px;">{{ $entidadNombre ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Escuela:</td>
                <td style="padding: 8px;">{{ $personal->escuela->escuela ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Status:</td>
                <td style="padding: 8px;">{{ $statusTexto }}</td>
            </tr>
            <tr>
                <th colspan="2" style="padding: 8px; text-align: left; border-top: 1px solid var(--principal); border-bottom: 1px solid var(--principal);">Información general del personal</th>
            </tr>
            <tr>
                <td style="padding: 8px;">Primer Apellido:</td>
                <td style="padding: 8px;">{{ $personal->apellido_paterno }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Segundo Apellido:</td>
                <td style="padding: 8px;">{{ $personal->apellido_materno }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Nombre:</td>
                <td style="padding: 8px;">{{ $personal->nombre }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">CURP:</td>
                <td style="padding: 8px;">{{ $personal->curp }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Edad:</td>
                <td style="padding: 8px;">{{ $personal->edad }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Género:</td>
                <td style="padding: 8px;">{{ $generoTexto }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Función:</td>
                <td style="padding: 8px;">{{ $personal->funcion->funcion ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Correo electrónico:</td>
                <td style="padding: 8px;">{{ $personal->correo_electronico }}</td>
            </tr>

            <tr>
                <th colspan="2" style="padding: 8px; text-align: left; border-top: 1px solid var(--principal); border-bottom: 1px solid var(--principal);">Estudios realizados</th>
            </tr>
            <tr>
                <td style="padding: 8px;">Nivel de estudio:</td>
                <td style="padding: 8px;">{{ $nivelEstudio->nivel_estudio ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Titulado:</td>
                <td style="padding: 8px;">{{ (($docente->titulo ?? 'n') === 's') ? 'Sí' : 'No' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Fecha de titulación:</td>
                <td style="padding: 8px;">{{ $docente->fecha_titulacion ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Tema de titulación:</td>
                <td style="padding: 8px;">{{ $docente->tema_titulacion ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Escuela de egreso:</td>
                <td style="padding: 8px;">{{ $docente->escuela_egreso ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Área de conocimiento:</td>
                <td style="padding: 8px;">{{ $area->campo ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Disciplina:</td>
                <td style="padding: 8px;">{{ $area->disciplina ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px;">Nivel SNI:</td>
                <td style="padding: 8px;">{{ $nivelSni->nivel_sni ?? '' }}</td>
            </tr>

            <tr>
                <th colspan="2" style="padding: 8px; text-align: left; border-top: 1px solid var(--principal); border-bottom: 1px solid var(--principal);">Lenguas Indígenas habladas</th>
            </tr>
            <tr>
                <td colspan="2" style="padding: 8px;">
                    @if(count($lenguas))
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($lenguas as $lengua)
                                <li>{{ $lengua }}</li>
                            @endforeach
                        </ul>
                    @else
                        No se registraron lenguas indígenas para este personal.
                    @endif
                </td>
            </tr>
        </table>

        <div style="text-align: center; display: flex; justify-content: center; gap: 10px;">
            <a href="{{ $regresar }}" class="boton__secundario" style="text-decoration: none; display: inline-block;">Regresar</a>
            <a href="{{ route('dashboard') }}" class="boton__secundario" style="text-decoration: none; display: inline-block;">Inicio</a>
        </div>
    </div>
</section>
@endsection
