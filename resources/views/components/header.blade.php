<header>
    <link href="{{ asset('favicon.ico') }}" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
    
    <nav class="navbar navbar-inverse sub-navbar navbar-fixed-top" id="navbarFixedTop">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#subenlaces">
                    <span class="sr-only">Interruptor de Navegación</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">SIBEN</a>
            </div>
            
            <div class="collapse navbar-collapse" id="subenlaces">
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="{{ url('/acerca_de') }}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            Ingresar <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="#" onclick='window.open("","Primera","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=917,height=600"); return false;'>SIBEN</a>
                            </li>
                        </ul>
                    </li>

                    <li class="last"><a href="{{ route('escuelas.busqueda') }}">Búsqueda</a></li>
                    <li class="last"><a href="{{ route('pages.masinformacion') }}">Más Información</a></li>     
                    <li class="last"><a href="{{ url('/estadisticas') }}">Estadísticas</a></li>
                    <li><a class="home_menu" href="http://dgesum.sep.gob.mx" target="_blank" rel="noopener noreferrer">DGESUM</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div style="background:#393c3e; color: white; text-align: center; padding: 10px; font-size: 17px; font-weight: bold;">
        Sistema de Información Básica de la Educación Normal (SIBEN)
    </div>
</header>