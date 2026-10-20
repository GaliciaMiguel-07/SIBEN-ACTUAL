<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/escuelas.css') }}">
    <title>Más Información — SIBEN</title>
    
    <x-header />

    <!-- Corrección de punto de origen para que la flecha gire sobre su propio centro -->
    <style>
        .acordeon__gatillo::after,
        .acordeon__gatillo .snd__icono,
        .acordeon__gatillo i {
            transition: transform 0.2s ease-in-out !important;
            transform-origin: center center !important;
            display: inline-block !important;
        }

        details[open] .acordeon__gatillo::after,
        details[open] .acordeon__gatillo .snd__icono,
        details[open] .acordeon__gatillo i {
            transform: rotate(222deg) !important;
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
                    Más Información
                </li>
            </ol>
        </nav>
    </div>

    <div class="contenedor" style="padding-top: 20px; padding-bottom: 60px;">       
        <section>
            <!-- Título principal a todo lo ancho -->
            <h2 class="h2r patria">Más información</h2>
            <hr class="red divider mb--32">

            <!-- Acordeón con estructura oficial del SND -->
            <div class="acordeon__grupo">
                
                <!-- Item 1 -->
                <details class="acordeon__item" name="siben-acordeon">
                    <summary class="acordeon__gatillo">
                        <span class="acordeon__tit">¿Qué es el SIBEN?</span>
                    </summary>
                    <div class="acordeon__contenido">
                        <p class="b1m">Es un programa que se utiliza para facilitar la consulta y actualización de la información de las Escuelas Normales a nivel nacional. Concentra los principales datos de las escuelas, sus docentes y alumnos; además de fungir como un directorio en línea y ofrecer estadísticas de interés, disponibles a las escuelas normales y responsables de las normales. Próximamente permitirá obtener datos de las unidades y programas de posgrado que existen en las normales, también estará disponible una herramienta que permitirá ubicar localizar geográficamente las mismas escuelas.</p>
                    </div>
                </details>

                <!-- Item 2 -->
                <details class="acordeon__item" name="siben-acordeon">
                    <summary class="acordeon__gatillo">
                        <span class="acordeon__tit">Antecedentes</span>
                    </summary>
                    <div class="acordeon__contenido">
                        <p class="b1m">Hasta 2005 se recolectaba información de las escuelas normales mediante cuestionarios que se enviaban a las autoridades educativas locales en forma impresa y en disco magnético, éstas lo enviaban a las escuelas donde se llenaba el formato y se regresaba a la Dirección General, muchas veces estos formatos llegaban al finalizar el ciclo escolar lo que hacía poco oportuna la información, al llegar se capturaba en Access lo que implicaba una posible fuente de error por la misma captura, cuando se detectaba información inconsistente nadie en las autoridades educativas locales ni en las escuelas sabían quien se había hecho cargo del asunto, lo que impedía confirmar o corregir el dato, lo que nos lleva a entender que la información era poco confiable.</p>
                        <p class="b1m mt--16">Por lo anterior se decidió que fuera en línea lo que evita recapturas y permite tener bien identificadas a las personas responsables de la información, actualmente se otorga un mes para actualizar su información lo que hizo más breve el tiempo para tener los datos.</p>
                        <p class="b1m mt--16">El Sistema de Información Básica de la Educación Normal empezó su funcionamiento en el ciclo escolar 2006-2007, donde se recababa la información más importante de los estudiantes y docentes frente a grupo.</p>
                        <p class="b1m mt--16">A través de un enlace estatal que es el vínculo ente las normales y el SIBEN, y con la colaboración de un capturista y supervisor por escuela, se fue logrando que las escuelas normales fueran poco a poco ingresando sus datos, al principio se les solicitó del ciclo escolar y se recibieron todas las sugerencias para mejorar el sistema, actualmente se obtiene tres veces más información que en la primera versión.</p>
                        <p class="b1m mt--16">Los tiempos de recuperación de información se redujeron de nueve meses en promedio a un mes y la cantidad de información se ha incrementado tres veces del 2007 al 2009, y cada año se incorporan más datos de acuerdo con las necesidades de los usuarios.</p>
                    </div>
                </details>

                <!-- Item 3 -->
                <details class="acordeon__item" name="siben-acordeon">
                    <summary class="acordeon__gatillo">
                        <span class="acordeon__tit">¿Para qué sirve?</span>
                    </summary>
                    <div class="acordeon__contenido">
                        <p class="b1m">Para apoyar a la toma de decisiones así como actividades cotidianas de la DGESUM:</p>
                        <ul class="listaBasica mt--12">
                            <li><p class="b1m">La planeación estratégica y operativa.</p></li>
                            <li><p class="b1m">Planeación y evaluación de documentos de las escuelas normales en el marco del EDINEN.</p></li>
                            <li><p class="b1m">Actualización del Directorio de las Instituciones Formadoras de Docentes.</p></li>
                            <li><p class="b1m">Información de base para el programa de becas y regulación de la educación normal.</p></li>
                            <li><p class="b1m">Estudios y aplicación de encuestas en estas instituciones.</p></li>
                            <li><p class="b1m">Conocimiento de las escuelas normales y su personal.</p></li>
                            <li><p class="b1m">Próximamente localizar geográficamente estas instituciones.</p></li>
                            <li><p class="b1m">Próximamente obtener datos de posgrados en las Instituciones Formadoras de Docentes.</p></li>
                        </ul>
                    </div>
                </details>

                <!-- Item 4 -->
                <details class="acordeon__item" name="siben-acordeon">
                    <summary class="acordeon__gatillo">
                        <span class="acordeon__tit">Objetivo</span>
                    </summary>
                    <div class="acordeon__contenido">
                        <p class="b1m"><b>Objetivo:</b> Contar con datos específicos, únicos y oficiales sobre la educación normal en México para la puesta en marcha de diferentes programas.</p>
                        <p class="b1m mt--16"><b>Meta:</b> Tener la participación del 100% de las escuelas en cada periodo de captura y generar así datos más certeros y precisos.</p>
                    </div>
                </details>

                <!-- Item 5 -->
                <details class="acordeon__item" name="siben-acordeon">
                    <summary class="acordeon__gatillo">
                        <span class="acordeon__tit">¿Cómo opera?</span>
                    </summary>
                    <div class="acordeon__contenido">
                        <p class="b1m">Todas las instituciones formadoras de docentes y la DGESUM intercambian directamente información para mantenerla actualizada con la validación de las Autoridades Educativas Estatales.</p>
                        <ul class="listaBasica mt--12">
                            <li><p class="b1m">La autoridad estatal nombra un enlace estatal y se le otorga una cuenta de usuario y contraseña.</p></li>
                            <li><p class="b1m">El director de cada escuela normal nombra un supervisor el cuál revisará los datos ingresados por el capturista.</p></li>
                            <li><p class="b1m">El director de cada escuela normal nombra un capturista el cuál ingresará o actualizará los datos cada ciclo escolar.</p></li>
                            <li><p class="b1m">La DGESUM a través de la Subdirección de Evaluación de Instituciones y Servicios mantendrá contacto con los enlaces estatales y dará a conocer el periodo de captura.</p></li>
                            <li><p class="b1m">Al final del primer periodo de captura del ciclo escolar, se enviarán firmados los informes que la DGESUM solicite.</p></li>
                        </ul>
                    </div>
                </details>

                <!-- Item 6 -->
                <details class="acordeon__item" name="siben-acordeon">
                    <summary class="acordeon__gatillo">
                        <span class="acordeon__tit">Alcances</span>
                    </summary>
                    <div class="acordeon__contenido">
                        <ul class="listaBasica">
                            <li><p class="b1m"><b>1er periodo:</b> Diciembre 2006 - Mayo del 2007. 353 escuelas participantes; 97,133 alumnos captados.</p></li>
                            <li><p class="b1m"><b>2º periodo:</b> Enero 2008. 448 escuelas participantes; 125,276 alumnos captados; 12,550 docentes captados.</p></li>
                            <li><p class="b1m"><b>3er periodo:</b> Mayo 2008. 460 escuelas participantes; 131,985 alumnos captados; 14,765 docentes captados.</p></li>
                            <li><p class="b1m"><b>4º periodo:</b> Diciembre 2008. 462 escuelas participantes; 133,286 alumnos captados; 35,124 egresados; 24,489 personal captado.</p></li>
                            <li><p class="b1m"><b>5º periodo:</b> Febrero y Mayo de 2009. 461 planteles participantes; 129,966 alumnos captados; 24,257 personal captado.</p></li>
                        </ul>
                    </div>
                </details>

                <!-- Item 7 -->
                <details class="acordeon__item" name="siben-acordeon">
                    <summary class="acordeon__gatillo">
                        <span class="acordeon__tit">Contacto</span>
                    </summary>
                    <div class="acordeon__contenido">
                        <p class="b1m">En caso de requerir soporte o mayor información:</p>
                        <p class="b1m mt--12">
                            Ing. Rafael Chávez Mondragón: <b>rafaelcm@nube.sep.gob.mx</b><br>
                            Ing. Lydia Raquel Vera Reza: <b>lrvera@nube.sep.gob.mx</b><br>
                            Ing. María Guadalupe Virto Blanco: <b>guadalupe.virto@nube.sep.gob.mx</b>
                        </p>
                        <p class="b1m mt--12">Conmutador (55) 3600-2511 ext. 53640.</p>
                    </div>
                </details>

                <!-- Item 8 -->
                <details class="acordeon__item" name="siben-acordeon">
                    <summary class="acordeon__gatillo">
                        <span class="acordeon__tit">Manuales y Presentaciones</span>
                    </summary>
                    <div class="acordeon__contenido">
                        <ul class="listaBasica">
                            <li>
                                <a href="https://365sep-my.sharepoint.com/:v:/g/personal/lrvera_nube_sep_gob_mx/EchaY6kW-fxChDlh6lFCPM0Bb0u74i6dxodSYNU-w9FCxg?e=Pfoh1Q&nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJTdHJlYW1XZWJBcHAiLCJyZWZlcnJhbFZpZXciOiJTaGFyZURpYWxvZy1MaW5rIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXcifX0%3D" target="_blank">
                                    Enlace al taller de capacitación del 20/10/25
                                </a>
                            </li>
                            <li><p class="b1m">Presentación 2025 <a href="https://siben.sep.gob.mx/siben/files/SIBEN_2025.pptx">Descargar archivo</a></p></li>
                            <li><p class="b1m">Presentación 2023 <a href="https://siben.sep.gob.mx/siben/files/SIBEN_2023.pdf">Descargar PDF</a></p></li>
                            <li><p class="b1m">Presentación 2022 <a href="https://siben.sep.gob.mx/siben/files/SIBEN_2022.pdf">Descargar PDF</a></p></li>
                            <li><p class="b1m">Manual del SIBEN 7 Licenciatura Enlaces Estatales <a href="https://siben.sep.gob.mx/siben/files/Manual_enlace_SIBEN7.pdf">Descargar PDF</a></p></li>
                            <li><p class="b1m">Manual del SIBEN 7 Licenciatura Supervisores <a href="https://siben.sep.gob.mx/siben/files/Manual_supervisor_SIBEN7.pdf">Descargar PDF</a></p></li>
                            <li><p class="b1m">Manual del SIBEN 7 Licenciatura Capturistas <a href="https://siben.sep.gob.mx/siben/files/Manual_Capturista_SIBEN_7.pdf">Descargar PDF</a></p></li>
                            <li><p class="b1m">Manual del SIBEN Posgrados <a href="https://siben.sep.gob.mx/siben/files/Manual_SIBEN_Posgrados-2026.pdf">Descargar PDF</a></p></li>
                            <li><p class="b1m">Manual del SIBEN Geográfico <a href="https://siben.sep.gob.mx/siben/files/Manual_SIBEN_Geografico.pdf">Descargar PDF</a></p></li>
                            <li><p class="b1m">Manual Encuesta Académicos <a href="https://siben.sep.gob.mx/siben/files/Manual_encuesta_academicos.pdf">Descargar PDF</a></p></li>
                            <li><p class="b1m">Manual Encuesta Alumnos <a href="https://siben.sep.gob.mx/siben/files/Manual_encuesta_alumnos.pdf">Descargar PDF</a></p></li>
                            <li><p class="b1m">Presentación SIBEN 8 <a href="https://siben.sep.gob.mx/siben/files/SIBEN_2016.pptx">Descargar archivo</a></p></li>
                        </ul>
                    </div>
                </details>

            </div>
        </section>
    </div>

    <x-footer />
</body>
</html>