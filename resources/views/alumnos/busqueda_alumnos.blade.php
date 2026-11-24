@extends('layouts.siben.siben')

@section('titulo', 'Búsqueda de Alumnos — SIBEN')

@section('content')

<div class="contenedor" style="flex: 1; padding: 32px 16px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">

    <div style="margin-bottom: 20px;">
        <a href="{{ $anterior ?? route('alumnos.index') }}"
           class="boton__secundario"
           style="text-decoration: none; padding: 6px 14px; font-size: 0.9rem; display: inline-block;">
            &larr; Regresar
        </a>
    </div>

    <div style="padding: 24px; background: #fff; border: 1px solid var(--neutro400); border-radius: 4px;">

        <div style="border-bottom: 1px solid var(--neutro400); padding-bottom: 12px; margin-bottom: 20px;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--neutro800);">
                Búsqueda de Alumnos — {{ $tituloTabla[$opc] ?? '' }}
            </h3>
        </div>

        {{-- Panel de búsqueda --}}
        <div style="margin-bottom: 24px; border: 1px solid var(--neutro300); border-radius: 4px; overflow: hidden;">

            <div style="background: var(--principal, #811331); color: #fff; padding: 10px 16px; font-weight: 600;">
                Filtros de búsqueda
            </div>

            <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; background-color: #fff;">

                {{-- Licenciatura --}}
                @if(!empty($licenciaturas))
                <div>
                    <label for="BuscaIdLicenciatura" style="display:block; font-size:0.9rem; color:var(--neutro800); font-weight:600; margin-bottom:6px;">Licenciatura:</label>
                    <select id="BuscaIdLicenciatura" name="Busca[id_licenciatura]"
                            style="width:100%; padding:8px 12px; border:1px solid var(--neutro400); border-radius:4px; font-size:0.9rem;">
                        <option value="">Todas</option>
                        @foreach($licenciaturas as $valor => $texto)
                            <option value="{{ $valor }}">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Especialidad --}}
                @if(!empty($especialidades))
                <div>
                    <label for="BuscaIdEspecialidad" style="display:block; font-size:0.9rem; color:var(--neutro800); font-weight:600; margin-bottom:6px;">Especialidad:</label>
                    <select id="BuscaIdEspecialidad" name="Busca[id_especialidad]"
                            style="width:100%; padding:8px 12px; border:1px solid var(--neutro400); border-radius:4px; font-size:0.9rem;">
                        <option value="">Todas</option>
                        @foreach($especialidades as $valor => $texto)
                            <option value="{{ $valor }}">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Modalidad --}}
                @if(!empty($modalidades))
                <div>
                    <label for="BuscaModalidad" style="display:block; font-size:0.9rem; color:var(--neutro800); font-weight:600; margin-bottom:6px;">Modalidad:</label>
                    <select id="BuscaModalidad" name="Busca[modalidad]"
                            style="width:100%; padding:8px 12px; border:1px solid var(--neutro400); border-radius:4px; font-size:0.9rem;">
                        <option value="">Todas</option>
                        @foreach($modalidades as $valor => $texto)
                            <option value="{{ $valor }}">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Turno --}}
                @if(!empty($turnos))
                <div>
                    <label for="BuscaTurno" style="display:block; font-size:0.9rem; color:var(--neutro800); font-weight:600; margin-bottom:6px;">Turno:</label>
                    <select id="BuscaTurno" name="Busca[turno]"
                            style="width:100%; padding:8px 12px; border:1px solid var(--neutro400); border-radius:4px; font-size:0.9rem;">
                        <option value="">Todos</option>
                        @foreach($turnos as $valor => $texto)
                            <option value="{{ $valor }}">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Semestre --}}
                <div>
                    <label for="BuscaSemestre" style="display:block; font-size:0.9rem; color:var(--neutro800); font-weight:600; margin-bottom:6px;">Semestre:</label>
                    <select id="BuscaSemestre" name="Busca[semestre]"
                            style="width:100%; padding:8px 12px; border:1px solid var(--neutro400); border-radius:4px; font-size:0.9rem;">
                        <option value="">Todos</option>
                        @foreach($semestres as $valor => $texto)
                            <option value="{{ $valor }}">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Estatus --}}
                <div>
                    <label for="BuscaStatus" style="display:block; font-size:0.9rem; color:var(--neutro800); font-weight:600; margin-bottom:6px;">Estatus:</label>
                    <select id="BuscaStatus" name="Busca[status]"
                            style="width:100%; padding:8px 12px; border:1px solid var(--neutro400); border-radius:4px; font-size:0.9rem;">
                        <option value="">Todos</option>
                        @foreach($status as $valor => $texto)
                            <option value="{{ $valor }}">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- CURP --}}
                <div>
                    <label for="BuscaCurp" style="display:block; font-size:0.9rem; color:var(--neutro800); font-weight:600; margin-bottom:6px;">CURP:</label>
                    <input type="text" id="BuscaCurp" name="Busca[curp]" maxlength="18"
                           onblur="this.value = this.value.trim().toUpperCase();"
                           style="width:100%; padding:8px 12px; border:1px solid var(--neutro400); border-radius:4px; font-size:0.9rem; box-sizing:border-box;">
                </div>

                {{-- NID --}}
                <div>
                    <label for="BuscaNid" style="display:block; font-size:0.9rem; color:var(--neutro800); font-weight:600; margin-bottom:6px;">NID:</label>
                    <input type="text" id="BuscaNid" name="Busca[nid]" maxlength="10"
                           style="width:100%; padding:8px 12px; border:1px solid var(--neutro400); border-radius:4px; font-size:0.9rem; box-sizing:border-box;">
                </div>

                {{-- Grupo --}}
                <div>
                    <label for="BuscaGrupo" style="display:block; font-size:0.9rem; color:var(--neutro800); font-weight:600; margin-bottom:6px;">Grupo:</label>
                    <input type="text" id="BuscaGrupo" name="Busca[grupo]" maxlength="5"
                           onblur="this.value = this.value.trim().toUpperCase();"
                           style="width:100%; padding:8px 12px; border:1px solid var(--neutro400); border-radius:4px; font-size:0.9rem; box-sizing:border-box;">
                </div>

            </div>

            <div style="padding: 0 16px 16px; background-color: #fff;">
                <button type="button" id="btnBuscarAlumnos" class="boton__primario"
                        style="padding: 8px 24px; font-size: 0.9rem; cursor: pointer;">
                    Buscar
                </button>
            </div>

        </div>

        {{-- Área de resultados --}}
        <div id="contenedorLista">
            <p style="color: var(--neutro600); text-align: center; padding: 20px;">
                Complete los filtros deseados y presione <strong>Buscar</strong>.
            </p>
        </div>

        {{-- Overlay para formulario de edición (Prototype lo necesita) --}}
        <div id="overoverlay" style="display:none; position:fixed; left:0; top:0; width:100%; height:100%; z-index:98; background:rgba(0,0,0,0.5);">
            <div style="position:relative; margin:40px auto; max-width:900px; background:#fff; border-radius:4px; overflow:hidden;">
                <div style="padding:8px 12px; background:var(--principal,#811331); color:#fff; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-weight:600;">Editar Alumno</span>
                    <span style="cursor:pointer; font-size:1.2rem;" onclick="document.getElementById('overoverlay').style.display='none';">&times;</span>
                </div>
                <div id="overlay" style="padding:16px; max-height:80vh; overflow:auto;"></div>
            </div>
        </div>

    </div>
