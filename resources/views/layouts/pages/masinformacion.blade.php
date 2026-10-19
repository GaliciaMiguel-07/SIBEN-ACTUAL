<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Más Información — SIBEN</title>
    
    <x-header />

    <!-- Estilos exactos según la maqueta del Design System -->
    <style>
        body {
            font-family: 'Noto Sans', system-ui, -apple-system, sans-serif;
            color: #161a1d;
            background-color: #ffffff;
        }

        .seccion-mas-info {
            padding-top: 30px;
            padding-bottom: 60px;
        }

        .titulo-seccion {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
            color: #161a1d;
            margin-top: 0;
            margin-bottom: 20px;
            border-top: 1px solid #e0e0e0;
            padding-top: 15px;
        }

        /* Contenedor del Acordeón */
        .acordeon-contenedor {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        .acordeon-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #ffffff;
            overflow: hidden;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .acordeon-header {
            width: 100%;
            padding: 16px 20px;
            background: #ffffff;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
            font-weight: 700;
            color: #161a1d;
            cursor: pointer;
            text-align: left;
            transition: background-color 0.2s ease;
        }

        .acordeon-header:hover {
            background-color: #f9f9f9;
        }

        /* Ícono SVG de flecha del Design System */
        .acordeon-icono {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            transition: transform 0.3s ease;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%20161a1d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
        }

        .acordeon-item.activo .acordeon-icono {
            transform: rotate(180deg);
        }

        .acordeon-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0, 1, 0, 1), padding 0.3s ease;
            padding: 0 20px;
            background-color: #ffffff;
        }

        .acordeon-item.activo .acordeon-body {
            max-height: 2000px;
            padding: 16px 20px 20px 20px;
            border-top: 1px solid #f0f0f0;
        }


        .acordeon-body ul {
            padding-left: 20px;
        }

        .acordeon-body li {
            margin-bottom: 6px;
        }

        .acordeon-body a {
            color: #9b2247;
            text-decoration: underline;
        }

        .acordeon-body a:hover {
            color: #611232;
        }

        .icon-pdf {
            width: 16px;
            vertical-align: middle;
            margin-left: 4px;
        }
    </style>
