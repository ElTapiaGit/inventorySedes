@extends('layouts.admin')

@section('title', 'Articulos Descartados')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/descarte.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagDescarte">
    <div class="container-fluid">
        <!-- Cabecera de la seccion -->
        <div class="mb-4">
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-trash me-2 text-danger"></i>Articulos Descartados
            </h2>
            <p class="text-muted small mb-0">Registro y trazabilidad de bienes dados de baja definitiva por descarte institucional.</p>
        </div>

        <!-- Filtros responsivos (row g-2) -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
            <form action="{{ route('descartes.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="descarteCodigo" class="form-label small fw-semibold text-muted mb-1">Codigo del Articulo:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-hash"></i></span>
                            <input type="text" id="descarteCodigo" name="codigo" class="form-control" placeholder="Codigo" value="{{ request('codigo') }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="descarteNombre" class="form-label small fw-semibold text-muted mb-1">Nombre del Articulo:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-search"></i></span>
                            <input type="text" id="descarteNombre" name="nombre" class="form-control" placeholder="Nombre" value="{{ request('nombre') }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label for="descarteFchInicio" class="form-label small fw-semibold text-muted mb-1">Fecha Desde:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-calendar"></i></span>
                            <input type="date" id="descarteFchInicio" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label for="descarteFchFin" class="form-label small fw-semibold text-muted mb-1">Fecha Hasta:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-calendar-check"></i></span>
                            <input type="date" id="descarteFchFin" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                        </div>
                    </div>
                    <div class="col-12 col-lg-2">
                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-funnel"></i>
                            <span>Filtrar</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla principal de Descartes -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                    <i class="bi bi-list-check me-2 text-primary"></i>Listado de Bajas y Descartes
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0" id="tablaDescartes">
                        <thead class="table-ulat-header">
                            <tr>
                                <th>Codigo</th>
                                <th>Nombre del Articulo</th>
                                <th>Descripcion del Descarte</th>
                                <th>Orden de Descarte</th>
                                <th>Fecha Descarte</th>
                                <th>Personal Responsable</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($descartes as $descarte)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $descarte->codigo }}</td>
                                    <td>{{ $descarte->nombre }}</td>
                                    <td>{{ $descarte->descrpcion_descarte ?: '-' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $descarte->orden_desacarte ?: 'S/N' }}
                                        </span>
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-x me-1 text-danger"></i>
                                        {{ \Carbon\Carbon::parse($descarte->fch_descarte)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <i class="bi bi-person me-1 text-secondary"></i>
                                        {{ $descarte->personal ? trim("{$descarte->personal->nombre} {$descarte->personal->ap_paterno}") : 'No asignado' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>No hay registros de articulos descartados que coincidan con la busqueda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-center">
                {{ $descartes->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</main>
@endsection
