<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/coordinator/logo_ULAT_ico.ico') }}">
    <title>Iniciar Sesión - Sistema de Inventario</title>
    <link rel="stylesheet" href="{{ asset('css/loginStyle.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <img src="{{ asset('img/logo.webp') }}" alt="Logo ULAT" class="logo">
            <h2>INICIO DE SESIÓN</h2>
            <h2>SISTEMA DE INVENTARIO</h2>
        </div>
        <div class="right-section">
            <noscript>
                @if($errors->has('login'))
                    <div class="alert-error">
                        {{ $errors->first('login') }}
                    </div>
                @endif
            </noscript>

            <form id="loginForm" action="{{ route('login') }}" method="POST" autocomplete="off">
                @csrf
                <div class="input-container">
                    <label for="username" class="input-label">Usuario</label>
                    <input type="text" id="username" name="username" required autocomplete="username" spellcheck="false" autofocus>
                </div>
                <div class="input-container">
                    <label for="password" class="input-label">Contraseña</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" spellcheck="false">
                    <span class="toggle-password" role="button" aria-label="Mostrar u ocultar contraseña">
                        <i id="toggleIcon" class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="btn-ingresar">
                    <button type="submit" id="loginButton">Ingresar</button>
                </div>
            </form>
        </div>
    </div> 

    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
