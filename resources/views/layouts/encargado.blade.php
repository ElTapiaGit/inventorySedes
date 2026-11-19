<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('img/coordinator/logo_ULAT_ico.ico')}}">
    <title>@yield('title', 'Sistema de Gestión')</title>

    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Hoja de estilos del Encargado -->
    <link rel="stylesheet" href="{{ asset('css/encargado/styleEncargado.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('styles')
</head>
<body>
    <!-- Cabecera Institucional con Barra Superior -->
    <header class="navbar navbar-expand-lg navbar-dark encargado-navbar sticky-top">
        <div class="container-fluid">
            <!-- Logo y Datos del Encargado -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('encargado.inicio') }}">
                <img src="{{ asset('img/admin/fondo_logo.png') }}" alt="Logo ULAT" class="rounded-circle border border-2 border-warning me-2" width="46" height="46">
                <div class="d-flex flex-column">
                    <span class="brand-title fw-bold">INVENTARIO ULAT</span>
                    @auth
                        <span class="user-name fw-semibold">{{ Auth::user()->nombre_completo }}</span>
                    @endauth
                </div>
            </a>

            <!-- Boton Hamburguesa Responsive -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu de navegacion -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <!-- Menu Articulos con submenu -->
                    <li class="nav-item dropdown {{ Request::is('encargado/registro/*') || Request::is('encargado/mobiliario*') || Request::is('encargado/material*') || Request::is('encargado/accesorio*') || Request::is('encargado/equipo*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-plus-circle me-1"></i> Registrar Artículos
                        </a>
                        <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('equipo.create') }}"><i class="bi bi-pc-display me-2 text-primary"></i> Equipos</a></li>
                            <li><a class="dropdown-item" href="{{ route('mobiliario.create') }}"><i class="bi bi-chair me-2 text-primary"></i> Mobiliarios</a></li>
                            <li><a class="dropdown-item" href="{{ route('material.create') }}"><i class="bi bi-tools me-2 text-primary"></i> Materiales</a></li>
                            <li><a class="dropdown-item" href="{{ route('accesorio.create') }}"><i class="bi bi-plug me-2 text-primary"></i> Accesorios</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('encargado.inventario.index') }}"><i class="bi bi-box-seam me-2 text-primary"></i> Consultar Inventario</a></li>
                        </ul>
                    </li>

                    <!-- Consultar Inventario -->
                    <li class="nav-item {{ request()->routeIs('encargado.inventario.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('encargado.inventario.index') }}">
                            <i class="bi bi-box-seam me-1"></i> Consultar Inventario
                        </a>
                    </li>

                    <!-- Uso Laboratorios -->
                    <li class="nav-item {{ Request::is('encargado/ambientes*') || Request::is('encargado/movimientos*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('movimiento.ambiente.index') }}">
                            <i class="bi bi-door-open me-1"></i> Uso Laboratorios
                        </a>
                    </li>

                    <!-- Usuarios -->
                    <li class="nav-item {{ Request::is('encargado/usuarios*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('encargado.usuarios') }}">
                            <i class="bi bi-people me-1"></i> Usuarios
                        </a>
                    </li>

                    <!-- Prestamos -->
                    <li class="nav-item {{ Request::is('encargado/prestamos*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('encargado.prestamo') }}">
                            <i class="bi bi-arrow-left-right me-1"></i> Préstamos
                        </a>
                    </li>

                    <!-- Mantenimiento -->
                    <li class="nav-item {{ Request::is('encargado/mantenimientos*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('encargado.mantenimiento') }}">
                            <i class="bi bi-wrench-adjustable me-1"></i> Mantenimiento
                        </a>
                    </li>

                    <!-- Salir -->
                    <li class="nav-item ms-lg-2">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" class="nav-link text-white-50" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-1"></i> Salir
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="{{ asset('js/core/form-protection.js') }}"></script>
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
