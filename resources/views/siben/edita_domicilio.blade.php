
@extends('layouts.siben.siben')

@section('titulo', 'Editar domicilio de la escuela')

@section('content')
<div class="contenedor" style="max-width: 1000px; margin: 0 auto; padding: 24px 16px; width: 100%; box-sizing: border-box;">

    <div class="buscar01" style="padding: 24px;">

        <h2 class="patria" style="color: var(--neutro900); font-size: 1.7rem; margin-bottom: 22px;">
            Editar domicilio de la escuela
        </h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Revisa los siguientes datos:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('siben.guardaDomicilio') }}">
            @csrf

            @php
                $dom = $domicilioEscuela;
                $valor = fn($campo, $default = '') => old(
                    "DomicilioEscuela.$campo",
                    $dom->$campo ?? $default
                );
            @endphp

            <h3 class="h5 mb-3">1. Ubicación</h3>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Entidad federativa</label>
                    <input type="text" class="form-control"
                           value="{{ $nombre_entidad }}" readonly>
                </div>

                <div class="col-md-4">
                    <label for="cve_mun" class="form-label">Municipio</label>
                    <select name="DomicilioEscuela[cve_mun]" id="cve_mun"
                            class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach ($municipios as $clave => $nombre)
                            <option value="{{ $clave }}"
                                @selected((string) $valor('cve_mun') === (string) $clave)>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="cve_loc" class="form-label">Localidad</label>
                    <select name="DomicilioEscuela[cve_loc]" id="cve_loc"
                            class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach ($locs as $clave => $nombre)
                            <option value="{{ $clave }}"
                                @selected((string) $valor('cve_loc') === (string) $clave)>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h3 class="h5 mb-3 mt-4">2. Vialidad</h3>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="tipovial" class="form-label">Tipo de vialidad</label>
                    <select name="DomicilioEscuela[tipovial]" id="tipovial"
                            class="form-select">
                        <option value="">Seleccione</option>
                        @foreach ($vialidad as $clave => $nombre)
                            <option value="{{ $clave }}"
                                @selected($valor('tipovial') === $clave)>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8">
                    <label for="nomvial" class="form-label">Nombre de vialidad</label>
                    <input type="text" name="DomicilioEscuela[nomvial]"
                           id="nomvial" class="form-control" maxlength="255"
                           value="{{ $valor('nomvial') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="carretera" class="form-label">Carretera</label>
                    <input type="text" name="DomicilioEscuela[carretera]"
                           id="carretera" class="form-control" maxlength="255"
                           value="{{ $valor('carretera') }}">
                </div>

                <div class="col-md-6">
                    <label for="camino" class="form-label">Camino</label>
                    <input type="text" name="DomicilioEscuela[camino]"
                           id="camino" class="form-control" maxlength="255"
                           value="{{ $valor('camino') }}">
                </div>
            </div>

            <h3 class="h5 mb-3 mt-4">3. Número exterior e interior</h3>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label" for="numextnum1">Número exterior</label>
                    <input type="text" name="DomicilioEscuela[numextnum1]"
                           id="numextnum1" class="form-control" maxlength="255"
                           value="{{ $valor('numextnum1') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="numextnum2">Número exterior adicional</label>
                    <input type="text" name="DomicilioEscuela[numextnum2]"
                           id="numextnum2" class="form-control" maxlength="255"
                           value="{{ $valor('numextnum2') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="numextalf1">Complemento exterior</label>
                    <input type="text" name="DomicilioEscuela[numextalf1]"
                           id="numextalf1" class="form-control" maxlength="35"
                           value="{{ $valor('numextalf1') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" for="numintnum">Número interior</label>
                    <input type="text" name="DomicilioEscuela[numintnum]"
                           id="numintnum" class="form-control" maxlength="5"
                           value="{{ $valor('numintnum') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="numintalf">Complemento interior</label>
                    <input type="text" name="DomicilioEscuela[numintalf]"
                           id="numintalf" class="form-control" maxlength="35"
                           value="{{ $valor('numintalf') }}">
                </div>
            </div>

            <h3 class="h5 mb-3 mt-4">4. Asentamiento</h3>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tipoasen" class="form-label">Tipo de asentamiento</label>
                    <select name="DomicilioEscuela[tipoasen]" id="tipoasen"
                            class="form-select">
                        <option value="">Seleccione</option>
                        @foreach ($asentamientos as $clave => $nombre)
                            <option value="{{ $clave }}"
                                @selected($valor('tipoasen') === $clave)>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="nomasen" class="form-label">Nombre del asentamiento</label>
                    <input type="text" name="DomicilioEscuela[nomasen]"
                           id="nomasen" class="form-control" maxlength="255"
                           value="{{ $valor('nomasen') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="cp" class="form-label">Código postal</label>
                    <input type="text" name="DomicilioEscuela[cp]"
                           id="cp" class="form-control" maxlength="5"
                           value="{{ $valor('cp') }}">
                </div>
            </div>

            <h3 class="h5 mb-3 mt-4">5. Referencias de ubicación</h3>

            @foreach ([1, 2, 3] as $numero)
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label" for="tiporef{{ $numero }}">
                            Tipo de referencia {{ $numero }}
                        </label>
                        <input type="text"
                               name="DomicilioEscuela[tiporef{{ $numero }}]"
                               id="tiporef{{ $numero }}" class="form-control"
                               maxlength="255"
                               value="{{ $valor('tiporef' . $numero) }}">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label" for="nomref{{ $numero }}">
                            Descripción de referencia {{ $numero }}
                        </label>
                        <input type="text"
                               name="DomicilioEscuela[nomref{{ $numero }}]"
                               id="nomref{{ $numero }}" class="form-control"
                               maxlength="255"
                               value="{{ $valor('nomref' . $numero) }}">
                    </div>
                </div>
            @endforeach

            <div class="mb-3">
                <label for="descrubic" class="form-label">Descripción de ubicación</label>
                <textarea name="DomicilioEscuela[descrubic]" id="descrubic"
                          class="form-control" rows="3"
                          maxlength="255">{{ $valor('descrubic') }}</textarea>
            </div>

            <h3 class="h5 mb-3 mt-4">6. Datos complementarios</h3>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="id_seguridad_social" class="form-label">
                        Seguridad social
                    </label>
                    <select name="DomicilioEscuela[id_seguridad_social]"
                            id="id_seguridad_social" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach ($seguridad_social as $clave => $nombre)
                            <option value="{{ $clave }}"
                                @selected((string) $valor('id_seguridad_social') === (string) $clave)>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="latitud" class="form-label">Latitud</label>
                    <input type="number" step="any"
                           name="DomicilioEscuela[latitud]" id="latitud"
                           class="form-control" required
                           value="{{ $valor('latitud', '0') }}">
                </div>

                <div class="col-md-3">
                    <label for="longitud" class="form-label">Longitud</label>
                    <input type="number" step="any"
                           name="DomicilioEscuela[longitud]" id="longitud"
                           class="form-control" required
                           value="{{ $valor('longitud', '0') }}">
                </div>
            </div>

            <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                <button type="submit" class="boton__primario"
                        style="padding: 9px 22px;">
                    Guardar domicilio
                </button>

                <a href="{{ route('siben.domicilioEscuela') }}"
                   class="boton__secundario"
                   style="display: inline-block; padding: 9px 22px; text-decoration: none;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const municipio = document.getElementById('cve_mun');
    const localidad = document.getElementById('cve_loc');

    if (!municipio || !localidad) {
        return;
    }

    municipio.addEventListener('change', async function () {
        localidad.innerHTML = '<option value="">Cargando...</option>';

        if (!municipio.value) {
            localidad.innerHTML = '<option value="">Seleccione</option>';
            return;
        }

        try {
            const respuesta = await fetch(@json(route('siben.loc')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'text/html',
                    'X-CSRF-TOKEN': @json(csrf_token())
                },
                body: JSON.stringify({
                    cve_mun: municipio.value
                })
            });

            if (!respuesta.ok) {
                throw new Error('No fue posible cargar las localidades.');
            }

            localidad.innerHTML = await respuesta.text();
        } catch (error) {
            localidad.innerHTML = '<option value="">Error al cargar localidades</option>';
            console.error(error);
        }
    });
});
</script>
@endpush
