<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://framework-gb.cdn.gob.mx/snd/v1.1.0/snd-guinda.css" rel="stylesheet">
    <title>Acceso al SIBEN</title>
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh; margin: 0; background-color: var(--neutro200);">

    <!-- Franja superior con los logos institucionales (SEP y DGESUM) -->
    <header style="background-color: var(--neutro100); border-bottom: 1px solid var(--neutro400); padding: 20px 0;">
        <div class="contenedor" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div>
                <img src="{{ url('/images/pleca_superior2020.jpg') }}" alt="Educación - Secretaría de Educación Pública | DGESUM - Dirección General de Educación Superior para el Magisterio" onerror="this.style.display='none'">
            </div>
        </div>
    </header>

    <div class="degradado--derecha" style="width: 100%;">
        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; padding: 20px 40px; font-size: 18px; color: #fff;">
        </div>
    </div>

    <!-- Contenedor principal centrado -->
    <div class="contenedor" style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 16px;">
        
        <div style="width: 100%; max-width: 650px;">
            <h2 class="h2r patria" style="text-align: center; margin-bottom: 24px;">SIBEN</h2>

            <!-- Alerta de error estilizada idéntica a la guía de diseño -->
            @if ($errors->any())
                <div style="background-color: #fdf2f2; border: 1px solid var(--principal); border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 14px; box-sizing: border-box;">
                    
                    <!-- Icono de advertencia guinda en SVG estandarizado -->
                    <div style="flex-shrink: 0; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; margin-top: 2px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--principal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>

                    <!-- Mensajes de error -->
                    <div style="color: var(--principal); font-size: 0.95rem; line-height: 1.5; font-weight: 500;">
                        @foreach ($errors->all() as $error)
                            <p style="margin: 0;">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tarjeta con el estilo oficial de contenedor (.buscar01) -->
            <div class="buscar01">
                <div class="buscar01__hgroup mb--24" style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">Ingrese sus credenciales de acceso</h3>
                </div>

                <form id="login" method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Retícula interna de 12 columnas para alinear los campos correctamente -->
                    <div class="reticulaGrid__12 gap--16 mb--24">
                        
                        <!-- Campo Usuario Obligatorio -->
                        <div class="columna__12--xs" style="margin-bottom: 16px;">
                            <label class="dblock mb--8 f--semibold" for="UsuarioLogin" style="color: var(--neutro800); display: block; margin-bottom: 8px;">
                                <span style="color: var(--principal); margin-right: 4px;">*</span>Usuario:
                            </label>
                            <input type="text"
                                   name="login" id="UsuarioLogin"
                                   value="{{ old('login') }}"
                                   placeholder="Ingrese su usuario" autofocus 
                                   style="width: 100%; height: 48px; padding: 0 16px; border: 1px solid var(--neutro400); border-radius: 8px; background: #fff; font-size: 1rem; color: var(--neutro800); box-sizing: border-box;">
                        </div>

                        <!-- Campo Contraseña Obligatorio -->
                        <div class="columna__12--xs" style="margin-bottom: 16px;">
                            <label class="dblock mb--8 f--semibold" for="UsuarioPassword" style="color: var(--neutro800); display: block; margin-bottom: 8px;">
                                <span style="color: var(--principal); margin-right: 4px;">*</span>Contraseña:
                            </label>
                            <input type="password"
                                   name="password" id="UsuarioPassword"
                                   placeholder="Ingrese su contraseña" 
                                   style="width: 100%; height: 48px; padding: 0 16px; border: 1px solid var(--neutro400); border-radius: 8px; background: #fff; font-size: 1rem; color: var(--neutro800); box-sizing: border-box;">
                        </div>

                        <!-- Checkbox Recordarme -->
                        <div class="columna__12--xs">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal; color: var(--neutro800);">
                                <input type="checkbox" name="remember" style="width: 18px; height: 18px;"> Recordarme
                            </label>
                        </div>
                    </div>

                    <!-- Botón de Iniciar Sesión con la clase oficial del SND -->
                    <div class="dflex justify-content-flex-end">
                        <button class="boton__primario" type="submit">Iniciar Sesión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Barra inferior institucional -->
    <div class="degradado--derecha" style="width: 100%;">
        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; padding: 20px 40px; font-size: 18px; color: #fff;">
            <img src="{{ url('/images/logo_siben_pequeno.png') }}"
                 alt="Logo SIBEN" style="height: 25px;" onerror="this.style.display='none'">
            <span>SIBEN</span>
            <span>2026</span>
        </div>
    </div>

    <!-- Script en silencio para prevenir el uso de inspeccionar y clic derecho -->
    <script>
        // Prevenir menú contextual de clic derecho en silencio
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        // Prevenir combinaciones de teclas frecuentes para abrir DevTools
        document.addEventListener('keydown', function (e) {
            // Tecla F12
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                return false;
            }

            // Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C
            if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) {
                e.preventDefault();
                return false;
            }

            // Ctrl+U (Ver código fuente)
            if (e.ctrlKey && (e.key === 'U' || e.key === 'u')) {
                e.preventDefault();
                return false;
            }

            // Atajos en macOS (Cmd+Opt+I)
            if (e.metaKey && e.altKey && (e.key === 'I' || e.key === 'i')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>