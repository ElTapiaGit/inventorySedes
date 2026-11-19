@extends('layouts.coordinator')

@section('title', 'Detalles del Material')

@section('content')
<main class="content px-3">
    <div class="container-fluid">
        <h1 class="text-center fw-bold">DETALLES DE MATERIAL</h1>

        <!-- Información del material -->
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-info text-dark">
                        <h5>Información del Material</h5>
                    </div>
                    <div class="card-body d-flex flex-column flex-md-row">
                        <!-- Información del material -->
                        <div class="flex-grow-1 mb-3 mb-md-0" style="max-width: 500px">
                            <p><strong>Código:</strong> {{ $material->cod_mate }}</p>
                            <p><strong>Ambiente:</strong> {{ $material->nombre ?? 'N/A' }}</p>
                            <p><strong>Tipo de material:</strong> {{ $material->tipo_mate }}</p>
                            <p style="text-align: justify;"><strong>Descripción:</strong><br> {{ $material->descripcion_mate }}</p>
                            <p><strong>Estado:</strong> {{ $material->estado_mate }}</p>
                            <p style="text-align: justify;"><strong>Observaciones:</strong><br> {{ $material->observacion_mate }}</p>
                            <p><strong>Fecha Registrada:</strong> {{ \Carbon\Carbon::parse($material->fch_registrada)->format('d/m/Y') }}</p>
                        </div>
                    
                        <!-- Imagen del material en el lado derecho -->
                        <div class="ml-md-3 px-2">
                            @if($material->ruta_foto)
                                <img src="{{ inventario_img_url($material->ruta_foto ?? null) }}" alt="Foto del material" class="img-fluid img-thumbnail" style="max-width: 600px;">
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
@endsection
