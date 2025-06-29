@extends('layouts.admin')

@section('title', 'Detalles Mobiliario')

@section('content')
<main class="content px-3 pagMueble">
    <div class="container-fluid">
        <h1 class="text-center fw-bold">DETALLES DE MUEBLE</h1>

        <!-- Información del mueble -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-info text-dark">
                        <h5>Información del mueble</h5>
                    </div>
                    <div class="card-body d-flex flex-column mx-3">

                        <!-- Información del mueble -->
                        <div class="mueble-info flex-grow-1">
                            <p><strong>Código:</strong> {{ $mobiliario->cod_mueble }}</p>
                            <p><strong>Tipo de Mueble:</strong> {{ $mobiliario->tipoMobiliario->tipo_mueble }}</p>
                            <p><strong>Descripción:</strong> <br> {{ $mobiliario->descripticion_mueb }} </p>
                            <p><strong>Observaciones:</strong> <br> {{ $mobiliario->observacion }}</p>
                            <p><strong>Estado:</strong> {{ $mobiliario->estado_mueb }}</p>
                            <p><strong>Vida Útil:</strong> {{ $mobiliario->vida_util }}</p>
                            <p><strong>Fecha Registrado:</strong> {{ \Carbon\Carbon::parse($mobiliario->fch_registro)->format('d/m/Y') }}</p>
                        </div>
                    
                        <!-- Imagen del mueble en el lado derecho -->
                        <div class="mueble-foto mt-3 text-center">
                            @if($mobiliario->foto)
                                <img src="{{ asset($mobiliario->foto->ruta_foto) }}" alt="Foto del mueble" class="img-fluid">
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