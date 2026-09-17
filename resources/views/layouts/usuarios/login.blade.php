<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al SIBEN</title>
    
    <style>
        /* Reseteo básico */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #ffffff;
        }

        
  

        /* Barras grises con degradado (arriba y abajo) */
        .barra-degradado {
            height: 45px;
            /* Degradado de gris oscuro a gris claro y viceversa */
            background: linear-gradient(to bottom, #888888 0%, #e0e0e0 50%, #888888 100%);
            width: 100%;
        }

        /* Estilos del área de login */
        .login-container {
            text-align: center;
            margin-top: 50px;
            margin-bottom: 80px;
        }

        .login-title {
            color: #008040; /* Verde institucional */
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        /* Estilos de la tabla del formulario para alinear etiquetas e inputs */
        .login-table {
            margin: 0 auto;
            border-collapse: collapse;
        }

        .login-table td {
            padding: 5px 10px;
        }

        .login-label {
            color: #008040; /* Verde institucional */
            text-align: right;
            font-size: 14px;
        }

        .login-input {
            text-align: left;
        }

        .login-input input {
            border: 1px solid #7a7a7a;
            padding: 3px;
            width: 200px;
        }

        .btn-submit {
            margin-top: 20px;
            background-color: #d4d4d4;
            border: 1px solid #333;
            padding: 4px 15px;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-submit:hover {
            background-color: #c0c0c0;
        }

        /* Barra inferior del footer */
        .footer-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-right: 40px;
        }

        .footer-content {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 20px;
            font-weight: normal;
        }
        
        .footer-logo {
            height: 25px; /* Ajusta según el tamaño de tu logo pequeño */
        }
        
        .error-msg {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Encabezado con logos -->
    <div>
        <!-- Reemplaza los nombres de archivo por los que subiste -->
        <div class="flag-bannerx" style="background-image: url(https://siben.sep.gob.mx/siben/img/pleca_superior2020.jpg);  background-size: cover;
   padding: 140px;  "></div>
    </div>

    <!-- Barra gris superior -->
    <div class="barra-degradado"></div>

    <!-- Área principal del formulario -->
    <div class="login-container">
        
        @isset($mensaje)
            <div class='error-msg'>{{ $mensaje }}</div>
        @endisset

        <div class="login-title">SIBEN</div>

        <form id="login" name="login" method="POST" action="{{ route('usuarios.authenticate') }}">
            @csrf
            
            <table class="login-table">
                <tr>
                    <td class="login-label">Usuario:</td>
                    <td class="login-input">
                        <input type="text" name="id_usuario" id="UsuarioIdUsuario" autofocus required>
                    </td>
                </tr>
                <tr>
                    <td class="login-label">Contraseña:</td>
                    <td class="login-input">
                        <input type="password" name="contrasenia" id="UsuarioContrasenia" required>
                    </td>
                </tr>
            </table>

            @if(session('error'))
            <div class="error-msg">{{session('error')}}</div>
            @endif
            
            <button type="submit" class="btn-submit">Iniciar Sesión</button>
        </form>
    </div>

    <!-- Barra gris inferior con versión -->
    <div class="barra-degradado footer-bar">
        <div class="footer-content">
            <!-- Reemplaza por el nombre de tu logo pequeño de SIBEN -->
            <img src="{{ asset('images/logo_siben_pequeno.png') }}" alt="Logo SIBEN" class="footer-logo">
            <span>SIBEN</span>
            <span>V8</span>
        </div>
    </div>

</body>
</html>