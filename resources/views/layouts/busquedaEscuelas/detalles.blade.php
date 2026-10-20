<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/escuelas.css') }}">
    <title>Detalles de Escuela — SIBEN</title>
    
    <x-header />
</head>
<body>

<div class="contenedor mt--24">
    <nav class="ruta__cont" aria-label="Ruta de navegación">
        <ol class="ruta__lista">
            <!-- Primer elemento con el ícono de la casa oficial -->
            <li class="ruta__li">
                <a class="ruta__a" href="{{ url('/') }}" aria-label="Inicio">
                    <i class="snd__icono snd-home icono--24" aria-hidden="true"></i>
                </a>
            </li>
            
            <!-- Segundo nivel (Navegación anterior) -->
            <li class="ruta__li">
                <a class="ruta__a" href="{{ route('siben.busqueda_escuelas.busqueda') }}">
                    Búsqueda
                </a>
            </li>

            <!-- Nivel actual (Sin enlace) -->
            <li class="ruta__li">
                <a class="ruta__a" href="{{ route('siben.busqueda_escuelas.resultado') }}">
                Resultados
                </a>
                <li class="ruta__li" aria-current="page">
                Detalles
            </li>
            </li>
        </ol>
    </nav>
</div>
    <div class="contenedor" style="padding-bottom: 60px;">       
        <section>
            <h2 class="h2r patria">DETALLES DE LA ESCUELA NORMAL SELECCIONADA</h2>
            <hr class="red divider">          

            @if(isset($escuela))
                
                <!-- ENCABEZADO BÁSICO (Usando el contenedor oficial .buscar01) -->
                <div class="buscar01" style="margin-bottom: 24px;">
                    <h3 class="sh1b color--principal" style="margin-top: 0;"><b>{{ $escuela->escuela }}</b></h3>
                    <p class="b1m" style="margin-bottom: 0; line-height: 1.6;">
                        <b>Entidad:</b> {{ is_object($entidad) ? ($entidad->entidad ?? $entidad->nombre) : (is_object($localidad) && isset($localidad->entidad) ? $localidad->entidad : $entidad) }}<br>
                        <b>Sostenimiento:</b> {{ $sostenimiento }}<br>
                        <b>Turno:</b> {{ $turno }}
                    </p>
                </div>

                <!-- DOMICILIO Y CONTACTO -->
                @if(!empty($domicilioData))
                    <div class="buscar01" style="margin-bottom: 24px;">
                        <h4 class="sh0b" style="margin-bottom: 16px; border-bottom: 1px solid var(--neutro400); padding-bottom: 8px;">Datos de Ubicación y Contacto</h4>
                        
                        <!-- Retícula responsiva para la información de domicilio -->
                        <div class="reticulaGrid__12 gap--16">
                            <div class="columna__6--md columna__12--xs">
                                <p class="b1m"><b>Vialidad:</b> {{ $domicilioData['vialidad'] }}</p>
                                <p class="b1m"><b>Núm Ext 1 / Núm Ext 2:</b> {{ $domicilioData['num_ext1'] }} / {{ $domicilioData['num_ext2'] }}</p>
                                <p class="b1m"><b>Núm Int:</b> {{ $domicilioData['num_int'] }}</p>
                                <p class="b1m"><b>Asentamiento Humano:</b> {{ $domicilioData['asentamiento'] }}</p>
                                <p class="b1m"><b>Localidad:</b> {{ is_object($localidad) ? ($localidad->localidad ?? $localidad->nombre ?? '') : $localidad }}</p>
                                <p class="b1m"><b>Municipio:</b> {{ is_object($municipio) ? ($municipio->municipio ?? $municipio->nombre ?? '') : (is_object($localidad) && isset($localidad->municipio) ? $localidad->municipio : $municipio) }}</p>
                                <p class="b1m"><b>Entidad:</b> {{ is_object($entidad) ? ($entidad->entidad ?? $entidad->nombre ?? '') : (is_object($localidad) && isset($localidad->entidad) ? $localidad->entidad : $entidad) }}</p>
                                <p class="b1m"><b>C.P.:</b> {{ $domicilioData['cp'] }}</p>
                            </div>
                            <div class="columna__6--md columna__12--xs">
                                <p class="b1m"><b>Referencias:</b> {{ $domicilioData['referencia1'] }} {{ $domicilioData['referencia2'] }} {{ $domicilioData['referencia3'] }}</p>
                                <p class="b1m"><b>Complementaria:</b> {{ $domicilioData['complementaria'] }}</p>
                                <p class="b1m"><b>Teléfonos:</b> {{ $domicilioData['telefono1'] }} / {{ $domicilioData['telefono2'] }} / {{ $domicilioData['telefono3'] }}</p>
                                <p class="b1m"><b>LADA / Fax:</b> {{ $domicilioData['lada'] }} / {{ $domicilioData['fax'] }}</p>
                                <p class="b1m"><b>Correo Electrónico:</b> {{ $escuela->correo_electronico ?? '-' }}</p>
                                <p class="b1m"><b>Web:</b> 
                                    @if(!empty($escuela->pagina_web))
                                        <a href="{{ str_starts_with($escuela->pagina_web, 'http') ? $escuela->pagina_web : 'http://'.$escuela->pagina_web }}" target="_blank">
                                            {{ $escuela->pagina_web }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- DIRECTIVOS -->
                @if(isset($personal) && count($personal) > 0)
                    <div class="buscar01" style="margin-bottom: 24px;">
                        <h4 class="sh0b" style="margin-bottom: 16px; border-bottom: 1px solid var(--neutro400); padding-bottom: 8px;">Directivos</h4>
                        <div class="deslizable--scroll">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tbody>
                                    @foreach($personal as $persona)
                                        <tr style="border-bottom: 1px solid var(--neutro400);">
                                            <td style="padding: 12px 8px;" class="b1m">{{ mb_strtoupper($persona->nombre ?? '') }}</td>
                                            <td style="padding: 12px 8px;" class="b1m">{{ mb_strtoupper($persona->apellido_paterno ?? '') }}</td>
                                            <td style="padding: 12px 8px;" class="b1m">{{ mb_strtoupper($persona->apellido_materno ?? '') }}</td>
                                            <td style="padding: 12px 8px;" class="b1m"><b>{{ $funciones[$persona->id_funcion] ?? 'Sin función especificada' }}</b></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- LICENCIATURAS -->
                @if(isset($licenciaturas) && count($licenciaturas) > 0)
                    <div class="buscar01" style="margin-bottom: 24px;">
                        <h4 class="sh0b" style="margin-bottom: 16px; border-bottom: 1px solid var(--neutro400); padding-bottom: 8px;">Licenciaturas</h4>
                        <div class="deslizable--scroll">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 2px solid var(--neutro400); text-align: left;">
                                        <th style="padding: 8px;" class="b1b">Licenciaturas</th>
                                        <th style="padding: 8px;" class="b1b">Especialidad</th>
                                        <th style="padding: 8px;" class="b1b">Modalidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licenciaturas as $lic)
                                        <tr style="border-bottom: 1px solid var(--neutro400);">
                                            <td style="padding: 12px 8px;" class="b1m">{{ $lic->licenciatura ?? '' }} {{ $lic->plan ?? '' }}</td>
                                            <td style="padding: 12px 8px;" class="b1m">{{ $lic->especialidad ?? 'n/a' }}</td>
                                            <td style="padding: 12px 8px;" class="b1m">
                                                {{ strtolower($lic->modalidad ?? '') === 'e' ? 'Escolarizada' : (strtolower($lic->modalidad ?? '') === 'm' ? 'Mixta' : ($lic->modalidad ?? 'Escolarizada')) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- IMÁGENES -->
                <div class="buscar01" style="margin-bottom: 24px;">
                    <h4 class="sh0b" style="margin-bottom: 16px; border-bottom: 1px solid var(--neutro400); padding-bottom: 8px;">Imágenes</h4>
                    @if(isset($imagenes) && count($imagenes) > 0)
                        <div class="reticulaGrid__12 gap--16">
                            @foreach($imagenes as $img)
                                <div class="columna__4--md columna__6--sm columna__12--xs">
                                    <div style="border: 1px solid var(--neutro400); border-radius: 8px; padding: 4px; background: #fff;">
                                        <img src="{{ $img }}" alt="Fotografía de la escuela" style="width: 100%; height: auto; border-radius: 4px; display: block;">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="b1m" style="margin: 0; color: var(--neutro600);">No hay imágenes disponibles para esta escuela.</p>
                    @endif
                </div>

            @else
                <div class="aviso aviso--error mb--24">
                    <div class="aviso__cont">
                        <p class="aviso__txt">No se encontró información para esta escuela.</p>
                    </div>
                </div>
            @endif

            <div class="mt--32">
                <a href="{{ route('siben.busqueda_escuelas.busqueda') }}" class="boton__secundario">Regresar a Búsqueda</a>
            </div>
        </section>
    </div>

    <x-footer />
</body>
</html>