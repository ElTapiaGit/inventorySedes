@extends('layouts.coordinator')

@section('title', 'Detalles del Prestamo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/prestamos.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
    $rutaVolver = url()->previous() != url()->current() ? url()->previous() : route($isClinica ? 'clinica.prestamos.index' : 'prestamos.index');
    $devuelto = (bool) $prestamo->fch_devolucion;
@endphp

@section('content')
<main class="content px-3 py-3 pagDetallesprestamo">
    <div class="container-fluid">
        <!-- Cabecera de Ficha -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                    <i class="bi bi-arrow-left-right me-2 text-primary"></i>Ficha de Prestamo: {{ $prestamo->nombre_solicitante }}
                </h4>
                @if($devuelto)
                    <span class="badge bg-success px-3 py-2 fs-6 rounded-pill">
                        <i class="bi bi-check-circle me-1"></i>Devuelto
                    </span>
                @else
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
                        <i class="bi bi-clock-history me-1"></i>Pendiente de Devolucion
                    </span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ $rutaVolver }}" class="btn btn-outline-secondary d-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <!-- Grid de Prestamo y Devolucion -->
        <div class="row g-4 mb-4">
            <!-- Columna 1: Informacion del Prestamo -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-person-up me-2 text-primary"></i> Informacion del Prestamo
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Prestatario / Solicitante</label>
                                <div class="fw-bold text-dark fs-6">{{ $prestamo->nombre_solicitante }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Fecha Prestada</label>
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-calendar-event me-1 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Hora de Prestamo</label>
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-clock me-1 text-primary"></i>
                                    {{ $prestamo->hora_prestamo }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Encargado Responsable</label>
                                <div class="text-secondary fw-semibold">
                                    <i class="bi bi-person-badge me-1"></i>{{ $prestamo->nombre_encargado }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Descripcion del Prestamo</label>
                                <div class="p-3 bg-light rounded-2 text-secondary small" style="white-space: pre-line;">
                                    {{ $prestamo->descripcion_prestamo ?: 'Sin descripcion registrada.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna 2: Detalles de Devolucion -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-person-down me-2 text-success"></i> Detalles de Devolucion
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($devuelto)
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Fecha de Devolucion</label>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-calendar-check me-1 text-success"></i>
                                        {{ \Carbon\Carbon::parse($prestamo->fch_devolucion)->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Hora de Devolucion</label>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-clock-history me-1 text-success"></i>
                                        {{ $prestamo->hora_devolucion }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Encargado de Recibir</label>
                                    <div class="text-secondary fw-semibold">
                                        <i class="bi bi-person-check me-1"></i>
                                        {{ $prestamo->nombre_encargado_devolucion ?? $prestamo->nombre_encargado ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Descripcion de Devolucion</label>
                                    <div class="p-3 bg-light rounded-2 text-secondary small" style="white-space: pre-line;">
                                        {{ $prestamo->devolucion_descripcion ?: 'Sin observaciones de devolucion registradas.' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning d-flex align-items-center mb-0 h-100" role="alert">
                                <i class="bi bi-clock-history fs-3 me-3 text-warning"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">Devolucion Pendiente</h6>
                                    <p class="mb-0 small text-secondary">Los articulos prestados aun no han sido devueltos. Una vez que el prestatario entregue los equipos, el encargado completara la fecha y observaciones de devolucion.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Articulos Prestados -->
        <div class="row justify-content-center mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-boxes me-2 text-primary"></i> Articulos Prestados
                        </h5>
                        <span class="badge bg-secondary rounded-pill">{{ isset($detalle_prestamos) ? count($detalle_prestamos) : 0 }}</span>
                    </div>
                    @if(isset($detalle_prestamos) && count($detalle_prestamos) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="tablaCabecera">
                                    <tr>
                                        <th>Codigo</th>
                                        <th>Nombre</th>
                                        <th>Observaciones</th>
                                        <th>Estado</th>
                                        <th>Ambiente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detalle_prestamos as $detalle)
                                        <tr>
                                            <td class="fw-semibold text-primary">{{ $detalle->cod_equipo }}</td>
                                            <td class="fw-semibold text-dark">{{ $detalle->nombre_equipo }}</td>
                                            <td>{{ $detalle->observacion_detalle ?: 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $stEq = strtolower(trim($detalle->estado_equipo ?? ''));
                                                    $bgEq = 'bg-secondary';
                                                    if (in_array($stEq, ['bueno', 'operativo', 'nuevo', 'funcionando'])) {
                                                        $bgEq = 'bg-success';
                                                    } elseif ($stEq === 'regular') {
                                                        $bgEq = 'bg-warning text-dark';
                                                    } elseif (str_contains($stEq, 'mantenimiento') || str_contains($stEq, 'reparar')) {
                                                        $bgEq = 'bg-info text-dark';
                                                    } elseif (str_contains($stEq, 'descarte') || $stEq === 'malo') {
                                                        $bgEq = 'bg-danger';
                                                    }
                                                @endphp
                                                <span class="badge {{ $bgEq }}">{{ $detalle->estado_equipo }}</span>
                                            </td>
                                            <td>{{ $detalle->ambiente_equipo ?: 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <div class="alert alert-secondary mb-0">
                                <i class="bi bi-info-circle me-1"></i> No hay detalles registrados de los articulos de este prestamo.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
