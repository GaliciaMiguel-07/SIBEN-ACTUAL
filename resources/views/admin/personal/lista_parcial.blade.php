@if($personal->isEmpty())
    <div class="buscar01" style="padding: 24px; text-align: center; color: var(--neutro600);">
        No hay personal registrado en esta escuela.
    </div>
@else
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--neutro400);">
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">No.</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Nombre</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Primer Apellido</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Segundo Apellido</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">CURP</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Edad</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Género</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Función</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600; text-align: center;">Estado</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600; text-align: center;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($personal as $i => $p)
                    <tr style="border-bottom: 1px solid var(--neutro300);">
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $i + 1 }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $p->nombre }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $p->apellido_paterno }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $p->apellido_materno }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $p->curp }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $p->edad }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ strtoupper($p->genero) }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $p->funcion->funcion ?? '' }}</td>
                        <td style="padding: 10px 12px; text-align: center; vertical-align: middle;">
                            @if ($p->status === 'a')
                                <span style="color: var(--exito, #28a745); font-weight: 600;">ACTIVO</span>
                            @else
                                <span style="color: var(--error, #dc3545); font-weight: 600;">INACTIVO</span>
                            @endif
                        </td>
                        <td style="padding: 10px 12px; text-align: center; vertical-align: middle;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center; flex-wrap: wrap;">
                                <a href="{{ route('admin.personal.editar', $p->id) }}" class="boton__secundario" style="width: 95px; text-align: center; box-sizing: border-box; text-decoration: none; padding: 5px 10px; font-size: 0.8rem; display: inline-block;">
                                    Editar
                                </a>
                                <button type="button" class="boton__primario" onclick="cambiaStatus({{ $p->id }})" style="width: 95px; text-align: center; box-sizing: border-box; padding: 5px 10px; font-size: 0.8rem; cursor: pointer;">
                                    {{ $p->status === 'a' ? 'Desactivar' : 'Activar' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif