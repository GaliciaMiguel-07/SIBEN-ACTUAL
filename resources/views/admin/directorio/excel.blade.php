<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head><meta charset="UTF-8"></head>
<body>
<table border="1">
    <tr><th colspan="6" style="font-size:14pt;">Directorio — {{ $entidad }}</th></tr>
    <tr style="background:#d9d9d9; font-weight:bold;">
        <th>Nombre</th><th>Cargo</th><th>Función</th><th>Correo</th><th>Dirección</th><th>Teléfono</th>
    </tr>
    @foreach($registros as $r)
        <tr>
            <td>{{ trim(implode(' ', array_filter([$r->titulo, $r->nombre, $r->apellido_paterno, $r->apellido_materno]))) }}</td>
            <td>{{ $r->cargo }}</td>
            <td>{{ $funciones[$r->id_funcion] ?? '' }}</td>
            <td>{{ $r->correo_electronico }}</td>
            <td>{{ $r->direccion }}</td>
            <td style="mso-number-format:'\@';">({{ $r->lada }}) {{ $r->telefono }}</td>
        </tr>
    @endforeach
</table>
</body>
</html>
