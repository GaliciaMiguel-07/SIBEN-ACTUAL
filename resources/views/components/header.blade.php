<header class="header">
  {{-- Favicon y CSS institucional --}}
  <script src="https://framework-gb.cdn.gob.mx/snd/v1/carga.js"
  integrity="sha384-co1XtMH+rcHM2juJRJdlpTdJfL9wAc1/EJ+71hoqFAIK4C7KEdcwhrtt8BPuwEWV"
  crossorigin="anonymous"
  data-tema="guinda"
  defer></script>

  {{-- 2. Barra de Navegación del Sistema SIBEN --}}
  <section class="subheader">
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
              <a href="{{ route('home') }}" aria-current="page" class="navHeader__a">
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

      {{-- Botón de Ingresar directo --}}
      <div class="iniciarSesion">
        <a href="{{ route('login') }}" 
           class="iniciarSesion__a cta">
          <span class="iniciarSesion__txt">
            Ingresar
          </span>
        </a>
      </div>

    </div>
  </section>
</header>