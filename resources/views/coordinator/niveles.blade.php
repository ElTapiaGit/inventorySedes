@extends('layouts.coordinator')

@section('title', 'Búsquedas Rápidas')

@section('content')
<main class="content px-3">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">BÚSQUEDAS RÁPIDAS</h3>
        </div>

        <!-- Formulario para buscar código de equipo -->
        <form action="{{ route('niveles.buscarEquipo') }}" method="POST" class="mb-3">
            @csrf
            <div class="input-group mx-auto" style="max-width: 400px;">
                <label for="buscar_codigo_equipo" class="visually-hidden">Código de Equipo</label>
                <input type="text" id="buscar_codigo_equipo" name="codigo" class="form-control" placeholder="Código de Equipo" required aria-label="Código de Equipo">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>

        <!-- Formulario para buscar código de mobiliario -->
        <form action="{{ route('niveles.buscarMobiliario') }}" method="POST" class="mb-3">
            @csrf
            <div class="input-group mx-auto" style="max-width: 400px;">
                <label for="buscar_codigo_mobiliario" class="visually-hidden">Código de Mobiliario</label>
                <input type="text" id="buscar_codigo_mobiliario" name="codigo" class="form-control" placeholder="Código de Mobiliario" required aria-label="Código de Mobiliario">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>

        <!-- Formulario para buscar código de material -->
        <form action="{{ route('niveles.buscarMaterial') }}" method="POST" class="mb-3">
            @csrf
            <div class="input-group mx-auto" style="max-width: 400px;">
                <label for="buscar_codigo_material" class="visually-hidden">Código de Material</label>
                <input type="text" id="buscar_codigo_material" name="codigo" class="form-control" placeholder="Código de Material" required aria-label="Código de Material">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>

        <!-- Formulario para buscar código de accesorio -->
        <form action="{{ route('niveles.buscarAccesorio') }}" method="POST" class="mb-3">
            @csrf
            <div class="input-group mx-auto" style="max-width: 400px;">
                <label for="buscar_codigo_accesorio" class="visually-hidden">Código de Accesorio</label>
                <input type="text" id="buscar_codigo_accesorio" name="codigo" class="form-control" placeholder="Código de Accesorio" required aria-label="Código de Accesorio">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mostrar mensaje de error si el código no existe -->
    @if(session('errorbuscar'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin resultados de búsqueda',
                text: '{{ session('errorbuscar') }}',
            });
        </script>
    @endif
@endpush
