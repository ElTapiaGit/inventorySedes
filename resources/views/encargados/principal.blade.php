<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Opciones</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <style>
        body{
            background: #bee4ce;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Lista de Opciones</h1>
        <div class="row justify-content-center">
            <!-- Botón para Agregar Equipos -->
            <div class="col-md-3 mb-3">
                <a href="{{ route('encargado.index')}}" class="btn btn-primary btn-block">
                    Agregar Equipos
                </a>
            </div>
            <!-- Botón para Agregar Mobiliario -->
            <div class="col-md-3 mb-3">
                <a href="{{ route('mobiliario.create')}}" class="btn btn-secondary btn-block">
                    Agregar Mobiliario
                </a>
            </div>
            <!-- Botón para Agregar Materiales -->
            <div class="col-md-3 mb-3">
                <a href="{{route('material.create')}}" class="btn btn-success btn-block">
                    Agregar Materiales
                </a>
            </div>
            <!-- Botón para Agregar Accesorios -->
            <div class="col-md-3 mb-3">
                <a href="{{ route('accesorio.create')}}" class="btn btn-danger btn-block">
                    Agregar Accesorios
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
