@extends('layouts.admin')

@section('title', 'Detalles de Material')

@section('content')
<main class="content px-3 pagMaterial">
    <div class="container-fluid">
        <h1 class="text-center fw-bold">DETALLES DE MATERIAL</h1>

        <!-- Información del material -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-info text-dark">
                        <h5>Información del Material</h5>
                    </div>
                    <div class="card-body d-flex flex-column mx-3">

                        <!-- Información del material -->
                        <div class="mueble-info flex-grow-1">
                            <p><strong>Código:</strong> {{ $material->cod_mate }}</p>
                            <p><strong>Tipo de material:</strong> {{ $material->tipo_mate }}</p>
                            <p><strong>Descripción:</strong> <br> {{ $material->descripcion_mate }} </p>
                            <p><strong>Estado:</strong> {{ $material->estado_mate }}</p>
                            <p><strong>Observaciones:</strong> <br> {{ $material->observacion_mate }}</p>
                            <p><strong>Fecha Registrada:</strong> {{ \Carbon\Carbon::parse($material->fch_registrada)->format('d/m/Y') }}</p>
                        </div>
                    
                        <!-- Imagen del material en el lado derecho -->
                        <div class="mueble-foto mt-3 text-center">
                            @if($material->foto)
                                <img src="{{ asset($material->foto->ruta_foto) }}" alt="Foto del material" class="img-fluid">
                            @else
                                <p>No hay foto disponible</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- Mensajes de error al obtener los datos -->
@if($errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ $errors->first() }}',
        });
    </script>
@endif
@endsection
