<header>
    <link href="{{ url('/images/favicon.ico') }}" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/styles/main.css" rel="stylesheet">

    <nav class="navbar navbar-expand-md navbar-dark bg-light sub-navbar fixed-top">
  <div class="container">
    <button
      type="button"
      class="navbar-toggler"
      data-bs-toggle="collapse"
      data-bs-target="#subNavBarDropdown"
      aria-controls="subNavBarDropdown"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <a class="navbar-brand sub-navbar" href="{{ route('home') }}">SIBEN</a>

    <div class="collapse navbar-collapse" id="subNavBarDropdown">
      {{-- Nota: Si necesitas que los elementos se alineen a la derecha como en el original, puedes cambiar "navbar-nav" por "navbar-nav ms-auto" --}}
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a
            class="nav-link subnav-link dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            Ingresar
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li>
              {{-- Abre el login en un popup de 917×600 --}}
              <a class="dropdown-item" href="{{ route('login') }}"
                 onclick='window.open(this.href, "sibenLogin",
                     "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=917,height=600");
                     return false;'>SIBEN</a>
            </li>
          </ul>
        </li>

        {{--
            Estos enlaces apuntan a secciones que aún no están implementadas.
            Se mantienen en el HTML pero sin href para evitar RouteNotFoundException.
        --}}
        <li class="nav-item">
          <a class="nav-link subnav-link" href="{{ route('escuelas.busqueda') }}">Búsqueda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link subnav-link" href="{{ route('pages.masinformacion') }}">Más Información</a>
        </li>     
        <li class="nav-item">
          <a class="nav-link subnav-link" href="{{ route('pages.estadisticasrecientes') }}">Estadísticas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link subnav-link" href="http://dgesum.sep.gob.mx" target="_blank" rel="noopener noreferrer">DGESUM</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


</header>
