@if($personal->isEmpty())
    <p>No hay personal registrado en esta escuela.</p>
@else
<table class="tabla-lista">
    <thead>
        <tr>
            <th>No.</th>
            <th>Nombre</th>
            <th>Primer Apellido</th>
            <th>Segundo Apellido</th>
            <th>CURP</th>
            <th>Edad</th>
            <th>Género</th>
            <th>Función</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($personal as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->nombre }}</td>
                <td>{{ $p->apellido_paterno }}</td>
                <td>{{ $p->apellido_materno }}</td>
                <td>{{ $p->curp }}</td>
                <td>{{ $p->edad }}</td>
                <td>{{ strtoupper($p->genero) }}</td>
                <td>{{ $p->funcion->funcion ?? '' }}</td>
                <td>
                    <span class="estado-{{ $p->status === 'a' ? 'activo' : 'inactivo' }}">
                        {{ strtoupper($p->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.personal.editar', $p->id) }}" class="btn-accion">Editar</a>
                    <button type="button" class="btn-accion btn-alterno" onclick="cambiaStatus({{ $p->id }})">
                        {{ $p->status === 'a' ? 'Desactivar' : 'Activar' }}
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

<style>
    .tabla-lista { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #ddd; font-size: 14px; }
    .tabla-lista th { background: #611232; color: #fff; padding: 8px 10px; text-align: left; border: 1px solid #611232; }
    .tabla-lista td { padding: 8px 10px; border: 1px solid #ddd; }
    .tabla-lista tr:nth-child(even) { background: #f7f7f7; }
    .estado-activo { background: #28a745; color: #fff; padding: 3px 10px; border-radius: 3px; display: inline-block; font-weight: 700; }
    .estado-inactivo { background: #dc3545; color: #fff; padding: 3px 10px; border-radius: 3px; display: inline-block; font-weight: 700; }
    .btn-accion { display: inline-block; padding: 5px 10px; background: #611232; color: #fff; border: none; border-radius: 3px; font-size: 13px; text-decoration: none; cursor: pointer; margin-right: 4px; }
    .btn-accion:hover { background: #4d0e27; }
    .btn-alterno { background: #6c757d; }
    .btn-alterno:hover { background: #5a6268; }
</style>