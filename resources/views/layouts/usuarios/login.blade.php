<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al SIBEN</title>
    <link href="{{ asset('favicon.ico') }}" rel="shortcut icon">
    <!-- Estilos del framework gob.mx -->
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
</head>
<body>

    <!-- Encabezado con logos -->

    <!-- Barra gris superior -->
    <div class="barra-degradado"></div>

    <!-- Área principal del formulario -->
    <div class="container" style="margin-top: 50px; margin-bottom: 80px; max-width: 600px;">
        
        <h2 class="text-center" >SIBEN</h2>

        <!-- Manejo de Errores -->
        @isset($mensaje)
            <div class="alert alert-danger text-center">
                {{ $mensaje }}
            </div>
        @endisset

        @if(session('error'))
            <div class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        <!-- Formulario con la estructura del código 2 y la lógica del código 1 -->
        <form id="login" name="login" class="form-horizontal" role="form" method="POST" action="{{ route('usuarios.authenticate') }}">
            @csrf
            
            <div class="form-group">
                <label class="col-sm-3 control-label" for="UsuarioIdUsuario">Usuario:</label>
                <div class="col-sm-9">
                    <!-- Se agregaron name, id, autofocus y required del código 1 -->
                    <input class="form-control" type="text" name="id_usuario" id="UsuarioIdUsuario" placeholder="Usuario" autofocus required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-3 control-label" for="UsuarioContrasenia">Contraseña:</label>
                <div class="col-sm-9">
                    <!-- Se agregaron name, id y required del código 1 -->
                    <input class="form-control" type="password" name="contrasenia" id="UsuarioContrasenia" placeholder="Contraseña" required>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember"> Recordarme
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button class="btn btn-primary pull-right" type="submit">Iniciar Sesión</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Barra gris inferior con versión -->
    <div class="barra-degradado footer-bar">
        <div class="footer-content" style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; padding: 20px 40px; font-size: 20px;">
            <img src="{{ asset('images/logo_siben_pequeno.png') }}" alt="Logo SIBEN" class="footer-logo" style="height: 25px;">
            <span>SIBEN</span>
            <span>V8</span>
        </div>
    </div>

</body>
</html>