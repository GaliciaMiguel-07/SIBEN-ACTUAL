@extends('layouts.siben.siben')

@section('titulo', 'Editar infraestructura')

@section('content')
<div class="contenedor" style="max-width:1000px; margin:0 auto; padding:30px 16px; width:100%;">

    <h2 class="patria">Editar infraestructura de la escuela</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los siguientes datos:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('siben.guardaEstructura') }}">
        @csrf

        <h3>Características del inmueble</h3>

        <div style="margin-bottom:16px;">
            <label for="propiedad">Propiedad del inmueble</label>
            <select id="propiedad" name="CaracteristicaEscuela[propiedad]" class="form-control" required>
                @foreach ($tiposPropiedad as $id => $nombre)
                    <option value="{{ $id }}" @selected(old('CaracteristicaEscuela.propiedad', $estructura->propiedad ?? 11) == $id)>
                        {{ $nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom:24px;">
            <label for="estructura">Tipo de construcción</label>
            <select id="estructura" name="CaracteristicaEscuela[estructura]" class="form-control" required>
                @foreach ($tiposEstructura as $id => $nombre)
                    <option value="{{ $id }}" @selected(old('CaracteristicaEscuela.estructura', $estructura->estructura ?? 21) == $id)>
                        {{ $nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        @php
            $secciones = [
                'Espacios e instalaciones' => [
                    'aulas' => 'Aulas',
                    'bibliotecas' => 'Bibliotecas',
                    'aulas_computo' => 'Aulas de cómputo',
                    'auditorios' => 'Auditorios',
                    'idiomas' => 'Aulas de idiomas',
                    'albercas' => 'Albercas',
                    'gimnasios' => 'Gimnasios',
                    'canchas' => 'Canchas',
                    'pistas' => 'Pistas',
                    'salas_maestros' => 'Salas de maestros',
                    'cubiculos_maestros' => 'Cubículos de maestros',
                    'salas_multiples' => 'Salas múltiples',
                    'laboratorios' => 'Laboratorios',
                    'talleres' => 'Talleres',
                ],
                'Equipos de cómputo' => [
                    'computo_alumnos' => 'Para alumnos',
                    'computo_docentes' => 'Para docentes',
                    'computo_administrativo' => 'Para administrativos',
                ],
                'Equipos con acceso a Internet' => [
                    'internet_alumnos' => 'Para alumnos',
                    'internet_docentes' => 'Para docentes',
                    'internet_administrativo' => 'Para administrativos',
                ],
                'Accesibilidad y recursos' => [
                    'd_rampas' => 'Rampas',
                    'd_elevadores' => 'Elevadores',
                    'd_computadoras' => 'Computadoras adaptadas',
                    'd_banios' => 'Baños adaptados',
                    'd_libros' => 'Libros accesibles',
                ],
            ];

            $opcionesSiNo = [
                's' => 'Sí',
                'n' => 'No',
            ];
        @endphp

        @foreach ($secciones as $titulo => $campos)
            <section style="margin-bottom:25px;">
                <h3>{{ $titulo }}</h3>

                <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(230px,1fr)); gap:16px;">
                    @foreach ($campos as $campo => $nombre)
                        <div>
                            <label for="{{ $campo }}">{{ $nombre }}</label>
                            <input
                                type="number"
                                id="{{ $campo }}"
                                name="CaracteristicaEscuela[{{ $campo }}]"
                                class="form-control"
                                min="0"
                                max="{{ in_array($campo, [
                                    'computo_alumnos',
                                    'computo_docentes',
                                    'computo_administrativo',
                                    'internet_alumnos',
                                    'internet_docentes',
                                    'internet_administrativo'
                                ]) ? 100000 : 255 }}"
                                value="{{ old('CaracteristicaEscuela.'.$campo, $estructura->$campo ?? 0) }}"
                                required
                            >
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <section style="margin-bottom:25px;">
            <h3>Servicios de conectividad y recursos educativos</h3>

            @foreach ([
                'internet' => 'Cuenta con Internet',
                'internet_banda_ancha' => 'Tipo de conexión',
                'restriccion_internet' => 'Restricción de Internet',
                'siden' => 'SIDEN',
                'edusat' => 'EDUSAT',
                'd_software' => 'Software accesible',
                'd_guias' => 'Guías accesibles',
            ] as $campo => $nombre)

                <div style="margin-bottom:16px;">
                    <label for="{{ $campo }}">{{ $nombre }}</label>

                    <select
                        id="{{ $campo }}"
                        name="CaracteristicaEscuela[{{ $campo }}]"
                        class="form-control"
                        required
                    >
                        @foreach (($campo === 'internet_banda_ancha' ? $bandaAncha : $opcionesSiNo) as $valor => $etiqueta)
                            <option
                                value="{{ $valor }}"
                                @selected(old('CaracteristicaEscuela.'.$campo, $estructura->$campo ?? 'n') == $valor)
                            >
                                {{ $etiqueta }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </section>

        <div style="display:flex; flex-wrap:wrap; gap:12px; justify-content:center; margin-top:25px;">
            <button type="submit" class="boton__primario" style="padding:10px 20px;">
                Guardar infraestructura
            </button>

            <a href="{{ route('siben.estructuraEscuela') }}" class="boton__secundario" style="padding:10px 20px; text-decoration:none;">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection