@extends('layouts.coordinator')

@section('title', 'Mantenimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/mantenimiento.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 py-3 pagMantenimiento">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional con barra de acciones -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                <i class="bi bi-wrench me-2 text-primary"></i>Equipos para Mantenimiento {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }}
            </h4>
            <div class="d-flex gap-2">
                <a href="{{ route($isClinica ? 'clinica.historialmantenimientos.index' : 'historialmantenimientos.index') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                    <i class="bi bi-clock-history me-1"></i> Historial de Mantenimiento
                </a>
                <a href="{{ route('print.equipos') }}" target="_blank" class="btn btn-outline-danger btn-sm d-flex align-items-center">
                    <i class="bi bi-printer me-1"></i> Imprimir Reporte
                </a>
            </div>
        </div>

        @if($equipos->isEmpty())
            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i> No hay equipos registrados para mantenimiento en este momento.
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                        <i class="bi bi-list-check me-2 text-primary"></i> Equipos que Requieren Mantenimiento
                    </h5>
                    <span class="badge bg-secondary rounded-pill">{{ $equipos->total() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Observaciones</th>
                                <th>Estado</th>
                                <th>Ambiente</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipos as $equipo)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $equipo->cod_equipo ?? $equipo->Cod_equipo }}</td>
                                    <td class="fw-semibold text-dark">{{ $equipo->nombre_equi }}</td>
                                    <td class="small text-muted">{{ $equipo->observaciones_equi ?: 'Sin observaciones' }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ inventario_estado_badge($equipo->estado_equi) }}">
                                            {{ $equipo->estado_equi ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $equipo->nombre_ambiente ?: 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $equipos->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mensaje cuando no hay equipos para mantenimiento -->
    @if($equipos->isEmpty())
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin equipos para mantenimiento',
                text: 'No hay equipos registrados para mantenimiento.',
                confirmButtonText: 'Entendido'
            });
        </script>
    @endif

    <!-- Mensaje de error de comunicacion con la BD -->
    @if(session('errordata'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errordata') }}',
            });
        </script>
    @endif
@endpush