</div>

{{-- URL del endpoint de búsqueda (para el JS) --}}
<script>
    window._urlBusqueda = @json($url);
</script>

@endsection

@push('scripts')
<script>
(function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]')
                    ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    : '';

    var contenedor = document.getElementById('contenedorLista');
    var btn        = document.getElementById('btnBuscarAlumnos');

    if (!btn || !contenedor) return;

    btn.addEventListener('click', function () {
        var params = new URLSearchParams();

        var ids = {
            'Busca[id_licenciatura]': 'BuscaIdLicenciatura',
            'Busca[id_especialidad]': 'BuscaIdEspecialidad',
            'Busca[modalidad]':       'BuscaModalidad',
            'Busca[turno]':           'BuscaTurno',
            'Busca[semestre]':        'BuscaSemestre',
            'Busca[status]':          'BuscaStatus',
            'Busca[curp]':            'BuscaCurp',
            'Busca[nid]':             'BuscaNid',
            'Busca[grupo]':           'BuscaGrupo'
        };

        Object.keys(ids).forEach(function (campo) {
            var el = document.getElementById(ids[campo]);
            if (el && el.value.trim() !== '') {
                params.append(campo, el.value.trim());
            }
        });

        contenedor.innerHTML = '<p style="text-align:center; padding:20px;">Buscando alumnos...</p>';

        fetch(window._urlBusqueda, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'text/html, application/xhtml+xml'
            },
            body: params
        })
        .then(function (r) {
            if (!r.ok) {
                return r.text().then(function (t) {
                    throw new Error('Error ' + r.status + ': ' + t.substring(0, 200));
                });
            }
            return r.text();
        })
        .then(function (html) {
            contenedor.innerHTML = html;
        })
        .catch(function (err) {
            contenedor.innerHTML =
                '<p style="color:#721c24;background:#f8d7da;padding:12px 16px;border:1px solid #f5c6cb;border-radius:4px;">'
                + 'Error al buscar: ' + err.message + '</p>';
        });
    });

    /* ------------------------------------------------------------------ */
    /* Funciones globales que los botones de la tabla llaman por onclick    */
    /* ------------------------------------------------------------------ */

    // Abre el formulario de edición en el overlay
    window.consultaAjax = function (url, parametros, cbOverlay, cbOcupado) {
        var overlay  = document.getElementById('overlay');
        var overover = document.getElementById('overoverlay');
        if (!overlay || !overover) return;

        if (typeof cbOcupado === 'function') cbOcupado();

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'text/html, application/xhtml+xml',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: parametros
        })
        .then(function (r) { return r.text(); })
        .then(function (html) {
            overlay.innerHTML = html;
            overover.style.display = 'block';
        })
        .catch(function (err) {
            alert('Error al cargar formulario: ' + err.message);
        });
    };

    window.confirmacion = function (url, parametros) {
        if (confirm('¿Está seguro de inscribir a este alumno?')) {
            window.consultaAjax(url, parametros, null, null);
        }
    };

    window.muestraOverlay = function () {
        var el = document.getElementById('overoverlay');
        if (el) el.style.display = 'block';
    };

    window.ocultaOverlay = function () {
        var el = document.getElementById('overoverlay');
        if (el) el.style.display = 'none';
    };

    window.muestraOcupado = function () {};
    window.ocultaOcupado  = function () {};

    // Cierra overlay con ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.ocultaOverlay();
    });
})();
</script>
@endpush
