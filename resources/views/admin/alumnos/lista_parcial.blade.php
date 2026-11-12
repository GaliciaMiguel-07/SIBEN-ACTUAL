@if($alumnos->isEmpty())
    <div class="buscar01" style="padding: 24px; text-align: center; color: var(--neutro600);">
        No se encontraron alumnos con esos criterios de búsqueda.
    </div>
@else
    <div style="margin-bottom: 12px; color: var(--neutro700); font-size: 0.9rem;">
        {{ $alumnos->count() }} {{ $alumnos->count() === 1 ? 'alumno encontrado' : 'alumnos encontrados' }}
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--neutro400);">
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">No.</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">NID</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Primer Apellido</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Segundo Apellido</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Nombre(s)</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">CURP</th>
                    @if($porIdentificador ?? false)
                        <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Escuela</th>
                        <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600; text-align: center;">Periodo</th>
                    @endif
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600;">Licenciatura</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600; text-align: center;">Sem.</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600; text-align: center;">Grupo</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600; text-align: center;">Estatus</th>
                    <th style="padding: 10px 12px; color: var(--neutro800); font-weight: 600; text-align: center;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumnos as $i => $a)
                    <tr style="border-bottom: 1px solid var(--neutro300);">
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $i + 1 }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $a->nid }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $a->apellido_paterno }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $a->apellido_materno }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $a->nombre }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $a->curp }}</td>
                        @if($porIdentificador ?? false)
                            <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">{{ $a->escuela }}</td>
                            <td style="padding: 10px 12px; color: var(--neutro800); text-align: center; vertical-align: middle;">
                                {{ substr((string) $a->captura, 0, 4) }}-{{ substr((string) $a->captura, -1) }}
                            </td>
                        @endif
                        <td style="padding: 10px 12px; color: var(--neutro800); vertical-align: middle;">
                            {{ $licenciaturas[$a->id_licenciatura] ?? $a->id_licenciatura }}
                        </td>
                        <td style="padding: 10px 12px; color: var(--neutro800); text-align: center; vertical-align: middle;">{{ $a->semestre }}</td>
                        <td style="padding: 10px 12px; color: var(--neutro800); text-align: center; vertical-align: middle;">{{ $a->grupo }}</td>
                        <td style="padding: 10px 12px; text-align: center; vertical-align: middle;">
                            @switch($a->status)
                                @case('i')
                                    <span style="color: var(--exito, #28a745); font-weight: 600;">Inscrito</span>
                                    @break
                                @case('x')
                                    <span style="color: var(--error, #dc3545); font-weight: 600;">No Insc.</span>
                                    @break
                                @case('t')
                                    <span style="color: #17a2b8; font-weight: 600;">B. Temporal</span>
                                    @break
                                @case('d')
                                    <span style="color: #b8860b; font-weight: 600;">B. Definitiva</span>
                                    @break
                                @default
                                    <span style="color: var(--neutro600);">{{ $a->status }}</span>
                            @endswitch
                        </td>
                        <td style="padding: 10px 12px; text-align: center; vertical-align: middle;">
                            <a href="{{ route('admin.alumnos.editar', ['nid' => $a->nid, 'registro' => $a->id_registro]) }}"
                               class="boton__primario"
                               style="text-decoration: none; padding: 5px 14px; font-size: 0.8rem; display: inline-block;">
                                Editar
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
