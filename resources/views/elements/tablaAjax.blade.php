@php $fila = 1; @endphp

@if(empty($renglones))
    <p style="color: var(--neutro600); text-align: center; padding: 20px;">
        No se encontraron alumnos con los filtros indicados.
    </p>
@else
    <div style="border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">

        <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
            Resultados: {{ count($renglones) }} alumno(s) encontrado(s)
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        @foreach($cabeceras as $cabecera)
                            <th style="padding: 8px 12px; text-align: left; border-bottom: 2px solid var(--neutro400); color: var(--neutro800); font-weight: 600; white-space: nowrap;">
                                {{ $cabecera }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($renglones as $id => $renglon)
                        <tr id="row{{ $id }}" class="{{ $fila }}"
                            style="border-bottom: 1px solid var(--neutro300); {{ ($fila % 2 === 0) ? 'background:#fafafa;' : 'background:#fff;' }}">
                            <td style="padding: 8px 12px; color: var(--neutro700);">{{ $fila++ }}</td>
                            @foreach($renglon as $columna)
                                <td style="padding: 8px 12px; color: var(--neutro900);">{!! $columna !!}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endif
