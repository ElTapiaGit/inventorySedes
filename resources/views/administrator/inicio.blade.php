
@extends('layouts.admin')

@section('title', 'Inicio')

@section('content')
    <main class="contentini p-3">  
        <div class="container-fluid">
                <div class="mb-3">`
                    <!--<h3>HOLA BIENVENIDO AL CONTENIDO DE LA PAGINA</h3>-->
                </div>
        </div>
    </main>

    <!--mensaje de error de conexion de la BD-->    
    @if(session('errordata'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errordata') }}',
            });
        </script>
    @endif
    
@endsection

