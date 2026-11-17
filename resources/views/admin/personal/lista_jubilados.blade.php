
@if($personal->isEmpty())
    <div class="alert alert-info">
        No hay personal jubilado registrado en esta escuela.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nombre</th>
                    <th>Primer apellido</th>
                    <th>Segundo apellido</th>
                    <th>CURP</th>
                    <th>Fecha de jubilación</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($personal as $i => $persona)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $persona->nombre }}</td>
                        <td>{{ $persona->apellido_paterno }}</td>
                        <td>{{ $persona->apellido_materno }}</td>
                        <td>{{ $persona->curp }}</td>
                        <td>
                            {{ $persona->fecha_jubilacion
                                ? \Carbon\Carbon::parse($persona->fecha_jubilacion)->format('d/m/Y')
                                : 'Sin fecha' }}
                        </td>
                        <td>
                            <button type="button"
                                class="btn btn-success btn-sm btn-reactivar-jubilado"
                                data-id="{{ $persona->id_personal }}">
                                Reactivar
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif