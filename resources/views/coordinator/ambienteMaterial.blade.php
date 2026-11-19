@extends('layouts.coordinator')

@section('title', 'Materiales del Ambiente')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/ambiente.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
    $tokenAmb = Crypt::encryptString($ambiente->id_ambiente);
    $rutaVolver = $isClinica ? route('coordinator.clinica.ambientes') : route('laboratorios.index');
@endphp

@section('content')
<main class="content px-3 py-3 ambiente-page">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional con boton volver -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                <i class="bi bi-tools me-2 text-primary"></i>Materiales del Ambiente: {{ $ambiente->nombre_completo_ambiente ?? $ambiente->nombre }}
            </h4>
            <a href="{{ $rutaVolver }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>

        <!-- Botonera de Categorias -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route($isClinica ? 'coordinator.clinica.equipos' : 'equipos.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-outline-primary d-flex align-items-center">
                    <i class="bi bi-pc-display me-1"></i> Equipos
                </a>
                <a href="{{ route($isClinica ? 'coordinator.clinica.materiales' : 'materiales.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-primary d-flex align-items-center">
                    <i class="bi bi-tools me-1"></i> Materiales
                </a>
                <a href="{{ route($isClinica ? 'coordinator.clinica.mobiliarios' : 'mobiliarios.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-outline-primary d-flex align-items-center">
                    <i class="bi bi-chair me-1"></i> Mobiliario
                </a>
            </div>
        </div>
        
        @if($materiales->isEmpty())
            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i> No existen materiales registrados en el ambiente {{ $ambiente->nombre_completo_ambiente ?? $ambiente->nombre }}.
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                        <i class="bi bi-list-check me-2 text-primary"></i> Lista de Materiales
                    </h5>
                    <span class="badge bg-secondary rounded-pill">{{ count($materiales) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th>Nro.</th>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                                <th>Fecha Registro</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @foreach($materiales as $material)
                                <tr>
                                    <td class="text-center">{{ $contador++ }}</td>
                                    <td class="fw-bold text-primary">{{ $material->cod_mate }}</td>
                                    <td class="fw-semibold text-dark">{{ $material->tipo_mate }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ inventario_estado_badge($material->estado_mate) }}">
                                            {{ $material->estado_mate ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ $material->observacion_mate ?: 'N/A' }}</td>
                                    <td class="text-center">{{ $material->fch_registrada ? \Carbon\Carbon::parse($material->fch_registrada)->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route($isClinica ? 'clinica.material.detalles' : 'material.detalles', ['token' => Crypt::encrypt($material->cod_mate)]) }}" class="btn btn-outline-primary btn-sm" title="Ver detalles del material">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</main>
@endsection
