<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('img/coordinator/logo_ULAT_ico.ico')}}">
    <title>@yield('title', 'Sistema de Gestión')</title>

    <link rel="stylesheet" href="{{ asset('css/encargado/styleEncargado.css') }}"> <!--principal-->
    <link rel="stylesheet" href="{{ asset('css/encargado/movimientoAmbiente.css') }}">
    <link rel="stylesheet" href="{{ asset('css/encargado/detallesUso.css') }}">

    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body{
            background: #bbcadc;
        }
    </style>

</head>
<body>
    <!-- Cabecera -->
    <header class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="{{route('encargado.inicio')}}">
                <img src="{{asset('img/admin/fondo_logo.png')}}" alt="Logo" width="50" height="50" >
                @auth
                <h7 class="text-light fw-bold">{{ Auth::user()->nombre_completo }}</h7>
                @endauth
            </a>

            <!-- Menú de navegación -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Menú Artículos con submenú -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Artículos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{route('equipo.create')}}">Equipos</a></li>
                            <li><a class="dropdown-item" href="{{route('mobiliario.create')}}">Mobiliarios</a></li>
                            <li><a class="dropdown-item" href="{{route('material.create')}}">Materiales</a></li>
                            <li><a class="dropdown-item" href="{{route('accesorio.create')}}">Accesorios</a></li>
                        </ul>
                    </li>
                    <!-- Otras opciones de menú -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('movimiento.ambiente.index') }}">Uso Laboratorios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('encargado.usuarios') }}">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('encargado.prestamo') }}">Prestamos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">Mantenimiento</a>
                    </li>

                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Salir
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
