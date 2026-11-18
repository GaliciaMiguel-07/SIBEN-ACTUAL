@extends('layouts.siben.siben')

@section('titulo', 'Fotografías de la escuela')

@section('content')
<div class="contenedor" style="max-width:1000px; margin:0 auto; padding:30px 16px; width:100%;">

    <h2 class="patria">Fotografías de la escuela</h2>

    <p>{{ $escuela->escuela }}</p>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

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

    <p>
        Puedes registrar hasta seis fotografías de la infraestructura de la escuela.
        El formato permitido es JPG o JPEG, con un tamaño máximo de 5 MB.
    </p>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px; margin-top:24px;">

        @foreach ($imagenes as $numero => $imagen)
            <div style="border:1px solid var(--neutro400); border-radius:8px; padding:16px;">

                <h3>Fotografía {{ $numero }}</h3>

                @if ($imagen)
                    <img
                        src="{{ asset('img/escuelas/big/'.$escuela_key.'_'.$numero.'.jpg') }}"
                        alt="Fotografía {{ $numero }} de {{ $escuela->escuela }}"
                        style="width:100%; height:190px; object-fit:contain; background:#f5f5f5;"
                    >

                    <p style="margin-top:10px;">Fotografía registrada.</p>
                @else
                    <div style="height:190px; display:flex; align-items:center; justify-content:center; background:#f5f5f5; border:1px dashed var(--neutro400);">
                        <span>Espacio disponible</span>
                    </div>
                @endif

            </div>
        @endforeach

    </div>

    @if ($subir)
        <section style="margin-top:30px; padding:20px; border:1px solid var(--neutro400); border-radius:8px;">

            <h3>Subir fotografía {{ $id }}</h3>

            <form
                method="POST"
                action="{{ route('siben.subeImagen', ['id' => $id]) }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div style="margin-bottom:18px;">
                    <label for="imagen">Selecciona una fotografía JPG o JPEG</label>

                    <input
                        type="file"
                        id="imagen"
                        name="imagen"
                        accept=".jpg,.jpeg,image/jpeg"
                        required
                        class="form-control"
                    >
                </div>

                <button type="submit" class="boton__primario" style="padding:10px 20px;">
                    Subir fotografía
                </button>
            </form>

        </section>
    @else
        <div class="alert alert-info" style="margin-top:24px;">
            La escuela ya tiene registradas las seis fotografías disponibles.
        </div>
    @endif

    <div style="display:flex; justify-content:center; margin-top:25px;">
        <a
            href="{{ route('siben.estructuraEscuela') }}"
            class="boton__secundario"
            style="padding:10px 20px; text-decoration:none;"
        >
            Regresar a infraestructura
        </a>
    </div>

</div>
@endsection