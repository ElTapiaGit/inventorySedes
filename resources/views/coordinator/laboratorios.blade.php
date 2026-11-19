@extends('layouts.coordinator')

@section('title', 'Laboratorios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/laboratorios.css') }}">
@endpush

@section('content')
<main class="content px-3 py-3">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                <i class="bi bi-funnel me-2 text-primary"></i>Laboratorios {{ isset($carreraCoordinada) ? 'de ' . $carreraCoordinada : (isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '') }}
            </h4>
        </div>

        <!-- Tarjeta de Busqueda -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card border-0 shadow-sm rounded-3 p-4">
                    <form class="d-flex flex-column gap-3" action="{{ route('laboratorios.buscar') }}" method="POST">
                        @csrf
                        <div>
                            <label for="Laboratorio" class="form-label text-muted small fw-semibold">Buscar Laboratorio por Nombre</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i class="bi bi-search"></i></span>
                                <input list="laboratorios" name="Laboratorio" id="Laboratorio" class="form-control" required placeholder="Seleccione o escriba el nombre del laboratorio" aria-label="Buscar laboratorio">
                            </div>
                            <datalist id="laboratorios">
                                @foreach($laboratorios as $laboratorio)
                                    <option value="{{ $laboratorio->nombre }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mensaje sin resultados de busqueda -->
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
