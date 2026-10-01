<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Acceso al SIBEN</title>

    <link href="{{ url('/images/favicon.ico') }}" rel="shortcut icon">

    <!-- Framework gob.mx -->
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
</head>

<body>

    <!-- Pleca superior de SIBEN -->
    @include('layouts.siben.pleca_superior')


    <!-- Barra gris superior -->
    <div class="barra-degradado"></div>


    <!-- Área principal del formulario -->
    <div class="container"
         style="margin-top: 50px; margin-bottom: 80px; max-width: 600px;">

        <h2 class="text-center">SIBEN</h2>

        @if ($errors->any())
            <div class="alert alert-danger text-center">
                @foreach ($errors->all() as $error)
                    <p style="margin:0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form id="login"
              class="form-horizontal"
              role="form"
              method="POST"
              action="{{ route('login') }}">

            @csrf

            <div class="form-group">

                <label class="col-sm-3 control-label"
                       for="UsuarioLogin">
                    Usuario:
                </label>

                <div class="col-sm-9">

                    <input class="form-control"
                           type="text"
                           name="login"
                           id="UsuarioLogin"
                           value="{{ old('login') }}"
                           placeholder="Usuario"
                           autofocus
                           required>

                </div>

            </div>


            <div class="form-group">

                <label class="col-sm-3 control-label"
                       for="UsuarioPassword">
                    Contraseña:
                </label>

                <div class="col-sm-9">

                    <input class="form-control"
                           type="password"
                           name="password"
                           id="UsuarioPassword"
                           placeholder="Contraseña"
                           required>

                </div>

            </div>


            <div class="form-group">

                <div class="col-sm-offset-3 col-sm-9">

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember">
                            Recordarme
                        </label>
                    </div>

                </div>

            </div>


            <div class="form-group">

                <div class="col-sm-offset-3 col-sm-9">

                    <button class="btn btn-primary pull-right"
                            type="submit">
                        Iniciar Sesión
                    </button>

                </div>

            </div>

        </form>

    </div>


    <!-- Footer de SIBEN -->
    @include('layouts.siben.foot')

</body>
</html>