@extends('layouts.coordinator')

@section('title', 'Historial de Mantenimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/mantenimiento.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 py-3 pagHistorial">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional con boton volver -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                <i class="bi bi-clock-history me-2 text-primary"></i>Historial de Mantenimientos {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }}
            </h4>
            <a href="{{ route($isClinica ? 'clinica.mantenimiento.index' : 'mantenimiento.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>

        <!-- Barra de filtros y busqueda -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
            <form action="{{ route($isClinica ? 'clinica.historialmantenimientos.index' : 'historialmantenimientos.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="cod_equipo" class="form-label text-muted small fw-semibold">Codigo del Articulo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-hash"></i></span>
                            <input type="text" name="cod_equipo" id="cod_equipo" class="form-control" placeholder="Codigo articulo..." value="{{ request('cod_equipo') ?? request('cod_articulo') }}" aria-label="Codigo Articulo">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="fch_inicio" class="form-label text-muted small fw-semibold">Fecha Inicio</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="fch_inicio" id="fch_inicio" class="form-control" value="{{ request('fch_inicio') }}" aria-label="Fecha Inicio">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="fch_fin" class="form-label text-muted small fw-semibold">Fecha Fin</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="fch_fin" id="fch_fin" class="form-control" value="{{ request('fch_fin') }}" aria-label="Fecha Fin">
                        </div>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center flex-grow-1 justify-content-center">
                            <i class="bi bi-search me-1"></i> Buscar
                        </button>
                        @if(request('cod_equipo') || request('cod_articulo') || request('fch_inicio') || request('fch_fin'))
                            <a href="{{ route($isClinica ? 'clinica.historialmantenimientos.index' : 'historialmantenimientos.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        @if($mantenimientos->isEmpty())
            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i> No se encontraron registros en el historial de mantenimientos.
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                        <i class="bi bi-list-check me-2 text-primary"></i> Registros de Mantenimiento
                    </h5>
                    <span class="badge bg-secondary rounded-pill">{{ $mantenimientos->total() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th>Codigo</th>
                                <th>Nombre Articulo</th>
                                <th>Tecnico Responsable</th>
                                <th>Fecha Inicio</th>
                                <th>Ambiente</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mantenimientos as $mantenimiento)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $mantenimiento->cod_articulo }}</td>
                                    <td class="fw-semibold text-dark">{{ $mantenimiento->nombre_articulo }}</td>
                                    <td>{{ $mantenimiento->nombre_tecnico ?: 'N/A' }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($mantenimiento->fch_inicio)->format('d/m/Y') }}</td>
                                    <td>{{ $mantenimiento->nombre_ambiente ?: 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route($isClinica ? 'clinica.detallesmantenimientos.mostrar' : 'detallesmantenimientos.mostrar', ['id' => encrypt($mantenimiento->id_mantenimiento_ini)]) }}" class="btn btn-outline-primary btn-sm" title="Ver ficha del mantenimiento">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $mantenimientos->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mensaje sin resultado de busqueda -->
    @if(session('errorbuscar'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin Resultados',
                text: '{{ session('errorbuscar') }}',
            });
        </script>
    @endif
@endpush
