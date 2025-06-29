@extends('layouts.pagClinica')

@section('title', 'Imagen Accesoirio-Equipo')

@section('content')
<main class="content px-3">
    <div class="container-fluid text-center">
        <h1 class="mb-4">Foto del Accesorio</h1>
        <div class="zoom-container">
            <img id="fotoAccesorio" src="{{ asset($fotos->ruta_foto) }}" alt="Foto del accesorio" class="img-fluid">
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

<!--Para mostrar el desplegable de edificios de la sede central-->
@section('dropdown-items')
    @foreach($edificios as $edificio)
        @if($edificio->nombre_edi == 'Edificio Central')
            <li><a class="dropdown-item" href="{{ route('coordinator.inicio') }}">{{ $edificio->nombre_edi }}</a></li>
        @elseif($edificio->nombre_edi == 'Clinica Odontologia')
            <li><a class="dropdown-item" href="{{ route('coordinator.clinica.inicio') }}">{{ $edificio->nombre_edi }}</a></li>
        @else
            <li><a class="dropdown-item" href="#">{{ $edificio->nombre_edi }}</a></li>
        @endif
    @endforeach
@endsection
