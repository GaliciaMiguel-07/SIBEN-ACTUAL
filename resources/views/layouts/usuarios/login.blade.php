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
                <img src="{{ url('/images/pleca_superior2020.jpg') }}" alt="Educación - Secretaría de Educación Pública | DGESUM - Dirección General de Educación Superior para el Magisterio"  onerror="this.style.display='none'">
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

            <!-- Errores de validación usando la alerta oficial SND -->
            @if ($errors->any())
                <div class="aviso aviso--error mb--24" style="margin-bottom: 24px;">
                    <div class="aviso__cont">
                        <div class="aviso__txt">
                            @foreach ($errors->all() as $error)
                                <p style="margin: 0;">{{ $error }}</p>
                            @endforeach
                        </div>
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
                        
                        <!-- Campo Usuario -->
                        <div class="columna__12--xs">
                            <label class="dblock mb--8 f--semibold" for="UsuarioLogin" style="color: var(--neutro800);">Usuario:</label>
                            <input class="form__control"
                                type="text"
                                   name="login" id="UsuarioLogin"
                                   value="{{ old('login') }}"
                                   placeholder="Ingrese su usuario" autofocus required
                                   style="width: 100%; height: 48px; padding: 0 16px; border: 1px solid var(--neutro400); border-radius: 8px; background: #fff; font-size: 1rem; color: var(--neutro800);">
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="columna__12--xs">
                            <label class="dblock mb--8 f--semibold" for="UsuarioPassword" style="color: var(--neutro800);">Contraseña:</label>
                            <input type="password"
                                   name="password" id="UsuarioPassword"
                                   placeholder="Ingrese su contraseña" required
                                   style="width: 100%; height: 48px; padding: 0 16px; border: 1px solid var(--neutro400); border-radius: 8px; background: #fff; font-size: 1rem; color: var(--neutro800);">
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
            <span>V8</span>
        </div>
    </div>

</body>
</html>