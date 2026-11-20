@php
    $isClinicaContext = (isset($menuContext) && $menuContext === 'clinica')
        || Request::is('coordinator/clinica*', 'coordinator/busquedas*', 'coordinator/niveles*', 'coordinator/reportes-clinica*')
        || (View::hasSection('menu-context') && trim(View::yieldContent('menu-context')) === 'clinica');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/coordinator/logo_ULAT_ico.ico') }}">
    <title>@yield('title', 'Coordinador')</title>

    <!-- Hojas de estilo principales -->
    <link rel="stylesheet" href="{{ asset('css/stylecoordinator.css') }}">
    @if($isClinicaContext)
        <link rel="stylesheet" href="{{ asset('css/coordinador/laboratorios.css') }}">
        <link rel="stylesheet" href="{{ asset('css/coordinador/ambiente.css') }}">
        <link rel="stylesheet" href="{{ asset('css/coordinador/detallesequipo.css') }}">
        <link rel="stylesheet" href="{{ asset('css/coordinador/accesorio.css') }}">
        <link rel="stylesheet" href="{{ asset('css/coordinador/mantenimiento.css') }}">
        <link rel="stylesheet" href="{{ asset('css/coordinador/movimientos.css') }}">
        <link rel="stylesheet" href="{{ asset('css/coordinador/prestamos.css') }}">
    @endif

    <!-- Iconografia y Bootstrap 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/989bcc080d.js" crossorigin="anonymous" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('css')
    @yield('styles')
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <div id="sidebarBackdrop" class="sidebar-backdrop" aria-hidden="true"></div>
        <aside id="sidebar">
            <div class="h-100">
                <div class="sidebar-logo">
                    <img src="{{ $coordLogoUrl }}" class="img-thumbnail" alt="{{ $coordCarreraTitulo }}">
                    <a href="{{ $isClinicaContext ? route('coordinator.clinica.inicio') : route('coordinator.inicio') }}">
                        <center><h2>{{ $coordCarreraTitulo }}</h2></center>
                    </a>
                </div>

                <!-- Menu de navegacion -->
                <ul class="sidebar-nav">
                    @if($isClinicaContext)
                        <li class="sidebar-item {{ Request::is('coordinator/busquedas*') ? 'active' : '' }}">
                            <a href="{{ route('nivel.index') }}" class="sidebar-link">
                                <i class="bi bi-search pe-2"></i>
                                Buscar
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/clinica/ambiente*') ? 'active' : '' }}">
                            <a href="{{ route('coordinator.clinica.ambientes') }}" class="sidebar-link">
                                <i class="bi bi-door-open pe-2"></i>
                                Ambientes
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/clinica/accesorio*') ? 'active' : '' }}">
                            <a href="{{ route('clinica.accesorios.index') }}" class="sidebar-link">
                                <i class="bi bi-tools pe-2"></i>
                                Accesorios
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/clinica/mantenimiento*') || Request::is('coordinator/clinica/historial-mantenimiento*') ? 'active' : '' }}">
                            <a href="{{ route('clinica.mantenimiento.index') }}" class="sidebar-link">
                                <i class="bi bi-wrench pe-2"></i>
                                Mantenimientos
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/clinica/movimiento*') ? 'active' : '' }}">
                            <a href="{{ route('clinica.movimientos.index') }}" class="sidebar-link">
                                <i class="bi bi-arrow-left-right pe-2"></i>
                                Movimientos
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/clinica/prestamo*') ? 'active' : '' }}">
                            <a href="{{ route('clinica.prestamos.index') }}" class="sidebar-link">
                                <i class="bi bi-arrow-left-right pe-2"></i>
                                Prestamos
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/reportes-clinica*') || Request::is('coordinator/reportes/clinica*') ? 'active' : '' }}">
                            <a href="{{ route('reporte.clinica.index') }}" class="sidebar-link">
                                <i class="bi bi-bar-chart-line pe-2"></i>
                                Reportes
                            </a>
                        </li>
                    @else
                        <li class="sidebar-item {{ Request::is('coordinator/laboratorio*') || Request::is('coordinator/ambiente*') ? 'active' : '' }}">
                            <a href="{{ route('laboratorios.index') }}" class="sidebar-link">
                                <i class="bi bi-funnel pe-2"></i>
                                Laboratorio
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/accesorio*') ? 'active' : '' }}">
                            <a href="{{ route('accesorios.index') }}" class="sidebar-link">
                                <i class="bi bi-tools pe-2"></i>
                                Accesorios
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/mantenimiento*') || Request::is('coordinator/historial-mantenimiento*') ? 'active' : '' }}">
                            <a href="{{ route('mantenimiento.index') }}" class="sidebar-link">
                                <i class="bi bi-wrench pe-2"></i>
                                Mantenimientos
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/movimiento*') ? 'active' : '' }}">
                            <a href="{{ route('movimientos.index') }}" class="sidebar-link">
                                <i class="bi bi-arrow-left-right pe-2"></i>
                                Movimientos
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/prestamo*') ? 'active' : '' }}">
                            <a href="{{ route('prestamos.index') }}" class="sidebar-link">
                                <i class="bi bi-arrow-left-right pe-2"></i>
                                Prestamos
                            </a>
                        </li>
                        <li class="sidebar-item {{ Request::is('coordinator/reporte*') ? 'active' : '' }}">
                            <a href="{{ route('reporte.index') }}" class="sidebar-link">
                                <i class="bi bi-bar-chart-line pe-2"></i>
                                Reportes
                            </a>
                        </li>
                    @endif

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

        <!-- Contenido principal -->
        <div class="main">
            <!-- Barra superior / Cabecera -->
            <header>
                <nav class="navbar navbar-expand px-2 px-sm-3 border-bottom d-flex flex-nowrap align-items-center">
                    <button id="toggleSidebar" type="button" class="btn btn-outline-secondary flex-shrink-0" aria-label="Alternar menu lateral">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="namaAdmin-container flex-grow-1 min-w-0 px-2 overflow-hidden">
                        <h5 class="namaAdmin mb-0 text-truncate fs-6 fw-semibold">
                            @auth
                                {{ Auth::user()->nombre_completo }}
                            @endauth
                        </h5>
                    </div>
                    <div class="ms-auto flex-shrink-0">
                        <div class="dropdown px-1 position-relative">
                            <a class="nav-link dropdown-toggle white-text text-truncate d-inline-block" href="#" id="branchDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 170px;">
                                <i class="bi bi-building me-1"></i>
                                <span class="d-none d-sm-inline">{{ $edificioActivo ? $edificioActivo->nombre_edi : ($isClinicaContext ? 'Clinica Odontologia' : 'Edificio Principal') }}</span>
                                <span class="d-inline d-sm-none">{{ Str::limit($edificioActivo ? $edificioActivo->nombre_edi : ($isClinicaContext ? 'Clinica' : 'Principal'), 12) }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="branchDropdown" id="optio">
                                @if(View::hasSection('dropdown-items'))
                                    @yield('dropdown-items')
                                @else
                                    @if(isset($edificios) && count($edificios) > 0)
                                        @foreach($edificios as $edificioItem)
                                            <li>
                                                <a class="dropdown-item {{ (isset($edificioActivoId) && $edificioActivoId == $edificioItem->id_edificio) ? 'active' : '' }}" 
                                                   href="{{ route('coordinator.edificio.seleccionar', ['id' => $edificioItem->id_edificio]) }}">
                                                    <i class="bi bi-building pe-1"></i> {{ $edificioItem->nombre_edi }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                @endif
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>

            <!-- Contenido dinamico de la pagina -->
            @yield('content')
        </div>
    </div>

    <!-- Scripts y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="{{ asset('js/dashboarNone.js') }}"></script>
    <script src="{{ asset('js/core/form-protection.js') }}"></script>

    <!-- Listener global de accesibilidad para modales Bootstrap 5 -->
    <script>
        document.addEventListener('hide.bs.modal', function (event) {
            if (document.activeElement && event.target.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
