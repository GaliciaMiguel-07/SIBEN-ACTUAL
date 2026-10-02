<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al SIBEN</title>
    <link href="{{ url('/images/favicon.ico') }}" rel="shortcut icon">
    <!-- Estilos del framework gob.mx -->
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
</head>
<body>

    <!-- Barra gris superior -->
    <div class="barra-degradado"></div>

    <!-- Área principal del formulario -->
    <div class="container" style="margin-top: 50px; margin-bottom: 80px; max-width: 600px;">

        <h2 class="text-center">SIBEN</h2>

        <!-- Errores de validación (usuario/contraseña incorrectos) -->
        @if ($errors->any())
            <div class="alert alert-danger text-center">
                @foreach ($errors->all() as $error)
                    <p style="margin:0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!--
            Campos:
            - name="login"    → el controller lee $request->login  (que busca en usuarios.id_usuario)
            - name="password" → el controller lee $request->password
        -->
        <form id="login" class="form-horizontal" role="form" method="POST"
              action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="col-sm-3 control-label" for="UsuarioLogin">Usuario:</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text"
                           name="login" id="UsuarioLogin"
                           value="{{ old('login') }}"
                           placeholder="Usuario" autofocus required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="UsuarioPassword">Contraseña:</label>
                <div class="col-sm-9">
                    <input class="form-control" type="password"
                           name="password" id="UsuarioPassword"
                           placeholder="Contraseña" required>
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
        <div class="footer-content"
             style="display:flex; justify-content:flex-end; align-items:center;
                    gap:15px; padding:20px 40px; font-size:20px;">
            <img src="{{ url('/images/logo_siben_pequeno.png') }}"
                 alt="Logo SIBEN" class="footer-logo" style="height:25px;">
            <span>SIBEN</span>
            <span>V8</span>
        </div>
    </div>

</body>
</html>
