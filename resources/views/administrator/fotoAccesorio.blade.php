@extends('layouts.admin')

@section('title', 'Imagen Equipo')

@section('content')
<main class="content px-3">
    <div class="container-fluid text-center">
        <h1 class="mb-4">Foto del Accesorio</h1>
        <div class="zoom-container">
            <img id="fotoAccesorio" src="{{ $ruta_foto }}" alt="Foto del accesorio" class="img-fluid">
        </div>
    </div>
    <!-- SweetAlert2 Scripts -->
    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ $errors->first() }}',
            });
        </script>
    @endif
</main>

@section('styles')
<style>
    .zoom-container {
        position: relative;
        width: 100%;
        max-width: 600px; /* Ajusta el tamaño máximo según sea necesario */
        margin: auto;
    }

    .zoom-container img {
        width: 100%;
        height: auto;
        transition: transform 0.2s; /* Hace el zoom suave */
    }

    .zoom-container:hover img {
        transform: scale(1.5); /* Escala la imagen al hacer hover */
    }
</style>
@endsection
@endsection
