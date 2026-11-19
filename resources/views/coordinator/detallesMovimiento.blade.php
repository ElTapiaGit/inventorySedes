@extends('layouts.coordinator')

@section('title', 'Detalles de Movimiento')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/movimientos.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
    $rutaVolver = url()->previous() != url()->current() ? url()->previous() : route($isClinica ? 'clinica.movimientos.index' : 'movimientos.index');
    $finalizado = (bool) $personal_fin;
@endphp

@section('content')
<main class="content px-3 py-3 pagdetalles">
    <div class="container-fluid">
        <!-- Cabecera de Ficha -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                    <i class="bi bi-door-open me-2 text-primary"></i>Uso de Ambiente: {{ $movimiento->nombre_ambiente }}
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
                        <div class="fw-bold text-dark fs-6">{{ $movimiento->nombre_ambiente }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small mb-1 fw-semibold">Docente / Usuario</label>
                        <div class="fw-bold text-dark fs-6">{{ $movimiento->nombre_usuario }}</div>
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
                                    {{ \Carbon\Carbon::parse($movimiento->fch_uso)->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Hora de Uso</label>
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-clock me-1 text-primary"></i>
                                    {{ $movimiento->hora_uso }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Encargado de Turno</label>
                                <div class="text-dark fw-semibold">
                                    <i class="bi bi-person-badge me-1 text-secondary"></i>
                                    {{ $personal_inicio->nombre }} {{ $personal_inicio->ap_paterno }} {{ $personal_inicio->ap_materno }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Celular del Encargado</label>
                                <div class="text-secondary">
                                    <i class="bi bi-telephone me-1"></i>
                                    {{ $personal_inicio->celular ?: 'No registrado' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna 2: Finalizacion / Recepcion -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-box-arrow-left me-2 text-primary"></i> Finalizacion / Recepcion
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($finalizado)
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Fecha de Fin</label>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-calendar-check me-1 text-success"></i>
                                        {{ \Carbon\Carbon::parse($movimiento->fch_fin)->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Hora de Fin</label>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-clock-history me-1 text-success"></i>
                                        {{ $movimiento->hora_fin }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Encargado de Recepcion</label>
                                    <div class="text-dark fw-semibold">
                                        <i class="bi bi-person-check me-1 text-secondary"></i>
                                        {{ $personal_fin->nombre }} {{ $personal_fin->ap_paterno }} {{ $personal_fin->ap_materno }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small mb-1 fw-semibold">Celular del Encargado</label>
                                    <div class="text-secondary">
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $personal_fin->celular ?: 'No registrado' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info d-flex align-items-center mb-0 h-100" role="alert">
                                <i class="bi bi-hourglass-split fs-3 me-3 text-info"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">Aun no finaliza el Uso del Ambiente</h6>
                                    <p class="mb-0 small text-secondary">El ambiente se encuentra actualmente ocupado. Los datos de recepcion se registraran una vez concluido el turno docente.</p>
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
