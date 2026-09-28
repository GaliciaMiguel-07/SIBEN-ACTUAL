<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEN - Iniciar sesión</title>
    <link rel="stylesheet" href="https://framework-gb.cdn.gob.mx/v5.8.1/css/framework-gob.min.css">
</head>
<body>

@include('components.header')

<main>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">

                <div class="card shadow-sm">
                    <div class="card-header text-center bg-white">
                        <img src="{{ asset('img/logo_siben.png') }}" alt="SIBEN" style="max-height:60px;">
                        <h5 class="mt-2">Sistema de Información Básica de Escuelas Normales</h5>
                    </div>

                    <div class="card-body">

                        {{-- Mensajes de error --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="login" class="form-label">Usuario</label>
                                <input
                                    type="text"
                                    id="login"
                                    name="login"
                                    class="form-control @error('login') is-invalid @enderror"
                                    value="{{ old('login') }}"
                                    required
                                    autofocus
                                >
                                @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer text-center text-muted small">
                        SIBEN V8 &mdash; SEP México
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

@include('components.footer')

</body>
</html>
