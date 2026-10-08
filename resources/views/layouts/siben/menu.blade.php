@php
    $usuario = session('Usuario', []);

    $nivel = (int) ($usuario['nivel'] ?? auth()->user()->nivel ?? 0);
@endphp

<div class="container-fluid">

    <div class="row">

        {{-- La tabla ocupará 8 de las 12 columnas --}}
        <div class="col-md-8">

            <div class="menu-siben">

                <div class="menu-titulo">
                    Opciones del sistema
                </div>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped tabla-opciones">

                        <thead>
                            <tr>
                                <th>Opción</th>
                                <th class="columna-acceso">Acceso</th>
                            </tr>
                        </thead>

                        <tbody>

                            {{-- =====================================================
                                 NIVEL 1
                            ====================================================== --}}
                            @if($nivel === 1)

                                <tr>
                                    <td>Edición</td>
                                    <td><a href="{{ route('admin.index') }}" class="btn-acceso">Acceder</a></td>
                                </tr>

                                <tr>
                                    <td>Mensajes</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Directorio</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Estadísticas</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Informe nacional</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Cambiar captura</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Lista usuarios</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Informes escuelas</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Consulta histórico</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Consulta docente</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Prueba Verhoeff</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>


                            {{-- =====================================================
                                 NIVEL 2 - SUPERVISOR ESTATAL
                            ====================================================== --}}
                            @elseif($nivel === 2)

                                <tr>
                                    <td>Conflictos</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Directorios</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Avance alumnos</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Avance personal</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Supervisores</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Directores</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Alumnos</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Consulta personal</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Listados</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Estadísticas</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>


                            {{-- =====================================================
                                 NIVEL 3 - SUPERVISOR DE ESCUELA
                            ====================================================== --}}
                            @elseif($nivel === 3)

                                <tr>
                                    <td>Configurar parámetros</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Datos generales</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Alumnos</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Listados</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Estadísticas</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>


                            {{-- =====================================================
                                 NIVEL 4 - CAPTURISTA
                            ====================================================== --}}
                            @elseif($nivel === 4)

                                <tr>
                                    <td>Datos generales</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Alumnos</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Personal</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Listados</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Estadísticas</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>

                            @endif

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================
         ACCIONES ADICIONALES
    ================================================================= --}}

    <div class="row">

        <div class="col-md-8">

            <div class="acciones-siben">

                @if(in_array($nivel, [2, 3, 4]))

                    <a href="#" class="btn btn-default btn-sm">
                        Descargar archivos
                    </a>

                @endif

            </div>

        </div>

    </div>

</div>


<style>

    .menu-siben {
        margin-top: 25px;
        margin-bottom: 15px;
    }

    .menu-titulo {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .tabla-opciones {
        width: 100%;
        margin-bottom: 15px;
    }

    .tabla-opciones th {
        font-weight: bold;
    }

    .tabla-opciones td {
        vertical-align: middle;
    }

    .columna-acceso {
        width: 110px;
        text-align: center;
    }

    .tabla-opciones td:last-child {
        text-align: center;
    }

    .acciones-siben {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    @media (max-width: 767px) {

        .menu-siben {
            margin-top: 20px;
        }

        .menu-titulo {
            font-size: 18px;
        }

        .tabla-opciones {
            font-size: 14px;
        }

        .tabla-opciones .btn {
            font-size: 12px;
        }

        .columna-acceso {
            width: 90px;
        }

        .acciones-siben {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }

        .acciones-siben .btn {
            width: 100%;
        }

    }

</style>