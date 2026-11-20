@extends('layouts.admin')

@section('title', 'Detalles del Accesorio: ' . $accesorio->cod_accesorio)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/accesorios.css') }}">
@endpush

@php
    $estado = $accesorio->estado_acce ?? 'No definido';
    $rutaVolver = url()->previous() != url()->current() 
        ? url()->previous() 
        : route('accesoriosadmin.unicos');
@endphp

@section('content')
<main class="content px-3 py-3 pagAccesorio">
    <div class="container-fluid">
        <!-- Cabecera de Ficha -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="mb-0 fw-bold fs-5 fs-sm-4" style="color: #0E0D39;">
                    <i class="bi bi-plug me-2 text-primary"></i>Ficha Tecnica: {{ $accesorio->cod_accesorio }}
                </h4>
                <span class="badge {{ inventario_estado_badge($estado) }} px-3 py-2 fs-6 rounded-pill">
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
                            <img src="{{ inventario_img_url($accesorio->foto->ruta_foto ?? null) }}"
                                 alt="{{ $accesorio->nombre_acce }}"
                                 class="img-fluid rounded-2 shadow-sm"
                                 style="max-height: 290px; object-fit: contain;"
                                 onerror="this.onerror=null;this.src='{{ asset('img/placeholder-articulo.svg') }}';">
                        </div>
                        <div class="small text-muted mb-1 d-flex flex-wrap justify-content-between gap-2 px-2">
                            <span><i class="bi bi-hash me-1"></i> Codigo: <strong>{{ $accesorio->cod_accesorio }}</strong></span>
                            <span><i class="bi bi-tag me-1"></i> Accesorio</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Especificaciones Tecnicas -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-card-checklist me-2 text-primary"></i> Informacion del Accesorio
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Nombre del Accesorio</label>
                                <div class="fw-bold text-dark fs-6">{{ $accesorio->nombre_acce }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Ubicacion</label>
                                <div class="text-dark">{{ $accesorio->ubicacion ?: 'No especificada' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Vida Util</label>
                                <div class="text-dark">{{ $accesorio->vida_util ?: 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Fecha de Registro</label>
                                <div class="text-dark">{{ $accesorio->fch_registro_acce ? \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') : 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Estado</label>
                                <div><span class="badge {{ inventario_estado_badge($estado) }}">{{ $estado }}</span></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Descripcion</label>
                                <div class="p-3 bg-light rounded-2 text-secondary small" style="white-space: pre-line;">{{ $accesorio->descripcion_acce ?: 'Sin descripcion registrada.' }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Observaciones</label>
                                <div class="p-3 bg-light rounded-2 text-secondary small" style="white-space: pre-line;">{{ $accesorio->observacion_ace ?: 'Sin observaciones registradas.' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informacion del Equipo Asociado (si existe) -->
        @if($equipo)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                                <i class="bi bi-pc-display me-2 text-primary"></i> Equipo Asociado
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label text-muted small mb-1 fw-semibold">Equipo</label>
                                            <div class="fw-bold text-dark fs-6">{{ $equipo->nombre_equi }}</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label text-muted small mb-1 fw-semibold">Codigo</label>
                                            <div class="fw-semibold text-dark">{{ $equipo->cod_equipo }}</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label text-muted small mb-1 fw-semibold">Marca / Modelo</label>
                                            <div class="text-dark">{{ $equipo->marca ?: 'N/A' }} - {{ $equipo->modelo ?: 'N/A' }}</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label text-muted small mb-1 fw-semibold">Estado</label>
                                            <div><span class="badge {{ inventario_estado_badge($equipo->estado_equi) }}">{{ $equipo->estado_equi }}</span></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1 fw-semibold">Descripcion</label>
                                            <div class="p-2 bg-light rounded-2 text-secondary small">{{ $equipo->descripcion_equi ?: 'Sin descripcion registrada.' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="border rounded-3 p-2 bg-light d-flex align-items-center justify-content-center" style="min-height: 180px; max-height: 220px; overflow: hidden;">
                                        <img src="{{ inventario_img_url($equipo->foto->ruta_foto ?? null) }}" 
                                             class="img-fluid rounded-2 shadow-sm" 
                                             alt="{{ $equipo->nombre_equi }}"
                                             style="max-height: 190px; object-fit: contain;"
                                             onerror="this.onerror=null;this.src='{{ asset('img/placeholder-articulo.svg') }}';">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Historial del Accesorio -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-clock-history me-2 text-primary"></i> Historial de Cambios del Accesorio
                        </h5>
                        <span class="badge bg-secondary rounded-pill">{{ count($historial ?? []) }}</span>
                    </div>
                    <div class="card-body p-0">
                        @if($historial->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-info-circle fs-4 d-block mb-2 text-secondary"></i>
                                No hay registros de historial de cambios para este accesorio.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 table-ulat">
                                    <thead>
                                        <tr>
                                            <th>Fecha de Cambio</th>
                                            <th>Motivo del Cambio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($historial as $registro)
                                            <tr>
                                                <td class="fw-semibold text-dark text-center" style="width: 200px;">
                                                    <i class="bi bi-calendar-event me-1 text-primary"></i>
                                                    {{ \Carbon\Carbon::parse($registro->fch_cambio)->format('d/m/Y') }}
                                                </td>
                                                <td>{{ $registro->motivo_cambio }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
