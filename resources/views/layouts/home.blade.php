<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIBEN — Sistema de Información Básica de Escuelas Normales</title>
</head>
<body>
<x-header />
    {{-- Banner Institucional --}}
    <div class="flag-bannerx"
         style="background-image: url('{{ url('/images/banner_2024.jpeg') }}');
                background-size: cover;
                background-position: center;
                width: 100%;
                min-height: 320px;
                margin-bottom: 30px;">
    </div>

    {{-- Título SIBEN (Centrado y extendido, color dorado) --}}
    <div class="reticulaGrid__12 ">
        <div class="columna__12 ">
            <div class="cajaFont--0 p--8 --center">
                <h2 class="h2r patria color--dorado500" style="text-align: center;">
                    Sistema de Información Básica de la Educación Normal (SIBEN)
                </h2>
            </div>
        </div>
    </div>



    {{-- Sección de Avisos (Alineado a la izquierda, texto continuo sin columnas) --}}
    <div class="reticulaGrid__12 ">
        <div class="columna__12 columna__6--md">
            <div class="cajaFont--0 p--8 --center">
                <h1 class="h1b">
                Avisos:
                </h1>
            </div>
        </div>
            
            
                <div class="columna__12 columna__7--md">
                    <div class="cajaFont--0 p--8 --center">
            <h1 class="h2m">
                Estimados usuarios:
            </h1>
                </div>
            </div>
            
                <div class="columna__12 columna__12--md">
                    <div class="cajaFont--0 p--8 --center">
            <p class="b1m">
                Les informamos que la captura de SIBEN LICENCIATURAS será del 09 al 27 de marzo de 2026. <br><br>
                Para comunicarse, llamar al conmutador (0155 3600 2511) y marcar la extensión 53640. <br><br>
            </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>