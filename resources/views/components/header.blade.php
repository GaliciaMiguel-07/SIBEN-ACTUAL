<header class="header">
  {{-- Favicon y CSS institucional --}}
  <link href="https://www.snd.gob.mx/snd/v1/snd-guinda.css" rel="stylesheet">

  <a class="irContent" href="#mainContent">
    Ir al contenido principal
  </a>

  {{-- 1. Encabezado Institucional Gob.mx --}}
  <section class="mexico">
    <div class="mexico__contenedor">
      <div class="mexico__escudo">
        <a href="https://www.gob.mx/" class="mexico__aescudo">
          <i class="snd__icono snd-gobierno-mexico mexico__img color--origen" role="img" aria-label="Ir a la página de inicio del Gobierno de México"></i>
        </a>
      </div>
      <div class="mexico__menu">
        <details class="mexico__details">
          <summary class="mexico__summary">
            <span class="mexico__span">
              Menú
            </span>
          </summary>
          <div class="mexico__detailsCont">
            <a href="https://www.gob.mx/tramites" class="mexico__a">
              Trámites
            </a>
            <a href="https://www.gob.mx/gobierno" class="mexico__a">
              Gobierno
            </a>
          </div>
        </details>
      </div>
    </div>
  </section>

  {{-- 2. Barra de Navegación del Sistema SIBEN --}}
<section class="subheader" style="margin-top: 50px;">
    <div class="subheader__contenedor">
      {{-- Menú de secciones --}}
      <details class="mexico__details navHeader__details">
        <summary class="mexico__summary">
          <span class="mexico__span">
            Menú
          </span>
        </summary>
        <nav class="navHeader">
          <ul class="navHeader__ul mexico__detailsCont">
            <li class="navHeader__li">
              <a href="{{ route('home') }}" class="navHeader__a">
                Inicio
              </a>
            </li>
            <li class="navHeader__li">
              <a href="{{ route('escuelas.busqueda') }}" class="navHeader__a">
                Búsqueda
              </a>
            </li>
            <li class="navHeader__li">
              <a href="{{ route('pages.masinformacion') }}" class="navHeader__a">
                Más Información
              </a>
            </li>
            <li class="navHeader__li">
              <a href="{{ route('pages.estadisticasrecientes') }}" class="navHeader__a">
                Estadísticas
              </a>
            </li>
            <li class="navHeader__li">
              <a href="http://dgesum.sep.gob.mx" class="navHeader__a" target="_blank" rel="noopener noreferrer">
                DGESUM
              </a>
            </li>
          </ul>
        </nav>
      </details>

      {{-- Botón de Accesibilidad --}}
      <button type="button" class="boton__limpio">
        <i class="snd__icono snd-accessibility--alt icono--32 icono--circuloRojo" aria-hidden="true"></i>
        <span class="oculto">
          Opciones de accesibilidad
        </span>
      </button>

      {{-- Botón de Ingresar directo (Abre la ventana emergente/popup sin submenú) --}}
      <div class="iniciarSesion">
        <a href="{{ route('login') }}" 
           class="iniciarSesion__a cta"
           onclick='window.open(this.href, "sibenLogin", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=917,height=600"); return false;'>
          <span class="iniciarSesion__txt">
            Ingresar
          </span>
        </a>
      </div>

    </div>
  </section>
</header>