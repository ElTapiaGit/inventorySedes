<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('img/coordinator/logo_ULAT_ico.ico')}}">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="{{ asset('css/loginStyle.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <img src="{{asset('img/logo.webp')}}" alt="Logo" class="logo">
            <h2>INICIO DE SESIÓN</h2>
            <h2>SISTEMA DE INVENTARIO</h2>
        </div>
        <div class="right-section">
            <form id="loginForm" action="{{ route('login')}}" method="POST">
                @csrf
                <div class="input-container">
                    <label for="username" class="input-label">Usuario</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-container">
                    <label for="password" class="input-label">Contraseña</label>
                    <input type="password" id="password" name="password" required autocomplete="off">
                    <span class="toggle-password" onclick="togglePasswordVisibility()">
                        <i id="toggleIcon" class="fas fa-eye"></i> <!-- Usando FontAwesome para el icono -->
                    </span>
                </div>
                <div class="btn-ingresar">
                    <button type="submit" id="loginButton">Ingresar</button>
                </div>
                
            </form>

            @if(session('login'))
                <script>
                    Swal.fire({
                        icon: 'info',
                        text: {!! json_encode(session('login')) !!} //evitar inyection js
                    });
                </script>
            @endif
            @if($errors->has('login')) 
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: {!! json_encode($errors->first('login')) !!} //para sms muchos intentos
                });
            </script>
            @endif
        </div>
    </div> 

<script src="{{asset('js/login.js')}}"></script>
</body>
</html>
