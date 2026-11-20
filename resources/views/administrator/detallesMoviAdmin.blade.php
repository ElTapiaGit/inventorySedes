@extends('layouts.admin')

@section('title', 'Detalles Uso Ambiente')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/movimiento.css') }}">
@endpush

@php
    $finalizado = (bool) $finalUso;
    $rutaVolver = url()->previous() != url()->current() 
        ? url()->previous() 
        : route('movimientosAdmin.index');
@endphp

@section('content')
<main class="content px-3 py-3 pagdetalles">
    <div class="container-fluid">
        <!-- Cabecera de Ficha -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="mb-0 fw-bold fs-5 fs-sm-4" style="color: #0E0D39;">
                    <i class="bi bi-door-open me-2 text-primary"></i>Uso de Ambiente: {{ $movimiento->ambiente->nombre ?? 'N/A' }}
                </h4>
                @if($finalizado)
                    <span class="badge bg-success px-3 py-2 fs-6 rounded-pill">
                        <i class="bi bi-check-circle me-1"></i>Finalizado
                    </span>
                @else
                    <span class="badge bg-info text-dark px-3 py-2 fs-6 rounded-pill">
                        <i class="bi bi-hourglass-split me-1"></i>En Curso
                    </span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ $rutaVolver }}" class="btn btn-outline-secondary d-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <!-- Informacion General del Uso -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                    <i class="bi bi-info-circle me-2 text-primary"></i> Informacion General del Movimiento
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small mb-1 fw-semibold">Ambiente</label>
                        <div class="fw-bold text-dark fs-6">{{ $movimiento->ambiente->nombre ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small mb-1 fw-semibold">Usuario / Docente</label>
                        <div class="fw-bold text-dark fs-6">{{ $movimiento->usuario->nombreCompleto ?? ($movimiento->usuario->nombre ?? 'N/A') }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small mb-1 fw-semibold">Semestre</label>
                        <div class="text-dark">{{ $movimiento->semestre ?: 'N/A' }}</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small mb-1 fw-semibold">Descripcion / Motivo de Uso</label>
                        <div class="p-3 bg-light rounded-2 text-secondary" style="white-space: pre-line;">
                            {{ $movimiento->descripcion ?: 'Sin descripcion registrada.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Entrega y Recepcion -->
        <div class="row g-4">
            <!-- Columna 1: Inicio / Entrega -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-box-arrow-in-right me-2 text-success"></i> Inicio / Entrega de Ambiente
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Fecha de Uso</label>
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-calendar-event me-1 text-primary"></i>
                                    {{ $movimiento->fch_uso ? \Carbon\Carbon::parse($movimiento->fch_uso)->format('d/m/Y') : 'N/A' }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Hora de Uso</label>
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-clock me-1 text-primary"></i>
                                    {{ $movimiento->hora_uso ?: 'N/A' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Encargado de Turno</label>
                                <div class="text-dark fw-semibold">
                                    <i class="bi bi-person-badge me-1"></i>
                                    {{ $personal_inicio ? trim("{$personal_inicio->nombre} {$personal_inicio->ap_paterno} {$personal_inicio->ap_materno}") : 'No asignado' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Contacto</label>
                                <div class="text-secondary">
                                    <i class="bi bi-telephone me-1"></i>
                                    {{ $personal_inicio->celular ?? 'Sin contacto' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna 2: Cierre / Devolucion -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-box-arrow-left me-2 text-primary"></i> Finalizacion / Cierre de Ambiente
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($finalizado)
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Fecha de Fin</label>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-calendar-check me-1 text-success"></i>
                                        {{ $finalUso->fch_fin ? \Carbon\Carbon::parse($finalUso->fch_fin)->format('d/m/Y') : 'N/A' }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Hora de Fin</label>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-clock me-1 text-success"></i>
                                        {{ $finalUso->hora_fin ?: 'N/A' }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Encargado de Cierre</label>
                                    <div class="text-dark fw-semibold">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ $finalUso->personal ? trim("{$finalUso->personal->nombre} {$finalUso->personal->ap_paterno} {$finalUso->personal->ap_materno}") : 'No asignado' }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Contacto</label>
                                    <div class="text-secondary">
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $finalUso->personal->celular ?? 'Sin contacto' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-0">
                                <i class="bi bi-clock-history fs-4 me-3 text-warning"></i>
                                <div>
                                    <div class="fw-semibold">Uso de ambiente en curso</div>
                                    <small class="text-muted">Aun no se ha registrado la devolucion ni finalizacion del ambiente.</small>
                                </div>
                            </div>
                        @endif
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
