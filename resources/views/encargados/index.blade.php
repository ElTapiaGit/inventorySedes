@extends('layouts.encargado')

@section('title', 'Página Principal')

@section('content')
<div class="container text-center mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="p-5 bg-light rounded shadow-sm">
                <h1 class="display-4">Bienvenido al Sistema de Gestión</h1>
                <p class="lead">Esta es la página principal.</p>
            </div>
        </div>
    </div>



    <!-- Mostrar mensajes de error -->
    @if($errors->any())
        <script>
            // Crear un array para almacenar todos los mensajes de error
            var errorMessages = [];
            
            // Recorrer los mensajes de error y agregarlos al array
            @foreach($errors->all() as $error)
                errorMessages.push('{{ $error }}');
            @endforeach
            
            // Mostrar todos los mensajes de error en un solo alerta
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: '<ul>' + errorMessages.map(msg => `<li>${msg}</li>`).join('') + '</ul>',
                confirmButtonText: 'Aceptar'
            });
        </script>
    @endif
</div>
@endsection
