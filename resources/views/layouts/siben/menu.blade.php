@php
    $usuario = session('Usuario', []);
    $nivel = (int) ($usuario['nivel'] ?? (auth()->check() ? auth()->user()->nivel : 0));
    $nombreUsuario = $usuario['nombre'] ?? (auth()->check() ? auth()->user()->nombre : '');

    // Mapeo de opciones del sistema según el nivel
    $opcionesPorNivel = [
        1 => [
            ['nombre' => 'Edición', 'url' => \Illuminate\Support\Facades\Route::has('admin.index') ? route('admin.index') : '#'],
            ['nombre' => 'Mensajes', 'url' => '#'],
            ['nombre' => 'Directorio', 'url' => '#'],
            ['nombre' => 'Estadísticas', 'url' => '#'],
            ['nombre' => 'Informe nacional', 'url' => '#'],
            ['nombre' => 'Cambiar captura', 'url' => '#'],
            ['nombre' => 'Lista usuarios', 'url' => '#'],
            ['nombre' => 'Informes escuelas', 'url' => '#'],
            ['nombre' => 'Consulta histórico', 'url' => '#'],
            ['nombre' => 'Consulta docente', 'url' => '#'],
            ['nombre' => 'Prueba Verhoeff', 'url' => '#'],
        ],
        2 => [
            ['nombre' => 'Conflictos', 'url' => '#'],
            ['nombre' => 'Directorios', 'url' => '#'],
            ['nombre' => 'Avance alumnos', 'url' => '#'],
            ['nombre' => 'Avance personal', 'url' => '#'],
            ['nombre' => 'Supervisores', 'url' => '#'],
            ['nombre' => 'Directores', 'url' => '#'],
            ['nombre' => 'Alumnos', 'url' => '#'],
            ['nombre' => 'Consulta personal', 'url' => '#'],
            ['nombre' => 'Listados', 'url' => '#'],
            ['nombre' => 'Estadísticas', 'url' => '#'],
        ],
        3 => [
            ['nombre' => 'Configurar parámetros', 'url' => '#'],
            ['nombre' => 'Datos generales', 'url' => '#'],
            ['nombre' => 'Alumnos', 'url' => '#'],
            ['nombre' => 'Listados', 'url' => '#'],
            ['nombre' => 'Estadísticas', 'url' => '#'],
        ],
        4 => [
            ['nombre' => 'Datos generales', 'url' => '#'],
            ['nombre' => 'Alumnos', 'url' => '#'],
            ['nombre' => 'Personal', 'url' => '#'],
            ['nombre' => 'Listados', 'url' => '#'],
            ['nombre' => 'Estadísticas', 'url' => '#'],
        ],
    ];

    $opciones = $opcionesPorNivel[$nivel] ?? [];
    $permiteDescarga = in_array($nivel, [2, 3, 4]);
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd-guinda.css" rel="stylesheet">
    <title>Opciones del Sistema - SIBEN</title>
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh; margin: 0; background-color: var(--neutro200);">

    <!-- Pleca superior oficial -->
    @if(view()->exists('pleca_superior'))
        @include('pleca_superior')
    @endif

    <!-- Contenido Principal: Opciones del Sistema -->
    <div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 900px; margin: 0 auto; width: 100%; box-sizing: border-box;">

        <!-- Saludo de bienvenida con tipografía y retícula oficial del gobierno -->
        <div style="margin-bottom: 24px;">
            <h1 class="h2r patria" style="color: var(--neutro900); font-size: 1.75rem; margin: 0 0 4px 0;">
                Bienvenido,
            </h1>
            @if(!empty($nombreUsuario))
                <p style="color: var(--neutro600); font-size: 1.1rem; margin: 0; font-weight: 500;">
                    {{ $nombreUsuario }}
                </p>
            @endif
        </div>

        <!-- Tarjeta contenedora con el estilo oficial (.buscar01) -->
        <div class="buscar01" style="margin-bottom: 24px;">
            
            @if(count($opciones) > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--neutro400);">
                                <th style="padding: 12px 16px; color: var(--neutro800); font-weight: 600;">Opción</th>
                                <th style="padding: 12px 16px; color: var(--neutro800); font-weight: 600; text-align: center; width: 130px;">Acceso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($opciones as $opcion)
                                <tr style="border-bottom: 1px solid var(--neutro300);">
                                    <td style="padding: 14px 16px; color: var(--neutro800); vertical-align: middle;">
                                        {{ $opcion['nombre'] }}
                                    </td>
                                    <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                        <a href="{{ $opcion['url'] }}" class="boton__primario" style="padding: 6px 16px; font-size: 0.9rem; text-decoration: none; display: inline-block;">
                                            Acceder
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 20px; text-align: center; color: var(--neutro600);">
                    No hay opciones disponibles para este nivel de usuario.
                </div>
            @endif
        </div>

        <!-- Acciones adicionales (Descargar archivos) -->
        @if($permiteDescarga)
            <div style="display: flex; justify-content: flex-start; margin-bottom: 30px;">
                <a href="#" class="boton__secundario" style="text-decoration: none; padding: 10px 20px; display: inline-block;">
                    Descargar archivos
                </a>
            </div>
        @endif

    </div>

    <!-- Pie de página institucional mediante partial foot -->
    @if(view()->exists('foot'))
        @include('foot')
    @elseif(view()->exists('footer'))
        @include('footer')
    @endif

    <!-- Script en silencio -->
    <script>
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                return false;
            }
            if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) {
                e.preventDefault();
                return false;
            }
            if (e.ctrlKey && (e.key === 'U' || e.key === 'u')) {
                e.preventDefault();
                return false;
            }
            if (e.metaKey && e.altKey && (e.key === 'I' || e.key === 'i')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>