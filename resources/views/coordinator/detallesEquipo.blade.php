@extends('layouts.coordinator')

@section('title', 'Detalles de Equipo: ' . $equipo->cod_equipo)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/detallesequipo.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
    $estado = $equipo->estado_equi ?? 'No definido';
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

    $rutaVolver = url()->previous() != url()->current() ? url()->previous() : (isset($equipo->AMBIENTE_id_ambiente) ? ($isClinica ? route('coordinator.clinica.equipos', ['id_ambiente' => Crypt::encryptString($equipo->AMBIENTE_id_ambiente)]) : route('equipos.show', ['token' => Crypt::encryptString($equipo->AMBIENTE_id_ambiente)])) : ($isClinica ? route('coordinator.clinica.ambientes') : route('laboratorios.index')));
@endphp

@section('content')
<main class="content px-3 py-3 detallesEquipo">
    <div class="container-fluid">
        <!-- Cabecera de Ficha -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="mb-0 fw-bold fs-5 fs-sm-4" style="color: #0E0D39;">
                    <i class="bi bi-pc-display me-2 text-primary"></i>Ficha Tecnica: {{ $equipo->cod_equipo }}
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
                            <img src="{{ inventario_img_url($equipo->ruta_foto ?? null) }}"
                                 alt="{{ $equipo->nombre_equi }}"
                                 class="img-fluid rounded-2 shadow-sm"
                                 style="max-height: 290px; object-fit: contain;"
                                 onerror="this.onerror=null;this.src='{{ asset('img/placeholder-articulo.svg') }}';">
                        </div>
                        <div class="small text-muted mb-1 d-flex flex-wrap justify-content-between gap-2 px-2">
                            <span><i class="bi bi-hash me-1"></i> Codigo: <strong>{{ $equipo->cod_equipo }}</strong></span>
                            <span><i class="bi bi-tag me-1"></i> Equipo</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Especificaciones Tecnicas -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-card-checklist me-2 text-primary"></i> Informacion del Equipo
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Nombre del Equipo</label>
                                <div class="fw-bold text-dark fs-6">{{ $equipo->nombre_equi }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Ambiente</label>
                                <div class="text-dark">{{ $equipo->nombre ?? 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Marca</label>
                                <div class="text-dark">{{ $equipo->marca ?: 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Modelo</label>
                                <div class="text-dark">{{ $equipo->modelo ?: 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Empotrado</label>
                                <div class="text-dark">{{ $equipo->empotrado ? 'Si' : 'No' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Vida Util</label>
                                <div class="text-dark">{{ $equipo->vida_util ?: 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Fecha de Registro</label>
                                <div class="text-dark">{{ $equipo->fch_registro ? \Carbon\Carbon::parse($equipo->fch_registro)->format('d/m/Y') : 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Estado</label>
                                <div><span class="badge {{ $badgeClass }}">{{ $estado }}</span></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Descripcion</label>
                                <div class="p-3 bg-light rounded-2 text-secondary small" style="white-space: pre-line;">{{ $equipo->descripcion_equi ?: 'Sin descripcion registrada.' }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Observaciones</label>
                                <div class="p-3 bg-light rounded-2 text-secondary small" style="white-space: pre-line;">{{ $equipo->observaciones_equi ?: 'Sin observaciones registradas.' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Componentes del equipo -->
        <div class="row justify-content-center mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-cpu me-2 text-primary"></i> Componentes del Equipo
                        </h5>
                        <span class="badge bg-secondary rounded-pill">{{ count($componentes ?? []) }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="headerComp">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripcion</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($componentes ?? []) as $componente)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $componente->nombre_compo }}</td>
                                    <td>{{ $componente->descripcion_compo }}</td>
                                    <td>
                                        @php
                                            $stComp = strtolower(trim($componente->estado_compo ?? ''));
                                            $bgComp = 'bg-secondary';
                                            if (in_array($stComp, ['bueno', 'operativo', 'nuevo', 'funcionando'])) {
                                                $bgComp = 'bg-success';
                                            } elseif ($stComp === 'regular') {
                                                $bgComp = 'bg-warning text-dark';
                                            } elseif (str_contains($stComp, 'mantenimiento') || str_contains($stComp, 'reparar')) {
                                                $bgComp = 'bg-info text-dark';
                                            } elseif (str_contains($stComp, 'descarte') || $stComp === 'malo') {
                                                $bgComp = 'bg-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $bgComp }}">{{ $componente->estado_compo }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No hay componentes registrados para este equipo.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesorios del equipo -->
        <div class="row justify-content-center mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-tools me-2 text-primary"></i> Accesorios Vinculados
                        </h5>
                        <span class="badge bg-secondary rounded-pill">{{ count($accesorios ?? []) }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="headerAcces">
                                <tr>
                                    <th>Codigo</th>
                                    <th>Nombre</th>
                                    <th>Descripcion</th>
                                    <th>Observaciones</th>
                                    <th>Estado</th>
                                    <th>Vida Util</th>
                                    <th>Ubicacion</th>
                                    <th>Fecha Registro</th>
                                    <th>Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($accesorios as $accesorio)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $accesorio->cod_accesorio }}</td>
                                    <td class="fw-semibold text-dark">{{ $accesorio->nombre_acce }}</td>
                                    <td>{{ $accesorio->descripcion_acce }}</td>
                                    <td>{{ $accesorio->observacion_ace ?: 'N/A' }}</td>
                                    <td>
                                        @php
                                            $stAcce = strtolower(trim($accesorio->estado_acce ?? ''));
                                            $bgAcce = 'bg-secondary';
                                            if (in_array($stAcce, ['bueno', 'operativo', 'nuevo', 'funcionando'])) {
                                                $bgAcce = 'bg-success';
                                            } elseif ($stAcce === 'regular') {
                                                $bgAcce = 'bg-warning text-dark';
                                            } elseif (str_contains($stAcce, 'mantenimiento') || str_contains($stAcce, 'reparar')) {
                                                $bgAcce = 'bg-info text-dark';
                                            } elseif (str_contains($stAcce, 'descarte') || $stAcce === 'malo') {
                                                $bgAcce = 'bg-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $bgAcce }}">{{ $accesorio->estado_acce }}</span>
                                    </td>
                                    <td>{{ $accesorio->vida_util ?: 'N/A' }}</td>
                                    <td>{{ $accesorio->ubicacion ?: 'N/A' }}</td>
                                    <td>{{ $accesorio->fch_registro_acce ? \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="text-center">
                                        @if($accesorio->ruta_foto || $accesorio->FOTO_id_foto)
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFotoAccesorio_{{ preg_replace('/[^a-zA-Z0-9_-]/', '_', $accesorio->cod_accesorio) }}" title="Ver foto ampliada">
                                                <i class="bi bi-camera"></i>
                                            </button>
                                        @else
                                            <span class="text-muted small">No disponible</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">No hay accesorios vinculados a este equipo.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modales Bootstrap 5 de visualizacion ampliada de foto para accesorios -->
@foreach ($accesorios as $accesorio)
    @if($accesorio->ruta_foto || $accesorio->FOTO_id_foto)
    <div class="modal fade" id="modalFotoAccesorio_{{ preg_replace('/[^a-zA-Z0-9_-]/', '_', $accesorio->cod_accesorio) }}" tabindex="-1" aria-labelledby="modalFotoLabel_{{ preg_replace('/[^a-zA-Z0-9_-]/', '_', $accesorio->cod_accesorio) }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fs-6 fw-bold text-dark" id="modalFotoLabel_{{ preg_replace('/[^a-zA-Z0-9_-]/', '_', $accesorio->cod_accesorio) }}">
                        <i class="bi bi-image me-2 text-primary"></i>Foto de Accesorio: {{ $accesorio->nombre_acce }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <div class="p-2 border rounded bg-light d-flex align-items-center justify-content-center" style="min-height: 250px; max-height: 380px; overflow: hidden;">
                        <img src="{{ inventario_img_url($accesorio->ruta_foto ?? null) }}"
                             alt="{{ $accesorio->nombre_acce }}"
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 360px; object-fit: contain;"
                             onerror="this.onerror=null;this.src='{{ asset('img/placeholder-articulo.svg') }}';">
                    </div>
                    <div class="small text-muted mt-2 d-flex justify-content-between px-2">
                        <span>Codigo: <strong>{{ $accesorio->cod_accesorio }}</strong></span>
                        <span>Ubicacion: <strong>{{ $accesorio->ubicacion ?: 'No especificada' }}</strong></span>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection
