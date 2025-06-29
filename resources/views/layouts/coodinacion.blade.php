<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('img/coordinator/logo_ULAT_ico.ico')}}">
    <title>@yield('title', 'Coordinador')</title>

    <!-- Enlazar el archivo CSS usando asset() -->
    <link rel="stylesheet" href="{{ asset('css/stylecoordinator.css') }}"> <!--principal-->
    <link rel="stylesheet" href="{{ asset('css/coordinador/laboratorios.css') }}"> <!--para laboratorios-->
    <link rel="stylesheet" href="{{ asset('css/coordinador/ambiente.css') }}"> <!--para laboratorios-->
    <link rel="stylesheet" href="{{ asset('css/coordinador/detallesequipo.css') }}"> <!--para detalles equipos-->
    <link rel="stylesheet" href="{{ asset('css/coordinador/accesorio.css') }}"> <!--para detalles equipos-->
    <link rel="stylesheet" href="{{ asset('css/coordinador/mantenimiento.css') }}"> 
    <link rel="stylesheet" href="{{ asset('css/coordinador/movimientos.css') }}"> 
    <link rel="stylesheet" href="{{ asset('css/coordinador/prestamos.css') }}"> 
    
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/989bcc080d.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="wrapper"><!--envoltura-->
        <aside id="sidebar">
            <div class="h-100">
                <div class="sidebar-logo">
                    <img src="{{asset('img/coordinator/logo_donto.png')}}" class="img-thumbnail" alt="logo">
                    <a href="{{route('coordinator.inicio')}}"><center><h2>Carrera de Odontologia</h2></center></a>
                </div>

                <!--menu de navegacion -->
                <ul class="sidebar-nav">
                    <li class="sidebar-item {{ Request::is('coordinator/laboratorio*') ? 'active' : '' }}">
                        <a href="{{ route('laboratorios.index') }}" class="sidebar-link">
                            <i class="fa-solid fa-flask pe-2"></i>
                            Laboratorio
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('coordinator/accesorio*') ? 'active' : '' }}">
                        <a href="{{route('accesorios.index')}}" class="sidebar-link">
                            <i class="fa-solid fa-toolbox pe-2"></i>
                            Accesorios
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('coordinator/mantenimiento*') ? 'active' : '' }}">
                        <a href="{{route('mantenimiento.index')}}" class="sidebar-link">
                            <i class="fa-solid fa-tools pe-2"></i>
                            Mantenimientos
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('coordinator/movimiento*') ? 'active' : '' }}">
                        <a href="{{ route('movimientos.index')}} " class="sidebar-link">
                            <i class="fa-solid fa-arrows-alt pe-2"></i>
                            Movimientos
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('coordinator/prestamo*') ? 'active' : '' }}">
                        <a href="{{ route('prestamos.index')}}" class="sidebar-link">
                            <i class="fa-solid fa-handshake pe-2"></i>
                            Prestamos
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('coordinator/reporte*') ? 'active' : '' }}">
                        <a href="{{ route('reporte.index') }}" class="sidebar-link">
                            <i class="fa-solid fa-chart-line pe-2"></i>
                            Reportes
                        </a>
                    </li>  
                    
                    <li class="sidebar-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" class="sidebar-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa-solid fa-sign-out-alt pe-2"></i>
                            Salir
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!--Componentes para el contenido para cada pagina-->
        <div class="main">
            <!--Cabecera-->
            <header>
                <nav class="navbar navbar-expand px-3 border-bottom">
                    <button id="toggleSidebar" type="button" class="btn btn-outline-secondary">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <h5 class="namaAdmin">
                        @auth
                            {{ Auth::user()->nombre_completo }}
                        @endauth
                    </h5>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown px-1">
                                <a class="nav-link dropdown-toggle white-text" href="#" id="branchDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Edificio Principal
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="branchDropdown" id="optio">
                                    @yield('dropdown-items')
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>      

            <!--Contenidos-->
            @yield('content')

        </div>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="{{asset('js/dashboarNone.js')}}"></script>
    
</body>
</html>