<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('img/coordinator/logo_ULAT_ico.ico')}}">
    <title>@yield('title', 'Administrador')</title>

    <!-- Enlazar el archivo CSS usando asset() -->
    <link rel="stylesheet" href="{{ asset('css/styleAdmin.css') }}"> <!--principal-->

    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>
<body>
    <div class="wrapper"><!--envoltura-->
        <div id="sidebarBackdrop" class="sidebar-backdrop" aria-hidden="true"></div>
        <aside id="sidebar">
            <div class="h-100">
                <div class="sidebar-logo">
                    <img src="{{asset('img/admin/fondo_logo.png')}}" class="img-thumbnail" alt="logo">
                    <a href="{{route('admin.index')}}"><center><h2>Administrador</h2></center></a>
                </div>
                

                <!--menu de navegacion -->
                <ul class="sidebar-nav">
                    <li class="sidebar-item {{ Request::is('administrator/carreras*') ? 'active' : '' }}">
                        <a href="{{ route('admin.carreras.index') }}" class="sidebar-link">
                            <i class="bi bi-mortarboard pe-2"></i>
                            Carreras
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/sedes*') ? 'active' : '' }}">
                        <a href="{{route('sedes.index')}}" class="sidebar-link">
                            <i class="bi bi-building pe-2"></i>
                            Sedes
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/ambiente*') ? 'active' : '' }}">
                        <a href="{{route('ambiente.index')}}" class="sidebar-link">
                            <i class="bi bi-door-open pe-2"></i>
                            Ambientes
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/personal*') ? 'active' : '' }}">
                        <a href="{{route('personal.index')}}" class="sidebar-link">
                            <i class="bi bi-person-badge pe-2"></i>
                            Personal
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/usuarios*') ? 'active' : '' }}">
                        <a href="{{route('usuario.index')}}" class="sidebar-link">
                            <i class="bi bi-people pe-2"></i>
                            Usuarios
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/mantenimiento*') ? 'active' : '' }}">
                        <a href="{{route('mantenimientosAdmin.index')}}" class="sidebar-link">
                            <i class="bi bi-wrench-adjustable pe-2"></i>
                            Mantenimientos
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/movimientos*') ? 'active' : '' }}">
                        <a href="{{route('movimientosAdmin.index')}}" class="sidebar-link">
                            <i class="bi bi-arrow-left-right pe-2"></i>
                            Movimientos
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/accesorios*') ? 'active' : '' }}">
                        <a href="{{route('accesoriosadmin.index')}}" class="sidebar-link">
                            <i class="bi bi-cpu pe-2"></i>
                            Accesorios
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/prestamos*') ? 'active' : '' }}">
                        <a href="{{route('prestamosAdmin.index')}}" class="sidebar-link">
                            <i class="bi bi-journal-arrow-up pe-2"></i>
                            Prestamos
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('administrator/reportes*') ? 'active' : '' }}">
                        <a href="{{ route('reportes.index') }}" class="sidebar-link">
                            <i class="bi bi-bar-chart-line pe-2"></i>
                            Reportes
                        </a>
                    </li>  

                    <li class="sidebar-item {{ Request::is('administrator/descartes*') ? 'active' : '' }}">
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
                            <i class="bi bi-box-arrow-right pe-2"></i>
                            Salir
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!--Componentes para el contenido para cada pagina-->
        <div class="main">
            <!--Cabecera fija con container-scroll-->
            <header>
                <nav class="navbar navbar-expand px-2 px-sm-3 border-bottom d-flex flex-nowrap align-items-center">
                    <button id="toggleSidebar" type="button" class="btn btn-outline-secondary flex-shrink-0" aria-label="Alternar menu lateral">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="namaAdmin-container flex-grow-1 min-w-0 px-2 overflow-hidden">
                        <h5 class="namaAdmin mb-0 text-truncate fs-6 fw-semibold text-white">
                            @auth
                                {{ Auth::user()->nombre_completo }}
                            @endauth
                        </h5>
                    </div>

                    <div class="ms-auto flex-shrink-0 d-flex align-items-center gap-1">
                        <ul class="navbar-nav d-flex flex-row align-items-center gap-1 mb-0">
                            <!--contenido de los menus desplegables-->
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
    <script src="{{ asset('js/core/form-protection.js') }}"></script>

    <!-- Listener global de accesibilidad para modales Bootstrap 5 -->
    <script>
        document.addEventListener('hide.bs.modal', function (event) {
            if (document.activeElement && event.target.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>