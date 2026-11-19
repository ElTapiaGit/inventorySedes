@extends('layouts.coordinator')

@section('title', 'Imagen Accesorio-Equipo')

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
        transform: scale(1.3);
    }
</style>
@endpush

@section('content')
<main class="content px-3 py-3">
    <div class="container-fluid">
        <!-- Barra de navegacion -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                <i class="bi bi-image me-2 text-primary"></i>Fotografia del Accesorio
            </h4>
            <button type="button" class="btn btn-outline-secondary d-flex align-items-center" onclick="window.history.back()">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </button>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 text-center">
                        <div class="zoom-container p-2 border rounded bg-light mb-3">
                            <img id="fotoAccesorio"
                                 src="{{ inventario_img_url($fotos->ruta_foto ?? null) }}"
                                 alt="Foto del accesorio"
                                 class="img-fluid rounded"
                                 onerror="this.onerror=null;this.src='{{ asset('img/placeholder-articulo.svg') }}';">
                        </div>
                        <p class="text-muted small mb-0">Pase el cursor sobre la imagen para ampliar los detalles.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
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
