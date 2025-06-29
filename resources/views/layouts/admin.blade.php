<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('img/coordinator/logo_ULAT_ico.ico')}}">
    <title>@yield('title', 'Administrador')</title>

    <!-- Enlazar el archivo CSS usando asset() -->
    <link rel="stylesheet" href="{{ asset('css/styleAdmin.css') }}"> <!--principal-->
    <link rel="stylesheet" href="{{ asset('css/administrador/sedeAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/ambienteAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/tipoyAmbiente.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/DetallesMueble.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/personal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/usuario.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/mantenimiento.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/detallesArticulos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/movimiento.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/descarte.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/prestamos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/administrador/accesorios.css') }}">
    

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
                    <img src="{{asset('img/admin/fondo_logo.png')}}" class="img-thumbnail" alt="logo">
                    <a href="{{route('admin.index')}}"><center><h2>Administrador</h2></center></a>
                </div>
                

                <!--menu de navegacion -->
                <ul class="sidebar-nav">
                    <li class="sidebar-item {{ Request::is('administrator/sedes*') ? 'active' : '' }}">
                        <a href="{{route('sedes.index')}}" class="sidebar-link">
                            <i class="fas fa-building pe-2"></i>
                            Sedes
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/ambiente*') ? 'active' : '' }}">
                        <a href="{{route('ambiente.index')}}" class="sidebar-link">
                            <i class="fas fa-door-open pe-2"></i>
                            Ambientes
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/personal*') ? 'active' : '' }}">
                        <a href="{{route('personal.index')}}" class="sidebar-link">
                            <i class="fas fa-user-tie pe-2"></i>
                            Personal
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/usuarios*') ? 'active' : '' }}">
                        <a href="{{route('usuario.index')}}" class="sidebar-link">
                            <i class="fas fa-users-cog pe-2"></i>
                            Usuarios
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/mantenimiento*') ? 'active' : '' }}">
                        <a href="{{route('mantenimientosAdmin.index')}}" class="sidebar-link">
                            <i class="fa-solid fa-tools pe-2"></i>
                            Mantenimientos
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/movimientos*') ? 'active' : '' }}">
                        <a href="{{route('movimientosAdmin.index')}}" class="sidebar-link">
                            <i class="fa-solid fa-arrows-alt pe-2"></i>
                            Movimientos
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/accesorios*') ? 'active' : '' }}">
                        <a href="{{route('accesoriosadmin.index')}}" class="sidebar-link">
                            <i class="fa-solid fa-toolbox pe-2"></i>
                            Accesorios
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/prestamos*') ? 'active' : '' }}">
                        <a href="{{route('prestamosAdmin.index')}}" class="sidebar-link">
                            <i class="fa-solid fa-handshake pe-2"></i>
                            Prestamos
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('reportes.index') }}" class="sidebar-link">
                            <i class="fa-solid fa-chart-line pe-2"></i>
                            Reportes
                        </a>
                    </li>  

                    <li class="sidebar-item {{ Request::is('administrator/reportes*') ? 'active' : '' }}">
                        <a href="{{ route('descartes.index') }}" class="sidebar-link">
                            <i class="bi bi-trash pe-2"></i>
                            Descartes
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

                            <!--contenido de los menus desplecables-->
                            @yield('sede-dropdown')
                            @yield('edificio-dropdown')
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