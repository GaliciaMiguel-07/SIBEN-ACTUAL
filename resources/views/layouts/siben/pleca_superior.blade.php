@php
    $usuario = auth()->user();
    $nivel   = $usuario->nivel ?? 1;
    $nombre  = $usuario->nombre ?? '';

    // Mapeo oficial de los roles según el nivel del usuario
    $rolesPorNivel = [
        1 => 'Administrador',
        2 => 'Supervisor Estatal',
        3 => 'Supervisor de Escuela',
        4 => 'Capturista',
        5 => 'Capturista'
    ];
    $nombreRol = $rolesPorNivel[$nivel] ?? 'Usuario';

    // Consulta directa a la base de datos para la entidad
    $entidadObj = \Illuminate\Support\Facades\DB::table('cat_entidades')
                    ->where('id_renapo', $usuario->id_renapo ?? '')
                    ->first();

    $nombreEntidad = $entidadObj->entidad ?? $entidadObj->nombre ?? '';
    $nombreEscuela = $usuario->escuela->escuela ?? '';
@endphp

<div id="pleca_superior">

    <!-- Header oficial con logos de la SEP / DGESUM -->
    <header style="background-color: var(--neutro100); border-bottom: 1px solid var(--neutro400); padding: 15px 0;">
        <div class="contenedor" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="width: 100%;">
                <img src="{{ url('/images/pleca_superior2020.jpg') }}" 
                     alt="Educación - Secretaría de Educación Pública | DGESUM" 
                     style="width: 100%; height: auto;" 
                     onerror="this.style.display='none'">
            </div>
        </div>
    </header>

    <!-- Franja institucional guinda limpia exclusivamente para el Rol -->
    @if ($nivel > 0)
        <div class="degradado--derecha" style="color: #fff; width: 100%;">
            <div class="contenedor" style="display: flex; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto;">
                <h2 style="font-size: 1.25rem; font-weight: bold; margin: 0; color: #fff;">
                    {{ $nombreRol }}
                </h2>
            </div>
        </div>

        <!-- Bloque de datos secundarios (Escuela, Entidad, Usuario, Cerrar sesión) -->
        <div style="background-color: var(--neutro100); border-bottom: 1px solid var(--neutro300); padding: 8px 0;">
            <div class="contenedor" style="display: flex; justify-content: flex-end; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                <div id="info_usuario" style="font-size: 0.85rem; color: var(--neutro800); text-align: right; line-height: 1.4;">

                    @if ($nivel == 1)

                        <strong>{{ $nombre }}</strong>

                    @elseif ($nivel == 2)

                        <span style="color: var(--neutro600);">Entidad:</span> <strong>{{ $nombreEntidad }}</strong>
                        &nbsp;|&nbsp;
                        <strong>{{ $nombre }}</strong>

                    @elseif ($nivel >= 3 && $nivel <= 5)

                        <span style="color: var(--neutro600);">Escuela:</span> <strong>{{ $nombreEscuela }}</strong>
                        &nbsp;|&nbsp;
                        <span style="color: var(--neutro600);">Entidad:</span> <strong>{{ $nombreEntidad }}</strong>
                        &nbsp;|&nbsp;
                        <strong>{{ $nombre }}</strong>

                    @else

                        <strong>{{ $nombre }}</strong>

                    @endif

                    &nbsp;|&nbsp;

                    <!-- Botón para cerrar sesión -->
                    <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0; margin-left: 5px;">
                        @csrf
                        <button type="submit" class="boton__secundario" style="padding: 4px 12px; font-size: 0.8rem; cursor: pointer;">
                            Cerrar sesión
                        </button>
                    </form>

                </div>
            </div>
        </div>
    @endif

    <!-- Banner de impersonación -->
    @if (auth()->check() && auth()->user()->impersonating_user_id)
        <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 8px 15px; text-align: center; font-size: 13px; font-weight: bold; color: #856404;">
            ⚠️ Estás operando como <strong>{{ auth()->user()->id_usuario }}</strong>.
            &nbsp;
            <form method="POST" action="{{ route('admin.stop.impersonating') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: #856404; color: #fff; border: none; padding: 3px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">
                    ← Volver a mi cuenta
                </button>
            </form>
        </div>
    @endif

</div>