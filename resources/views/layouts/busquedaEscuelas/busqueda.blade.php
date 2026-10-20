<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Búsqueda de Escuelas - SIBEN</title>
    
    <x-header />

    <!-- Estilos de ajuste basados en las variables oficiales de estilos.css -->
    <style>
        .form__control {
            width: 100%;
            height: 48px;
            padding: 8px 16px;
            font-size: 1rem;
            color: var(--neutro800);
            background-color: var(--neutro100);
            border: 1px solid var(--neutro400);
            border-radius: 8px;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.15s ease-in-out;
        }

        .form__control:focus {
            border-color: var(--principal);
            box-shadow: 0 0 0 2px rgba(97, 18, 50, 0.2);
        }

        .form__label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--neutro800);
            margin-bottom: 6px;
            display: block;
        }

        .checkbox-snd {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--neutro800);
            user-select: none;
        }

        .checkbox-snd input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--principal);
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Migas de pan oficiales SND -->
<div class="contenedor mt--24">
    <nav class="ruta__cont" aria-label="Ruta de navegación">
        <ol class="ruta__lista">
            <li class="ruta__li">
                <a class="ruta__a" href="{{ url('/') }}" aria-label="Inicio">
                    <i class="snd__icono snd-home icono--24" aria-hidden="true"></i>
                </a>
            </li>
            <li class="ruta__li" aria-current="page">
                Búsqueda
            </li>
        </ol>
    </nav>
</div>

<div class="contenedor" style="padding-top: 20px; padding-bottom: 60px;">
    <section>
        <h2 class="h2r patria">BÚSQUEDA DE ESCUELAS</h2>
        <hr class="red divider">

        <h3 class="sh1b mb--24" style="font-weight: 600; color: var(--neutro800);">
            Seleccione las características de las escuelas que desea buscar
        </h3>

        <form action="{{ route('siben.busqueda_escuelas.resultado') }}" method="POST">
            @csrf
            
            <!-- Componente oficial de tarjeta de búsqueda del SND -->
            <div class="buscar01">
                
                <!-- Retícula Oficial SND (12 Columnas) -->
                <div class="reticulaGrid__12 gap--24">
                    
                    <!-- Fila 1: Escuela (4 col) | Entidad (4 col) | Sostenimiento (4 col) -->
                    <div class="columna__4--md columna__12--xs">
                        <div class="form__grupo">
                            <label class="form__label">Escuela:</label>
                            <input class="form__control" name="Escuela[escuela]" placeholder="Nombre de la escuela" type="text" value="{{ old('Escuela.escuela') }}">
                        </div>
                    </div>

                    <div class="columna__4--md columna__12--xs">
                        <div class="form__grupo">
                            <label class="form__label">Entidad:</label>
                            <select class="form__control" name="Escuela[id_entidad]">
                                <option value="">-- Seleccione una entidad --</option>
                                @foreach($entidades as $id => $entidad)
                                    <option value="{{ $id }}" {{ old('Escuela.id_entidad') == $id ? 'selected' : '' }}>
                                        {{ $entidad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="columna__4--md columna__12--xs">
                        <div class="form__grupo">
                            <label class="form__label">Sostenimiento:</label>
                            <select class="form__control" name="Escuela[sostenimiento]">
                                <option value="">-- Seleccione el sostenimiento --</option>
                                <option value="p" {{ old('Escuela.sostenimiento') == 'p' ? 'selected' : '' }}>Pública</option>
                                <option value="q" {{ old('Escuela.sostenimiento') == 'q' ? 'selected' : '' }}>Particular</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 2: Turno (4 col) | Licenciatura (4 col) -->
                    <div class="columna__4--md columna__12--xs">
                        <div class="form__grupo">
                            <label class="form__label">Turno:</label>
                            <select class="form__control" name="Escuela[turno]">
                                <option value="">-- Seleccione el turno --</option>
                                @foreach($turno as $key => $label)
                                    <option value="{{ $key }}" {{ old('Escuela.turno') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="columna__4--md columna__12--xs">
                        <div class="form__grupo">
                            <label class="form__label">Licenciatura:</label>
                            <select class="form__control" name="EscuelaLicenciatura[id_licenciatura]">
                                <option value="">-- Seleccione una licenciatura --</option>
                                @foreach($licenciaturas as $id => $licenciatura)
                                    <option value="{{ $id }}" {{ old('EscuelaLicenciatura.id_licenciatura') == $id ? 'selected' : '' }}>
                                        {{ $licenciatura }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Fila 3: Opciones "Que cuente con:" (Ocupa 12 columnas completas) -->
                    <div class="columna__12--md columna__12--xs mt--8">
                        <label class="form__label mb--12" style="font-size: 1rem;">Que cuente con:</label>
                        <div class="dflex dflex--row dflex--colgap--32 dflex--acenter">
                            <label class="checkbox-snd">
                                <input type="checkbox" name="Correo[op1]" value="1" {{ old('Correo.op1') ? 'checked' : '' }}>
                                Correo electrónico
                            </label>

                            <label class="checkbox-snd">
                                <input type="checkbox" name="Pagina[op2]" value="1" {{ old('Pagina.op2') ? 'checked' : '' }}>
                                Página Web
                            </label>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Botonera oficial SND alineada a la derecha -->
            <div class="botonera botonera--end mt--32">
                <button class="boton__primario" type="submit">Buscar</button>
            </div>
        </form>
    </section>
</div>

<x-footer />
</body>
</html>