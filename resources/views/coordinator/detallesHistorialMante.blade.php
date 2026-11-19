@extends('layouts.coordinator')

@section('title', 'Detalles de Mantenimiento')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/mantenimiento.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
    $rutaVolver = url()->previous() != url()->current() ? url()->previous() : route($isClinica ? 'clinica.historialmantenimientos.index' : 'historialmantenimientos.index');
    $finalizado = ($mantenimiento->informe_final !== null && $mantenimiento->fch_final !== null);
@endphp

@section('content')
<main class="content px-3 py-3 detallesMantenimiento">
    <div class="container-fluid">
        <!-- Cabecera de Ficha -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                    <i class="bi bi-wrench me-2 text-primary"></i>Ficha de Mantenimiento: {{ $articulo->codigo }}
                </h4>
                @if($finalizado)
                    <span class="badge bg-success px-3 py-2 fs-6 rounded-pill">
                        <i class="bi bi-check-circle me-1"></i>Finalizado
                    </span>
                @else
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
                        <i class="bi bi-clock-history me-1"></i>En Proceso
                    </span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ $rutaVolver }}" class="btn btn-outline-secondary d-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Columna Izquierda: Fotografia y Personas -->
            <div class="col-lg-4">
                <!-- Tarjeta de Foto y Articulo -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-image me-2 text-primary"></i> Fotografia del Articulo
                        </h5>
                    </div>
                    <div class="card-body p-3 text-center">
                        <div class="border rounded-3 p-2 bg-light mb-3 d-flex align-items-center justify-content-center" style="min-height: 240px; max-height: 300px; overflow: hidden;">
                            <img src="{{ inventario_img_url($mantenimiento->ruta_foto ?? null) }}"
                                 alt="{{ $articulo->nombre }}"
                                 class="img-fluid rounded-2 shadow-sm"
                                 style="max-height: 270px; object-fit: contain;"
                                 onerror="this.onerror=null;this.src='{{ asset('img/placeholder-articulo.svg') }}';">
                        </div>
                        <div class="small text-muted d-flex justify-content-between px-2">
                            <span>Codigo: <strong>{{ $articulo->codigo }}</strong></span>
                            <span>Articulo: <strong>{{ $articulo->nombre }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta del Tecnico -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-person-gear me-2 text-primary"></i> Datos del Tecnico
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <p class="mb-2"><strong>Nombre:</strong> <span class="text-secondary">{{ $mantenimiento->nombre ?: 'N/A' }}</span></p>
                        <p class="mb-2"><strong>Celular:</strong> <span class="text-secondary">{{ $mantenimiento->celular ?: 'N/A' }}</span></p>
                        <p class="mb-0"><strong>Direccion:</strong> <span class="text-secondary">{{ $mantenimiento->direccion ?: 'N/A' }}</span></p>
                    </div>
                </div>

                <!-- Tarjeta del Personal Encargado -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-person-badge me-2 text-primary"></i> Personal Encargado
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <p class="mb-2"><strong>Nombre:</strong> <span class="text-secondary">{{ $personal->nombre_completo ?? ($personal->nombre . ' ' . $personal->ap_paterno) }}</span></p>
                        <p class="mb-0"><strong>Celular:</strong> <span class="text-secondary">{{ $personal->celular ?: 'N/A' }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Informes del Mantenimiento -->
            <div class="col-lg-8">
                <!-- Informacion de Inicio -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-clipboard-pulse me-2 text-primary"></i> Apertura del Mantenimiento
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1 fw-semibold">Fecha de Inicio</label>
                            <div class="fw-bold text-dark fs-6">
                                <i class="bi bi-calendar-event me-2 text-primary"></i>{{ \Carbon\Carbon::parse($mantenimiento->fch_inicio)->format('d/m/Y') }}
                            </div>
                        </div>
                        <div>
                            <label class="form-label text-muted small mb-1 fw-semibold">Informe Inicial / Diagnostico</label>
                            <div class="p-3 bg-light rounded-2 text-secondary" style="white-space: pre-line;">
                                {{ $mantenimiento->informe_inicial ?: 'Sin informe inicial registrado.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informacion de Cierre -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-clipboard-check me-2 text-primary"></i> Cierre y Finalizacion
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($finalizado)
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1 fw-semibold">Fecha de Finalizacion</label>
                                <div class="fw-bold text-dark fs-6 text-success">
                                    <i class="bi bi-calendar-check me-2 text-success"></i>{{ \Carbon\Carbon::parse($mantenimiento->fch_final)->format('d/m/Y') }}
                                </div>
                            </div>
                            <div>
                                <label class="form-label text-muted small mb-1 fw-semibold">Informe Final / Trabajo Realizado</label>
                                <div class="p-3 bg-light rounded-2 text-secondary" style="white-space: pre-line;">
                                    {{ $mantenimiento->informe_final }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                                <i class="bi bi-clock-history fs-4 me-3 text-warning"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">Mantenimiento en Proceso</h6>
                                    <p class="mb-0 small">Aun no se ha finalizado este mantenimiento. El informe final estara disponible una vez que el tecnico concluya la reparacion.</p>
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
