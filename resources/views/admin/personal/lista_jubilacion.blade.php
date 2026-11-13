
@if($personal->isEmpty())

    <div style="padding: 20px; text-align: center; color: #666;">
        No se encontró personal para esta escuela.
    </div>

@else

    <div style="overflow-x: auto;">
        <table class="table table-bordered table-striped" style="width: 100%; font-size: 13px;">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nombre</th>
                    <th>Primer Apellido</th>
                    <th>Segundo Apellido</th>
                    <th>CURP</th>
                    <th>Edad</th>
                    <th>Género</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>
                @foreach($personal as $index => $persona)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>{{ $persona->nombre }}</td>

                        <td>{{ $persona->apellido_paterno }}</td>

                        <td>{{ $persona->apellido_materno }}</td>

                        <td>{{ $persona->curp }}</td>

                        <td>{{ $persona->edad }}</td>

                        <td>
                            @switch(strtolower($persona->genero ?? ''))
                                @case('h')
                                    Hombre
                                    @break
                                @case('m')
                                    Mujer
                                    @break
                                @default
                                    {{ strtoupper($persona->genero ?? '') }}
                            @endswitch
                        </td>

                        <td style="text-align: center;">
                            {{ strtoupper($persona->status) }}
                        </td>

                        <td style="text-align: center;">
                           <button
                                type="button"
                                class="btn btn-sm"
                                style="background-color: #611232; border-color: #611232; color: white;"
                                onclick="abrirModalJubilacion('{{ $persona->id }}', '{{ addslashes($persona->nombre) }} {{ addslashes($persona->apellido_paterno) }} {{ addslashes($persona->apellido_materno) }}')">
                                Jubilar
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="color: #666; font-size: 0.9rem;">
            Total de registros: <strong>{{ $personal->count() }}</strong>
        </p>
    </div>

@endif
