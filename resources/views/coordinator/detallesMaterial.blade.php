@extends('layouts.coordinator')

@section('title', 'Detalles de Material: ' . $material->cod_mate)

@php
    $isClinica = request()->is('*clinica*');
    $estado = $material->estado_mate ?? 'No definido';
    $estadoNorm = strtolower(trim($estado));
    $badgeClass = 'bg-secondary';
    if (in_array($estadoNorm, ['nuevo', 'bueno', 'operativo', 'funcionando'])) {
        $badgeClass = 'bg-success';
    } elseif ($estadoNorm === 'regular') {
        $badgeClass = 'bg-warning text-dark';
    } elseif (str_contains($estadoNorm, 'mantenimiento') || str_contains($estadoNorm, 'reparar')) {
        $badgeClass = 'bg-info text-dark';
    } elseif (str_contains($estadoNorm, 'descarte') || $estadoNorm === 'malo') {
        $badgeClass = 'bg-danger';
    }

    $rutaVolver = url()->previous() != url()->current() ? url()->previous() : (isset($material->AMBIENTE_id_ambiente) ? ($isClinica ? route('coordinator.clinica.materiales', ['id_ambiente' => Crypt::encryptString($material->AMBIENTE_id_ambiente)]) : route('materiales.show', ['token' => Crypt::encryptString($material->AMBIENTE_id_ambiente)])) : ($isClinica ? route('coordinator.clinica.ambientes') : route('laboratorios.index')));
@endphp

@section('content')
<main class="content px-3 py-3">
    <div class="container-fluid">
        <!-- Cabecera de Ficha -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="mb-0 fw-bold fs-5 fs-sm-4" style="color: #0E0D39;">
                    <i class="bi bi-boxes me-2 text-primary"></i>Ficha Tecnica: {{ $material->cod_mate }}
                </h4>
                <span class="badge {{ $badgeClass }} px-3 py-2 fs-6 rounded-pill">
                    <i class="bi bi-activity me-1"></i>{{ $estado }}
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ $rutaVolver }}" class="btn btn-outline-secondary d-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Columna Izquierda: Fotografia Oficial -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-image me-2 text-primary"></i> Fotografia Oficial
                        </h5>
                    </div>
                    <div class="card-body p-3 text-center">
                        <div class="border rounded-3 p-2 bg-light mb-3 d-flex align-items-center justify-content-center" style="min-height: 260px; max-height: 320px; overflow: hidden;">
                            <img src="{{ inventario_img_url($material->ruta_foto ?? null) }}"
                                 alt="{{ $material->tipo_mate }}"
                                 class="img-fluid rounded-2 shadow-sm"
                                 style="max-height: 290px; object-fit: contain;"
                                 onerror="this.onerror=null;this.src='{{ asset('img/placeholder-articulo.svg') }}';">
                        </div>
                        <div class="small text-muted mb-1 d-flex flex-wrap justify-content-between gap-2 px-2">
                            <span><i class="bi bi-hash me-1"></i> Codigo: <strong>{{ $material->cod_mate }}</strong></span>
                            <span><i class="bi bi-tag me-1"></i> Material</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Especificaciones Tecnicas -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-card-checklist me-2 text-primary"></i> Informacion del Material
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Tipo de Material</label>
                                <div class="fw-bold text-dark fs-6">{{ $material->tipo_mate }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Ambiente</label>
                                <div class="text-dark">{{ $material->nombre ?? 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Fecha de Registro</label>
                                <div class="text-dark">{{ $material->fch_registrada ? \Carbon\Carbon::parse($material->fch_registrada)->format('d/m/Y') : 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Estado</label>
                                <div><span class="badge {{ $badgeClass }}">{{ $estado }}</span></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Descripcion</label>
                                <div class="p-3 bg-light rounded-2 text-secondary small" style="white-space: pre-line;">{{ $material->descripcion_mate ?: 'Sin descripcion registrada.' }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Observaciones</label>
                                <div class="p-3 bg-light rounded-2 text-secondary small" style="white-space: pre-line;">{{ $material->observacion_mate ?: 'Sin observaciones registradas.' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
