<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

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
        <h1 class="patria" style="color: #BC955C; font-size: 1.8rem; font-weight: bold; margin: 0; text-align: center;">
            Sistema de Información Básica de la Educación Normal (SIBEN)
        </h1>


    {{-- Sección de Avisos (Alineado a la izquierda, texto continuo sin columnas) --}}
    <div class="mexico__contenedor" style="max-width: 1200px; margin: 0 auto; padding: 0 20px 60px 20px;">
        <div style="text-align: left; font-size: 1rem; color: #333; line-height: 1.6;">
            <p style="margin-bottom: 10px;">
                <strong>Avisos:</strong>
            </p>
            
            <p>
                <strong>Estimados usuarios:</strong><br>
                Les informamos que la captura de SIBEN LICENCIATURAS será del 09 al 27 de marzo de 2026.
            </p>
            
            <p>
                Para comunicarse, llamar al conmutador (0155 3600 2511) y marcar la extensión 53640.
            </p>
        </div>
    </div>

    

<x-footer />

</body>
</html>