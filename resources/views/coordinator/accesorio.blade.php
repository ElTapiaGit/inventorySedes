@extends('layouts.coordinator')

@section('title', 'Accesorios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/accesorio.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 py-3 pagacces">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                <i class="bi bi-plug me-2 text-primary"></i>Lista de Accesorios {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }}
            </h4>
            <!-- Botones de navegacion -->
            <div class="d-flex gap-2">
                <a href="{{ route($isClinica ? 'clinica.accesorios.equipo' : 'accesorios.equipo') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                    <i class="bi bi-pc-display me-1"></i> Equipos con Accesorios
                </a>
                <a href="{{ route($isClinica ? 'clinica.accesorios.unicos' : 'accesorios.unicos') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="bi bi-tools me-1"></i> Accesorios Unicos
                </a>
            </div>
        </div>

        <!-- Barra de herramientas y buscadores integrados -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
            <div class="row g-3 align-items-center">
                <!-- Buscador por codigo -->
                <div class="col-md-6">
                    <form method="GET" action="{{ route($isClinica ? 'clinica.accesorios.buscarCodigo' : 'accesorios.buscarCodigo') }}">
                        <label for="buscar_accesorio_codigo" class="form-label text-muted small fw-semibold">Buscar por Codigo de Accesorio</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-hash"></i></span>
                            <input type="text" id="buscar_accesorio_codigo" class="form-control" name="codigo" placeholder="Ej: AC-001" value="{{ request('codigo') }}" aria-label="Buscar por codigo">
                            <button class="btn btn-primary d-flex align-items-center" type="submit">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                            @if(request('codigo'))
                                <a href="{{ route($isClinica ? 'clinica.accesorios.index' : 'accesorios.index') }}" class="btn btn-outline-secondary" title="Limpiar busqueda">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Buscador por nombre -->
                <div class="col-md-6">
                    <form method="GET" action="{{ route($isClinica ? 'clinica.accesorios.buscarNombre' : 'accesorios.buscarNombre') }}">
                        <label for="buscar_accesorio_nombre" class="form-label text-muted small fw-semibold">Buscar por Nombre de Accesorio</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscar_accesorio_nombre" class="form-control" name="nombre" placeholder="Ej: Cable HDMI" value="{{ request('nombre') }}" aria-label="Buscar por nombre">
                            <button class="btn btn-primary d-flex align-items-center" type="submit">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                            @if(request('nombre'))
                                <a href="{{ route($isClinica ? 'clinica.accesorios.index' : 'accesorios.index') }}" class="btn btn-outline-secondary" title="Limpiar busqueda">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de accesorios -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-ulat align-middle mb-0">
                    <thead class="table-ulat-header">
                        <tr>
                            <th>Codigo</th>
                            <th>Nombre</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                            <th>Ubicacion</th>
                            <th>Registrada</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accesorios as $accesorio)
                            <tr>
                                <td class="fw-bold text-primary">{{ $accesorio->cod_accesorio }}</td>
                                <td class="fw-semibold text-dark">{{ $accesorio->nombre_acce }}</td>
                                <td class="small text-muted">{{ $accesorio->observacion_ace ?: 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="badge {{ inventario_estado_badge($accesorio->estado_acce) }}">
                                        {{ $accesorio->estado_acce ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $accesorio->ubicacion ?: 'No especificada' }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route($isClinica ? 'clinica.accesorios.show' : 'accesorios.show', $accesorio->cod_accesorio) }}" class="btn btn-outline-primary btn-sm" title="Ver ficha del accesorio">
                                        <i class="bi bi-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No se encontraron accesorios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $accesorios->links('pagination::bootstrap-5') }}
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mensaje cuando no se encuentra resultados de busqueda -->
    @if (session('errorbuscar'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Busqueda NO Encontrada',
                text: '{{ session('errorbuscar') }}'
            });
        </script>
    @endif

    <!-- Mensaje de error en la comunicacion a BD -->
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
            });
        </script>
    @endif
@endpush
