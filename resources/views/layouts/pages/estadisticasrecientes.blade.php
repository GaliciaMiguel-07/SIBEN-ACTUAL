<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/escuelas.css') }}">
    <title>Estadísticas — SIBEN</title>
    
    <x-header />

    <!-- Estilo oficial de pastillas (tabs) y ajuste de tarjeta a ancho completo -->
    <style>
        .pastillas-container {
            display: flex;
            flex-wrap: wrap;
            border-bottom: 1px solid var(--neutro400);
            gap: 0;
            margin-bottom: 24px;
            padding: 0;
            list-style: none;
        }

        .pastilla-btn {
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 500;
            color: var(--neutro800);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .pastilla-btn:hover {
            background-color: var(--neutro300);
            color: var(--principal);
        }

        .pastilla-btn.active,
        .pastilla-btn[aria-selected="true"] {
            color: var(--principal);
            font-weight: 700;
            border-bottom-color: var(--principal);
            background-color: #fff;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
            border-left: 1px solid var(--neutro400);
            border-right: 1px solid var(--neutro400);
            border-top: 1px solid var(--neutro400);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeInTab 0.3s ease-in-out;
        }

        @keyframes fadeInTab {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Forzar a que la tabla de estadísticas ocupe el 100% real de la retícula */
        .tabla-estadisticas {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--neutro100);
            border-radius: 8px;
            border: 1px solid var(--neutro400);
        }

        .tabla-estadisticas td {
            width: 100%;
            display: block;
            padding: 14px 20px;
        }

        .tabla-estadisticas tr:not(:last-child) td {
            border-bottom: 1px solid var(--neutro400);
        }
    </style>
</head>
<body>

    <!-- Migas de pan oficiales SND -->
    <div class="contenedor mt--24">
        <nav class="ruta__cont" aria-label="Ruta de navegación">
            <ol class="ruta__lista">
                <li class="ruta__li">
                    <a class="ruta__a" href="{{ url('/') }}" aria-label="Inicio">
                        <i class="snd__icono snd-home icono--24" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="ruta__li" aria-current="page">
                    Estadísticas
                </li>
            </ol>
        </nav>
    </div>

    <div class="contenedor pt--20 pb--60">
        <section>
            <h2 class="h2r patria">Estadísticas</h2>
            <hr class="red divider mb--32">

            <!-- Retícula principal de 12 columnas SND -->
            <div class="reticulaGrid__12 gap--16">
                
                <!-- Pastillas / Tabs ocupando las 12 columnas -->
                <div class="columna__12--md columna__12--xs">
                    <div role="tablist" class="pastillas-container">
                        <button class="pastilla-btn active" onclick="openTab(event, 'ciclo-25-26')" role="tab" aria-selected="true">2025-2026</button>
                        <button class="pastilla-btn" onclick="openTab(event, 'ciclo-24-25')" role="tab" aria-selected="false">2024-2025</button>
                        <button class="pastilla-btn" onclick="openTab(event, 'ciclo-23-24')" role="tab" aria-selected="false">2023-2024</button>
                        <button class="pastilla-btn" onclick="openTab(event, 'ciclo-22-23')" role="tab" aria-selected="false">2022-2023</button>
                        <button class="pastilla-btn" onclick="openTab(event, 'ciclo-21-22')" role="tab" aria-selected="false">2021-2022</button>
                    </div>
                </div>

                <!-- Bloques de Contenido por Tab ocupando las 12 columnas a ancho completo -->
                <div class="columna__12--md columna__12--xs">
                    
                    <!-- CONTENIDO TAB 1: Ciclo 2025-2026 -->
                    <div id="ciclo-25-26" class="tab-content active" role="tabpanel">
                        <div class="sh1b mb--16 color--neutro800">Ciclo escolar 2025-2026</div>
                        <div class="deslizable--scroll">
                            <table class="tabla-estadisticas">
                                <tbody>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2025/M2526xsem.xlsx">Matrícula por semestre</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2025/M2526xlic.xlsx">Matrícula por licenciatura</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2025/M2526xedad.xlsx">Matrícula por Edad</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2025/M2526xsexo.xlsx">Matrícula por Sexo</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2025/P2526xfuncion.xlsx">Personal por función</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2025/P2526xfc.xlsx">Personal Académico por forma de contratación</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2025/P2526xtd.xlsx">Personal Académico por tiempo de dedicación</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2025/P2526xne.xlsx">Personal Académico por nivel de estudio</a></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- CONTENIDO TAB 2: Ciclo 2024-2025 -->
                    <div id="ciclo-24-25" class="tab-content" role="tabpanel">
                        <div class="sh1b mb--16 color--neutro800">Ciclo escolar 2024-2025</div>
                        <div class="deslizable--scroll">
                            <table class="tabla-estadisticas">
                                <tbody>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2024/M2425xsem.xlsx">Matrícula por semestre</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2024/M2425xlic.xlsx">Matrícula por licenciatura</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2024/M2425xedad.xlsx">Matrícula por Edad</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2024/M2425xsexo.xlsx">Matrícula por Sexo</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2024/P2425xfuncion.xlsx">Personal por función</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2024/P2425xfc.xlsx">Personal Académico por forma de contratación</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2024/P2425xtd.xlsx">Personal Académico por tiempo de dedicación</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2024/P2425xne.xlsx">Personal Académico por nivel de estudio</a></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- CONTENIDO TAB 3: Ciclo 2023-2024 -->
                    <div id="ciclo-23-24" class="tab-content" role="tabpanel">
                        <div class="sh1b mb--16 color--neutro800">Ciclo escolar 2023-2024</div>
                        <div class="deslizable--scroll">
                            <table class="tabla-estadisticas">
                                <tbody>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2023/M2324xsem.xlsx">Matrícula por semestre</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2023/M2324xlic.xlsx">Matrícula por licenciatura</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2023/M2324xedad.xlsx">Matrícula por Edad</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2023/M2324xsexo.xlsx">Matrícula por Sexo</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2023/P2324xfuncion.xlsx">Personal por función</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2023/P2324xfc.xlsx">Personal Académico por forma de contratación</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2023/P2324xtd.xlsx">Personal Académico por tiempo de dedicación</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2023/P2324xne.xlsx">Personal Académico por nivel de estudio</a></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- CONTENIDO TAB 4: Ciclo 2022-2023 -->
                    <div id="ciclo-22-23" class="tab-content" role="tabpanel">
                        <div class="sh1b mb--16 color--neutro800">Ciclo escolar 2022-2023</div>
                        <div class="deslizable--scroll">
                            <table class="tabla-estadisticas">
                                <tbody>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2022/M2223xsem.xlsx">Matrícula por semestre</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2022/M2223xlic.xlsx">Matrícula por licenciatura</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2022/M2223xedad.xlsx">Matrícula por Edad</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2022/M2223xsexo.xlsx">Matrícula por Sexo</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2022/P2223xfuncion.xlsx">Personal por función</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2022/P2223xfc.xlsx">Personal Académico por forma de contratación</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2022/P2223xtd.xlsx">Personal Académico por tiempo de dedicación</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2022/P2223xne.xlsx">Personal Académico por nivel de estudio</a></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- CONTENIDO TAB 5: Ciclo 2021-2022 -->
                    <div id="ciclo-21-22" class="tab-content" role="tabpanel">
                        <div class="sh1b mb--16 color--neutro800">Ciclo escolar 2021-2022</div>
                        <div class="deslizable--scroll">
                            <table class="tabla-estadisticas">
                                <tbody>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2021/M2122xsem.xlsx">Matrícula por semestre</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2021/M2122xlic.xlsx">Matrícula por licenciatura</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2021/M2122xedad.xlsx">Matrícula por Edad</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2021/M2122xsexo.xlsx">Matrícula por Sexo</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2021/P2122xfuncion.xlsx">Personal por función</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2021/P2122xfc.xlsx">Personal Académico por forma de contratación</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2021/P2122xtd.xlsx">Personal Académico por tiempo de dedicación</a></td></tr>
                                    <tr><td><a href="https://siben.sep.gob.mx/siben/files/estadistica2021/P2122xne.xlsx">Personal Académico por nivel de estudio</a></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <!-- Script nativo para el cambio de pastillas -->
    <script>
        function openTab(evt, tabId) {
            const contents = document.querySelectorAll(".tab-content");
            contents.forEach(content => content.classList.remove("active"));

            const buttons = document.querySelectorAll(".pastilla-btn");
            buttons.forEach(btn => {
                btn.classList.remove("active");
                btn.setAttribute("aria-selected", "false");
            });

            document.getElementById(tabId).classList.add("active");
            evt.currentTarget.classList.add("active");
            evt.currentTarget.setAttribute("aria-selected", "true");
        }
    </script>

</body>
</html>