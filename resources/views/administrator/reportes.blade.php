@extends('layouts.admin')

@section('title', 'Reportes Institucionales')

@section('content')
<main class="content px-3 py-4">
    <div class="container-fluid">
        <!-- Cabecera de la seccion -->
        <div class="mb-4">
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-bar-chart-line me-2 text-primary"></i>Modulo de Reportes
            </h2>
            <p class="text-muted small mb-0">Generacion e impresion de reportes consolidados y estadisticos del inventario universitario.</p>
        </div>

        <!-- Seccion de tarjetas de reportes -->
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-2 bg-light rounded-3 text-primary me-3">
                                    <i class="bi bi-pc-display fs-4"></i>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-0 fs-6">Equipos Adquiridos</h5>
                            </div>
                            <p class="card-text text-muted small">Reporte detallado sobre la cantidad y adquisicion de equipos informaticos y biomedicos.</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('reportes.equipos-adquiridos') }}" target="_blank" class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Generar Reporte</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-2 bg-light rounded-3 text-primary me-3">
                                    <i class="bi bi-boxes fs-4"></i>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-0 fs-6">Mobiliario y Materiales</h5>
                            </div>
                            <p class="card-text text-muted small">Reporte cuantitativo sobre el mobiliario institucional y materiales registrados.</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('reportes.mobiliarioMate') }}" target="_blank" class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Generar Reporte</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-2 bg-light rounded-3 text-primary me-3">
                                    <i class="bi bi-plug fs-4"></i>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-0 fs-6">Accesorios Adquiridos</h5>
                            </div>
                            <p class="card-text text-muted small">Consolidado sobre los accesorios adquiridos y su vinculacion general en el inventario.</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('reportes.accesorioAdquirido') }}" target="_blank" class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Generar Reporte</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-2 bg-light rounded-3 text-primary me-3">
                                    <i class="bi bi-people fs-4"></i>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-0 fs-6">Resumen de Personal</h5>
                            </div>
                            <p class="card-text text-muted small">Desglose del personal universitario por tipo de rol, carrera asignada y estado laboral.</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('reportes.personal') }}" target="_blank" class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Generar Reporte</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>  

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-2 bg-light rounded-3 text-primary me-3">
                                    <i class="bi bi-door-open fs-4"></i>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-0 fs-6">Reporte de Ambientes</h5>
                            </div>
                            <p class="card-text text-muted small">Estructura y distribucion de ambientes clasificados por sedes, edificios y niveles.</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('reportes.ambientes') }}" target="_blank" class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Generar Reporte</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-2 bg-light rounded-3 text-primary me-3">
                                    <i class="bi bi-arrow-repeat fs-4"></i>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-0 fs-6">Rotacion de Accesorios</h5>
                            </div>
                            <p class="card-text text-muted small">Detalles estadisticos sobre los accesorios con mayor frecuencia de reposicion o cambio.</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('reportes.accesoriosMasRepuesto') }}" target="_blank" class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Generar Reporte</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-2 bg-light rounded-3 text-danger me-3">
                                    <i class="bi bi-trash fs-4"></i>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-0 fs-6">Articulos Descartados</h5>
                            </div>
                            <p class="card-text text-muted small">Trazabilidad de bienes dados de baja definitiva por descarte institucional.</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('reportes.articulosDescartados') }}" target="_blank" class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Generar Reporte</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-2 bg-light rounded-3 text-primary me-3">
                                    <i class="bi bi-activity fs-4"></i>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-0 fs-6">Estado de Equipos</h5>
                            </div>
                            <p class="card-text text-muted small">Reporte por estado funcional de los equipos distribuidos en los edificios.</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('reportes.estado.equipos') }}" target="_blank" class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Generar Reporte</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
