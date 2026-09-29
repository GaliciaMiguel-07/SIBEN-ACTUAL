<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar 2FA — SIBEN</title>
    <link href="{{ url('/images/favicon.ico') }}" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
</head>
<body>

    <div class="barra-degradado"></div>

    <div class="container" style="margin-top: 50px; margin-bottom: 80px; max-width: 600px;">

        <h2 class="text-center">Autenticación de dos pasos</h2>

        @if ($errors->any())
            <div class="alert alert-danger text-center">
                @foreach ($errors->all() as $error)
                    <p style="margin:0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('ok'))
            <div class="alert alert-success text-center">
                {{ session('ok') }}
            </div>
        @endif

        <ol style="margin-bottom: 20px;">
            <li>Instala <strong>Google Authenticator</strong>, <strong>Authy</strong> o <strong>Microsoft Authenticator</strong> en tu teléfono.</li>
            <li>Abre la app y escanea el código QR que aparece abajo.</li>
            <li>Ingresa el código de 6 dígitos que muestra la app para confirmar.</li>
        </ol>

        {{-- QR generado como SVG en el servidor, sin servicios externos --}}
        <div class="text-center" style="margin: 25px 0;">
            {!! $qrSvg !!}
        </div>

        <p class="text-center" style="margin-bottom: 25px;">
            Si no puedes escanear el QR, escribe esta clave manualmente en la app:<br>
            <strong style="font-family: monospace; font-size: 20px; letter-spacing: 3px;">
                {{ $secret }}
            </strong>
        </p>

        <form class="form-horizontal" role="form" method="POST"
              action="{{ route('2fa.configurar.post') }}">
            @csrf

            {{-- El secreto viaja oculto para que enable() lo reciba sin volver a calcularlo --}}
            <input type="hidden" name="secret" value="{{ $secret }}">

            <div class="form-group">
                <label class="col-sm-4 control-label" for="codigo">
                    Código de confirmación:
                </label>
                <div class="col-sm-8">
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
                <div class="col-sm-offset-4 col-sm-8">
                    <button class="btn btn-primary pull-right" type="submit">
                        Activar 2FA
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-default">
                        Cancelar
                    </a>
                </div>
            </div>

        </form>

        {{-- Si ya tiene 2FA activo, mostrar opción de desactivarlo --}}
        @if (auth()->user()->google2fa_secret)
            <hr>
            <div class="text-center">
                <p>¿Quieres desactivar la autenticación de dos pasos?</p>
                <form method="POST" action="{{ route('2fa.desactivar') }}">
                    @csrf
                    <button class="btn btn-danger" type="submit"
                            onclick="return confirm('¿Seguro que quieres desactivar el 2FA? Tu cuenta quedará con menos protección.')">
                        Desactivar 2FA
                    </button>
                </form>
            </div>
        @endif

    </div>

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