</head>
<body>
    <br><br>
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="{{ url('/') }}"><i class="icon icon-home"></i></a></li>
            <li><a href="{{ url('/') }}">Inicio</a></li>
            <li class="active">Más Información</li>
        </ol>
    </div>

    <div class="contenedor" seccion-mas-info" style="max-width: 1200px;">
        <div class="row">
            <!-- Columna Izquierda: Título -->
            <div class="col-md-4 col-sm-12">
                <h2 class="h2b patria">Más información</h2>
            </div>

            <!-- Columna Derecha: Acordeón -->
            <div class="col-md-8 col-sm-12">
                <div class="acordeon-contenedor" id="acordeonSiben">

                    <!-- Item 1 -->
                    <div class="acordeon-item">
                        <button class="acordeon-header" type="button">
                            <h4 class="sh1b">¿Qué es el SIBEN?</h4>
                            <span class="acordeon-icono"></span>
                        </button>
                        <div class="acordeon-body">
                            <p class="b1m">Es un programa que se utiliza para facilitar la consulta y actualización de la información de las Escuelas Normales a nivel nacional. Concentra los principales datos de las escuelas, sus docentes y alumnos; además de fungir como un directorio en línea y ofrecer estadísticas de interés, disponibles a las escuelas normales y responsables de las normales. Próximamente permitirá obtener datos de las unidades y programas de posgrado que existen en las normales, también estará disponible una herramienta que permitirá ubicar localizar geográficamente las mismas escuelas.</p>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="acordeon-item">
                        <button class="acordeon-header" type="button">
                            <h4 class="sh1b">Antecedentes</h4>
                            <span class="acordeon-icono"></span>
                        </button>
                        <div class="acordeon-body">
                            <p class="b1m">Hasta 2005 se recolectaba información de las escuelas normales mediante cuestionarios que se enviaban a las autoridades educativas locales en forma impresa y en disco magnético, éstas lo enviaban a las escuelas donde se llenaba el formato y se regresaba a la Dirección General, muchas veces estos formatos llegaban al finalizar el ciclo escolar lo que hacía poco oportuna la información, al llegar se capturaba en Access lo que implicaba una posible fuente de error por la misma captura, cuando se detectaba información inconsistente nadie en las autoridades educativas locales ni en las escuelas sabían quien se había hecho cargo del asunto, lo que impedía confirmar o corregir el dato, lo que nos lleva a entender que la información era poco confiable.</p>
                            <p class="b1m">Por lo anterior se decidió que fuera en línea lo que evita recapturas y permite tener bien identificadas a las personas responsables de la información, actualmente se otorga un mes para actualizar su información lo que hizo más breve el tiempo para tener los datos.</p>
                            <p class="b1m">El Sistema de Información Básica de la Educación Normal empezó su funcionamiento en el ciclo escolar 2006-2007, donde se recababa la información más importante de los estudiantes y docentes frente a grupo.</p>
                            <p class="b1m">A través de un enlace estatal que es el vínculo ente las normales y el SIBEN, y con la colaboración de un capturista y supervisor por escuela, se fue logrando que las escuelas normales fueran poco a poco ingresando sus datos, al principio se les solicitó del ciclo escolar y se recibieron todas las sugerencias para mejorar el sistema, actualmente se obtiene tres veces más información que en la primera versión.</p>
                            <p class="b1m">Los tiempos de recuperación de información se redujeron de nueve meses en promedio a un mes y la cantidad de información se ha incrementado tres veces del 2007 al 2009, y cada año se incorporan más datos de acuerdo con las necesidades de los usuarios.</p>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="acordeon-item">
                        <button class="acordeon-header" type="button">
                            <h4 class="sh1b">¿Para qué sirve?</h4>
                            <span class="acordeon-icono"></span>
                        </button>
                        <div class="acordeon-body">
                            <p class="b1m">Para apoyar a la toma de decisiones así como actividades cotidianas de la DGESUM:</p>
                            <ul class="listaBasica">
                                <li><p class="b1m">La planeación estratégica y operativa.</li>
                                <li><p class="b1m">Planeación y evaluación de documentos de las escuelas normales en el marco del EDINEN.</li>
                                <li><p class="b1m">Actualización del Directorio de las Instituciones Formadoras de Docentes.</li>
                                <li><p class="b1m">Información de base para el programa de becas y regulación de la educación normal.</li>
                                <li><p class="b1m">Estudios y aplicación de encuestas en estas instituciones.</li>
                                <li><p class="b1m">Conocimiento de las escuelas normales y su personal.</li>
                                <li><p class="b1m">Próximamente localizar geográficamente estas instituciones.</li>
                                <li><p class="b1m">Próximamente obtener datos de posgrados en las Instituciones Formadoras de Docentes.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="acordeon-item">
                        <button class="acordeon-header" type="button">
                            <h4 class="sh1b">Objetivo</h4>
                            <span class="acordeon-icono"></span>
                        </button>
                        <div class="acordeon-body">
                            <p class="b1m"><b>Objetivo:</b> Contar con datos específicos, únicos y oficiales sobre la educación normal en México para la puesta en marcha de diferentes programas.</p>
                            <p class="b1m"><b>Meta:</b> Tener la participación del 100% de las escuelas en cada periodo de captura y generar así datos más certeros y precisos.</p>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="acordeon-item">
                        <button class="acordeon-header" type="button">
                            <h4 class="sh1b">¿Cómo opera?</h4>
                            <span class="acordeon-icono"></span>
                        </button>
                        <div class="acordeon-body">
                            <p class="b1m">Todas las instituciones formadoras de docentes y la DGESUM intercambian directamente información para mantenerla actualizada con la validación de las Autoridades Educativas Estatales.</p>
                            <ul class="listaBasica">
                                <li><p class="b1m">La autoridad estatal nombra un enlace estatal y se le otorga una cuenta de usuario y contraseña.</li>
                                <li><p class="b1m">El director de cada escuela normal nombra un supervisor el cuál revisará los datos ingresados por el capturista.</li>
                                <li><p class="b1m">El director de cada escuela normal nombra un capturista el cuál ingresará o actualizará los datos cada ciclo escolar.</li>
                                <li><p class="b1m">La DGESUM a través de la Subdirección de Evaluación de Instituciones y Servicios mantendrá contacto con los enlaces estatales y dará a conocer el periodo de captura.</li>
                                <li><p class="b1m">Al final del primer periodo de captura del ciclo escolar, se enviarán firmados los informes que la DGESUM solicite.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Item 6 -->
                    <div class="acordeon-item">
                        <button class="acordeon-header" type="button">
                            <h4 class="sh1b">Alcances</h4>
                            <span class="acordeon-icono"></span>
                        </button>
                        <div class="acordeon-body">
                            <ul class="listaBasica">
                                <li><p class="b1m"><b>1er periodo:</b> Diciembre 2006 - Mayo del 2007. 353 escuelas participantes; 97,133 alumnos captados.</li>
                                <li><p class="b1m"><b>2º periodo:</b> Enero 2008. 448 escuelas participantes; 125,276 alumnos captados; 12,550 docentes captados.</li>
                                <li><p class="b1m"><b>3er periodo:</b> Mayo 2008. 460 escuelas participantes; 131,985 alumnos captados; 14,765 docentes captados.</li>
                                <li><p class="b1m"><b>4º periodo:</b> Diciembre 2008. 462 escuelas participantes; 133,286 alumnos captados; 35,124 egresados; 24,489 personal captado.</li>
                                <li><p class="b1m"><b>5º periodo:</b> Febrero y Mayo de 2009. 461 planteles participantes; 129,966 alumnos captados; 24,257 personal captado.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Item 7 -->
                    <div class="acordeon-item">
                        <button class="acordeon-header" type="button">
                            <h4 class="sh1b">Contacto</h4>
                            <span class="acordeon-icono"></span>
                        </button>
                        <div class="acordeon-body">
                            <p class="b1m">En caso de requerir soporte o mayor información:</p>
                            <p class="b1m">
                                Ing. Rafael Chávez Mondragón: <b>rafaelcm@nube.sep.gob.mx</b><br>
                                Ing. Lydia Raquel Vera Reza: <b>lrvera@nube.sep.gob.mx</b><br>
                                Ing. María Guadalupe Virto Blanco: <b>guadalupe.virto@nube.sep.gob.mx</b>
                            </p>
                            <p class="b1m">Conmutador (55) 3600-2511 ext. 53640.</p>
                        </div>
                    </div>

                    <!-- Item 8 -->
                    <div class="acordeon-item">
                        <button class="acordeon-header" type="button">
                            <h4 class="sh1b">Manuales y Presentaciones</h4>
                            <span class="acordeon-icono"></span>
                        </button>
                        <div class="acordeon-body">
                            <ul class="listaBasica">
                                <li>
                                    <a href="https://365sep-my.sharepoint.com/:v:/g/personal/lrvera_nube_sep_gob_mx/EchaY6kW-fxChDlh6lFCPM0Bb0u74i6dxodSYNU-w9FCxg?e=Pfoh1Q&nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJTdHJlYW1XZWJBcHAiLCJyZWZlcnJhbFZpZXciOiJTaGFyZURpYWxvZy1MaW5rIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXcifX0%3D" target="_blank">
                                        Enlace al taller de capacitación del 20/10/25
                                    </a>
                                </li>
                                <li><p class="b1m">Presentación 2025 <a href="https://siben.sep.gob.mx/siben/files/SIBEN_2025.pptx"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="Archivo"></a></li>
                                <li><p class="b1m">Presentación 2023 <a href="https://siben.sep.gob.mx/siben/files/SIBEN_2023.pdf"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="PDF"></a></li>
                                <li><p class="b1m">Presentación 2022 <a href="https://siben.sep.gob.mx/siben/files/SIBEN_2022.pdf"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="PDF"></a></li>
                                <li><p class="b1m">Manual del SIBEN 7 Licenciatura Enlaces Estatales <a href="https://siben.sep.gob.mx/siben/files/Manual_enlace_SIBEN7.pdf"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="PDF"></a></li>
                                <li><p class="b1m">Manual del SIBEN 7 Licenciatura Supervisores <a href="https://siben.sep.gob.mx/siben/files/Manual_supervisor_SIBEN7.pdf"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="PDF"></a></li>
                                <li><p class="b1m">Manual del SIBEN 7 Licenciatura Capturistas <a href="https://siben.sep.gob.mx/siben/files/Manual_Capturista_SIBEN_7.pdf"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="PDF"></a></li>
                                <li><p class="b1m">Manual del SIBEN Posgrados <a href="https://siben.sep.gob.mx/siben/files/Manual_SIBEN_Posgrados-2026.pdf"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="PDF"></a></li>
                                <li><p class="b1m">Manual del SIBEN Geográfico <a href="https://siben.sep.gob.mx/siben/files/Manual_SIBEN_Geografico.pdf"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="PDF"></a></li>
                                <li><p class="b1m">Manual Encuesta Académicos <a href="https://siben.sep.gob.mx/siben/files/Manual_encuesta_academicos.pdf"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="PDF"></a></li>
                                <li><p class="b1m">Manual Encuesta Alumnos <a href="https://siben.sep.gob.mx/siben/files/Manual_encuesta_alumnos.pdf"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="PDF"></a></li>
                                <li><p class="b1m">Presentación SIBEN 8 <a href="https://siben.sep.gob.mx/siben/files/SIBEN_2016.pptx"><img src="https://siben.sep.gob.mx/siben/img/pdf.jpg" class="icon-pdf" alt="Archivo"></a></li>
                            </ul>
                        </div>
                    </div>
                    <br>

                </div>
            </div>
        </div>
    </div>

    <!-- Script para comportamiento de un solo panel abierto -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const acordeonItems = document.querySelectorAll('.acordeon-item');

            acordeonItems.forEach(item => {
                const header = item.querySelector('.acordeon-header');

                header.addEventListener('click', () => {
                    const estaActivo = item.classList.contains('activo');

                    // Cerrar todos los paneles abiertos
                    acordeonItems.forEach(i => i.classList.remove('activo'));

                    // Abrir únicamente el panel seleccionado si no estaba activo
                    if (!estaActivo) {
                        item.classList.add('activo');
                    }
                });
            });
        });
    </script>

    <x-footer />
</body>
</html>