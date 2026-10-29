<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA — SIBEN</title>
    <link href="{{ url('/images/favicon.ico') }}" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
</head>
<body>

    <div class="barra-degradado"></div>

    <div class="container" style="margin-top: 50px; margin-bottom: 80px; max-width: 600px;">

        <h2 class="text-center">SIBEN</h2>

        <p class="text-center" style="margin-bottom: 25px;">
            Abre tu aplicación de autenticación e ingresa el código de 6 dígitos.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger text-center">
                @foreach ($errors->all() as $error)
                    <p style="margin:0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form class="form-horizontal" role="form" method="POST"
              action="{{ route('2fa.verificar.post') }}">
            @csrf

            <div class="form-group">
                <label class="col-sm-3 control-label" for="codigo">Código:</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text"
                           name="codigo" id="codigo"
                           placeholder="000000"
                           maxlength="6"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           autofocus required>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button class="btn btn-primary pull-right" type="submit">
                        Verificar
                    </button>
                    <a href="{{ route('login') }}" class="btn btn-default">
                        Cancelar
                    </a>
                </div>
            </div>

        </form>
    </div>

    <div class="barra-degradado footer-bar">
        <div class="footer-content"
             style="display:flex; justify-content:flex-end; align-items:center;
                    gap:15px; padding:20px 40px; font-size:20px;">
            <img src="{{ url('/images/logo_siben_pequeno.png') }}"
                 alt="Logo SIBEN" class="footer-logo" style="height:25px;">
            <span>SIBEN</span>
            <span>2026</span>
        </div>
    </div>

</body>
</html>