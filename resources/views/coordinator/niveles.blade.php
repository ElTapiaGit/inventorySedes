@extends('layouts.coordinator')

@section('title', 'Busquedas Rapidas')

@section('content')
<main class="content px-3 py-3">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                <i class="bi bi-search me-2 text-primary"></i>Busquedas Rapidas de Articulos
            </h4>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Buscar Equipo -->
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-pc-display me-2 text-primary"></i> Buscar Equipo por Codigo
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('niveles.buscarEquipo') }}" method="POST">
                            @csrf
                            <label for="buscar_codigo_equipo" class="form-label text-muted small fw-semibold">Codigo del Equipo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i class="bi bi-hash"></i></span>
                                <input type="text" id="buscar_codigo_equipo" name="codigo" class="form-control" placeholder="Ej: EQ-001" required aria-label="Codigo de Equipo">
                                <button type="submit" class="btn btn-primary d-flex align-items-center">
                                    <i class="bi bi-search me-1"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Buscar Mobiliario -->
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-chair me-2 text-primary"></i> Buscar Mobiliario por Codigo
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('niveles.buscarMobiliario') }}" method="POST">
                            @csrf
                            <label for="buscar_codigo_mobiliario" class="form-label text-muted small fw-semibold">Codigo del Mobiliario</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i class="bi bi-hash"></i></span>
                                <input type="text" id="buscar_codigo_mobiliario" name="codigo" class="form-control" placeholder="Ej: MB-001" required aria-label="Codigo de Mobiliario">
                                <button type="submit" class="btn btn-primary d-flex align-items-center">
                                    <i class="bi bi-search me-1"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Buscar Material -->
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-tools me-2 text-primary"></i> Buscar Material por Codigo
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('niveles.buscarMaterial') }}" method="POST">
                            @csrf
                            <label for="buscar_codigo_material" class="form-label text-muted small fw-semibold">Codigo del Material</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i class="bi bi-hash"></i></span>
                                <input type="text" id="buscar_codigo_material" name="codigo" class="form-control" placeholder="Ej: MT-001" required aria-label="Codigo de Material">
                                <button type="submit" class="btn btn-primary d-flex align-items-center">
                                    <i class="bi bi-search me-1"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Buscar Accesorio -->
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-plug me-2 text-primary"></i> Buscar Accesorio por Codigo
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('niveles.buscarAccesorio') }}" method="POST">
                            @csrf
                            <label for="buscar_codigo_accesorio" class="form-label text-muted small fw-semibold">Codigo del Accesorio</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i class="bi bi-hash"></i></span>
                                <input type="text" id="buscar_codigo_accesorio" name="codigo" class="form-control" placeholder="Ej: AC-001" required aria-label="Codigo de Accesorio">
                                <button type="submit" class="btn btn-primary d-flex align-items-center">
                                    <i class="bi bi-search me-1"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mostrar mensaje de error si el codigo no existe -->
    @if(session('errorbuscar'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin resultados de busqueda',
                text: '{{ session('errorbuscar') }}',
            });
        </script>
    @endif
@endpush
