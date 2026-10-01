<header>
    <link href="{{ url('/images/favicon.ico') }}" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">

    <nav class="navbar navbar-inverse sub-navbar navbar-fixed-top" id="navbarFixedTop">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed"
                        data-toggle="collapse" data-target="#subenlaces">
                    <span class="sr-only">Interruptor de Navegación</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('home') }}">SIBEN</a>
            </div>

            <div class="collapse navbar-collapse" id="subenlaces">
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                           role="button" aria-expanded="false">
                            Ingresar <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                {{-- Abre el login en un popup de 917×600 --}}
                                <a href="{{ route('login') }}"
                                   onclick='window.open(this.href, "sibenLogin",
                                       "toolbar=no,location=no,directories=no,status=no,
                                        menubar=no,scrollbars=yes,resizable=no,
                                        width=917,height=600");
                                       return false;'>SIBEN</a>
                            </li>
                        </ul>
                    </li>

                    {{--
                        Estos enlaces apuntan a secciones que aún no están implementadas.
                        Se mantienen en el HTML pero sin href para evitar RouteNotFoundException.
                    --}}
                    <li class="last"><a href="{{ route('escuelas.busqueda') }}">Búsqueda</a></li>
                    <li class="last"><a href="{{ route('pages.masinformacion') }}">Más Información</a></li>     
                    <li class="last"><a href="{{ route('pages.estadisticasrecientes') }}">Estadísticas</a></li>
                    <li>
                        <a class="home_menu" href="http://dgesum.sep.gob.mx"
                           target="_blank" rel="noopener noreferrer">DGESUM</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

</header>
