@extends('layouts.coordinator')

@section('title', 'Reportes')

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 py-3">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Opciones de Reportes {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }}
            </h4>
        </div>

        <div class="row g-4">
            <!-- Reporte 1: Equipos -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                        <i class="bi bi-pc-display fs-5 text-primary me-2"></i>
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">Equipos {{ isset($carreraCoordinada) ? 'de ' . $carreraCoordinada : '' }}</h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <p class="card-text text-muted small mb-4">Reporte institucional sobre la cantidad y especificacion tecnica de los equipos registrados.</p>
                        <a href="{{ route($isClinica ? 'reporte.clinica.equipos' : 'reporte.equiposOdont') }}" target="_blank" class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Generar Reporte PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte 2: Muebles y Materiales -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                        <i class="bi bi-chair fs-5 text-primary me-2"></i>
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">Muebles y Materiales</h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <p class="card-text text-muted small mb-4">Lista detallada de mobiliarios y materiales por ambiente y piso.</p>
                        <a href="{{ route($isClinica ? 'reporte.clinica.contenido' : 'reporte.ambiente.odontologia') }}" target="_blank" class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Generar Reporte PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte 3: Estado de Equipos -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                        <i class="bi bi-activity fs-5 text-primary me-2"></i>
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">Estado de Equipos</h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <p class="card-text text-muted small mb-4">Clasificacion de equipos segun su estado operativo (bueno, regular, en mantenimiento).</p>
                        <a href="{{ route($isClinica ? 'report.estado.equiposClinica' : 'report.estado.equipos') }}" target="_blank" class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Generar Reporte PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte 4: Equipos Descartados -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                        <i class="bi bi-trash fs-5 text-primary me-2"></i>
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">Equipos Descartados</h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <p class="card-text text-muted small mb-4">Detalles historicos de equipos dados de baja o descartados por deterioro.</p>
                        <a href="{{ route('reporte.equiposDescartadOdont') }}" target="_blank" class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Generar Reporte PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte 5: Accesorios Reemplazados -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                        <i class="bi bi-tools fs-5 text-primary me-2"></i>
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">Accesorios Reemplazados</h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <p class="card-text text-muted small mb-4">Registro de accesorios con mayor frecuencia de reposicion y sustitucion.</p>
                        <a href="{{ route('reporte.accesorioCambiadOdont') }}" target="_blank" class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Generar Reporte PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
