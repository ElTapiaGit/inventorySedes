@extends('layouts.admin')

@section('title', 'Imagen Equipo')

@push('styles')
<style>
    .zoom-container {
        position: relative;
        width: 100%;
        max-width: 600px;
        margin: auto;
    }

    .zoom-container img {
        width: 100%;
        height: auto;
        transition: transform 0.2s;
    }

    .zoom-container:hover img {
        transform: scale(1.5);
    }
</style>
@endpush

@section('content')
<main class="content px-3">
    <div class="container-fluid text-center">
        <h1 class="mb-4">Foto del Accesorio</h1>
        <div class="zoom-container">
            <img id="fotoAccesorio" src="{{ $ruta_foto }}" alt="Foto del accesorio" class="img-fluid">
        </div>
    </div>
</main>
@endsection

@push('scripts')
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
@endpush
