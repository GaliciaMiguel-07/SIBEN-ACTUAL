@php
    $usuario = session('Usuario', []);
    $nivel = $usuario['nivel'] ?? '';
@endphp

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

                @if($nivel == 2)

                    <tr>
                        <td>Conflictos</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Directorios</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Avance alumnos</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Avance personal</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Supervisores</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Directores</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Alumnos</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Consulta personal</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Listados</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Estadísticas</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                @elseif($nivel == 3)

                    <tr>
                        <td>Configurar parámetros</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Datos generales</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Alumnos</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Listados</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Estadísticas</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                @elseif($nivel == 4)

                    <tr>
                        <td>Datos generales</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Alumnos</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Personal</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Listados</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Estadísticas</td>
                        <td><a href="#" class="btn btn-primary btn-sm">Acceder</a></td>
                    </tr>

                @endif

            </tbody>

        </table>
    </div>

</div>

<style>
    .menu-siben {
        width: 100%;
        margin: 25px 0;
    }

    .menu-titulo {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .tabla-opciones {
        width: 100%;
        margin-bottom: 0;
    }

    .tabla-opciones th {
        font-weight: bold;
    }

    .tabla-opciones td {
        vertical-align: middle;
    }

    .columna-acceso {
        width: 120px;
        text-align: center;
    }

    .tabla-opciones td:last-child {
        text-align: center;
    }

    @media (max-width: 600px) {
        .menu-siben {
            margin: 20px 0;
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
    }
</style>