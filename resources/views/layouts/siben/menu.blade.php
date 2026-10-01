@php
    $usuario = session('Usuario', []);

    $nivel = (int) ($usuario['nivel'] ?? auth()->user()->nivel ?? 0);
@endphp

<div class="menu-siben">

    <div class="menu-titulo">
        Opciones del sistema
    </div>

    <div class="table-responsive">

        <table class="tabla-opciones">

            <thead>
                <tr>
                    <th>Opción</th>
                    <th class="columna-acceso">Acceso</th>
                </tr>
            </thead>

            <tbody>

                {{-- NIVEL 2 - SUPERVISOR ESTATAL --}}
                @if($nivel === 2)

                    <tr>
                        <td>Conflictos</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Directorios</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Avance alumnos</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Avance personal</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Supervisores</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Directores</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Alumnos</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Consulta personal</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Listados</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Estadísticas</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                {{-- NIVEL 3 - SUPERVISOR DE ESCUELA --}}
                @elseif($nivel === 3)

                    <tr>
                        <td>Configurar parámetros</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Datos generales</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Alumnos</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Listados</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Estadísticas</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                {{-- NIVEL 4 - CAPTURISTA --}}
                @elseif($nivel === 4)

                    <tr>
                        <td>Datos generales</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Alumnos</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Personal</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Listados</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                    <tr>
                        <td>Estadísticas</td>
                        <td><a href="#" class="btn-acceso">Acceder</a></td>
                    </tr>

                @endif

            </tbody>

        </table>

    </div>


    {{-- ACCIONES ADICIONALES --}}

    <div class="acciones-siben">

        @if(in_array($nivel, [2, 3, 4]))

            <a href="#" class="btn-descargar">
                Descargar archivos
            </a>

        @endif

    </div>

</div>


<style>

    /* =========================================================
       ESTILO GENERAL DEL MENÚ
    ========================================================= */

    .menu-siben {
        width: 100%;
        margin: 30px auto;
        font-family: "Montserrat", "Helvetica Neue", Helvetica, Arial, sans-serif;
        color: #404040;
    }


    /* =========================================================
       TÍTULO
    ========================================================= */

    .menu-titulo {
        font-size: 24px;
        font-weight: 600;
        color: #404040;
        border-bottom: 3px solid #611232;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }


    /* =========================================================
       TABLA
    ========================================================= */

    .tabla-opciones {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
        border: 1px solid #dddddd;
        font-size: 15px;
    }


    /* ENCABEZADO */

    .tabla-opciones thead th {
        background: #611232;
        color: #ffffff;
        font-weight: 600;
        text-align: left;
        padding: 13px 15px;
        border: 1px solid #611232;
    }

    .tabla-opciones thead th.columna-acceso {
        text-align: center;
    }


    /* CELDAS */

    .tabla-opciones tbody td {
        padding: 12px 15px;
        border: 1px solid #dddddd;
        vertical-align: middle;
    }


    /* FILAS */

    .tabla-opciones tbody tr:nth-child(even) {
        background-color: #f7f7f7;
    }

    .tabla-opciones tbody tr:hover {
        background-color: #eeeeee;
    }


    /* COLUMNA ACCESO */

    .tabla-opciones td:last-child {
        width: 120px;
        text-align: center;
    }


    /* =========================================================
       BOTÓN ACCEDER
    ========================================================= */

    .btn-acceso {
        display: inline-block;
        padding: 7px 16px;
        background-color: #611232;
        color: #ffffff !important;
        border: 1px solid #611232;
        border-radius: 4px;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-acceso:hover {
        background-color: #4d0e27;
        border-color: #4d0e27;
        color: #ffffff !important;
    }


    /* =========================================================
       BOTÓN DESCARGAR
    ========================================================= */

    .acciones-siben {
        margin-top: 20px;
    }

    .btn-descargar {
        display: inline-block;
        padding: 9px 18px;
        background-color: #ffffff;
        color: #611232 !important;
        border: 2px solid #611232;
        border-radius: 4px;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-descargar:hover {
        background-color: #611232;
        color: #ffffff !important;
    }


    /* =========================================================
       RESPONSIVO
    ========================================================= */

    @media (max-width: 600px) {

        .menu-siben {
            margin: 20px 0;
        }

        .menu-titulo {
            font-size: 20px;
        }

        .tabla-opciones {
            font-size: 14px;
        }

        .tabla-opciones tbody td {
            padding: 10px;
        }

        .btn-acceso {
            padding: 6px 10px;
            font-size: 13px;
        }

        .tabla-opciones td:last-child {
            width: 90px;
        }

    }

</style>