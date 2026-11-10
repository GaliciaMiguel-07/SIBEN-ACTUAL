<option value="">Seleccione</option>

@if (!empty($opciones))
    @foreach ($opciones as $valor => $texto)
        <option value="{{ $valor }}">{{ $texto }}</option>
    @endforeach
@endif