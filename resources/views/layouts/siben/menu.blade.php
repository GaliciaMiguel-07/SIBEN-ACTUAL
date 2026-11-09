{{-- Fragmento del menú SIBEN. El layout principal ya declara html/head/body. --}}
@php
    $nombreUsuario = auth()->user()?->nombre ?? '';
    $opciones = $menu ?? [];
    $permiteDescarga = in_array((int) ($nivel ?? 0), [2, 3, 4], true);
@endphp

<section class="contenedor" style="max-width: 900px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">
    <header style="margin-bottom: 24px;">
        <h1 class="h2r patria" style="color: var(--neutro900); font-size: 1.75rem; margin: 0 0 4px;">
            Bienvenido,
        </h1>
        @if ($nombreUsuario !== '')
            <p style="color: var(--neutro600); font-size: 1.1rem; margin: 0; font-weight: 500;">
                {{ $nombreUsuario }}
            </p>
        @endif
    </header>

    @if (!empty($mensajeCaptura))
        <div role="status" class="alert alert-warning" style="margin-bottom: 18px;">
            {{ $mensajeCaptura }}
        </div>
    @endif

    <div class="buscar01" style="margin-bottom: 24px; overflow-x: auto;">
        @if (count($opciones))
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--neutro400);">
                        <th scope="col" style="padding: 12px 16px;">Opción</th>
                        <th scope="col" style="padding: 12px 16px; text-align: center; width: 160px;">Acceso</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($opciones as $opcion)
                        <tr style="border-bottom: 1px solid var(--neutro300);">
                            <td style="padding: 14px 16px;">{{ $opcion['nombre'] }}</td>
                            <td style="padding: 14px 16px; text-align: center;">
                                @if (!empty($opcion['disponible']) && !empty($opcion['ruta']))
                                    <a href="{{ $opcion['ruta'] }}" class="boton__primario"
                                       style="padding: 6px 16px; font-size: .9rem; text-decoration: none; display: inline-block;">
                                        Acceder
                                    </a>
                                @else
                                    <span class="text-muted" aria-disabled="true"
                                          title="La ruta de este módulo todavía no está migrada">
                                        En migración
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="padding: 20px; text-align: center; color: var(--neutro600);">
                No hay opciones disponibles para este módulo y nivel.
            </p>
        @endif
    </div>

    @if ($permiteDescarga && \Illuminate\Support\Facades\Route::has('siben.archivos'))
        <div style="margin-bottom: 24px;">
            <a href="{{ route('siben.archivos') }}" class="boton__secundario"
               style="text-decoration: none; padding: 10px 20px; display: inline-block;">
                Descargar archivos
            </a>
        </div>
    @endif

    @if (count($mensajes ?? []))
        <section aria-labelledby="mensajes-siben">
            <h2 id="mensajes-siben" class="h4">Mensajes</h2>
            @foreach ($mensajes as $mensaje)
                <article class="alert alert-info" style="margin-bottom: 10px;">
                    {{ is_array($mensaje) ? ($mensaje['mensaje'] ?? '') : ($mensaje->mensaje ?? '') }}
                </article>
            @endforeach
        </section>
    @endif

    <form method="POST" action="{{ route('logout') }}" style="margin-top: 24px;">
        @csrf

    </form>
</section>
