@extends('layouts.coordinator')

@section('title', 'Inventario del Ambiente')

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
                <i class="bi bi-door-open me-2 text-primary"></i>{{ $ambiente->nombre_completo_ambiente ?? $ambiente->nombre }}
            </h4>
            <a href="{{ $rutaVolver }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>

        <!-- Botonera de Categorias -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route($isClinica ? 'coordinator.clinica.equipos' : 'equipos.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-outline-primary d-flex align-items-center">
                    <i class="bi bi-pc-display me-1"></i> Ver Equipos
                </a>
                <a href="{{ route($isClinica ? 'coordinator.clinica.materiales' : 'materiales.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-outline-primary d-flex align-items-center">
                    <i class="bi bi-tools me-1"></i> Ver Materiales
                </a>
                <a href="{{ route($isClinica ? 'coordinator.clinica.mobiliarios' : 'mobiliarios.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-outline-primary d-flex align-items-center">
                    <i class="bi bi-chair me-1"></i> Ver Mobiliario
                </a>
            </div>
        </div>
        
        @if($equipos->isEmpty() && $materiales->isEmpty() && $mobiliarios->isEmpty())
            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i> El ambiente {{ $ambiente->nombre_completo_ambiente ?? $ambiente->nombre }} no tiene registros de equipos, materiales ni mobiliarios.
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                        <i class="bi bi-boxes me-2 text-primary"></i> Inventario Consolidado del Ambiente
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th>Nro.</th>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                                <th>Vida Util</th>
                                <th>Fecha Registro</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @foreach($equipos as $equipo)
                                <tr>
                                    <td class="text-center">{{ $contador++ }}</td>
                                    <td class="fw-bold text-primary">{{ $equipo->cod_equipo }}</td>
                                    <td class="fw-semibold text-dark">{{ $equipo->nombre_equi }}</td>
                                    <td><span class="badge bg-light text-dark border">Equipo</span></td>
                                    <td class="text-center">
                                        <span class="badge {{ inventario_estado_badge($equipo->estado_equi) }}">
                                            {{ $equipo->estado_equi ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ $equipo->observaciones_equi ?: 'N/A' }}</td>
                                    <td class="text-center">{{ $equipo->vida_util ?: 'N/A' }}</td>
                                    <td class="text-center">{{ $equipo->fch_registro ? \Carbon\Carbon::parse($equipo->fch_registro)->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route($isClinica ? 'clinica.equipo.detalles' : 'equipo.detalles', ['token' => Crypt::encrypt($equipo->cod_equipo)]) }}" class="btn btn-outline-primary btn-sm" title="Ver detalles del equipo">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($materiales as $material)
                                <tr>
                                    <td class="text-center">{{ $contador++ }}</td>
                                    <td class="fw-bold text-primary">{{ $material->cod_mate }}</td>
                                    <td class="fw-semibold text-dark">{{ $material->tipo_mate }}</td>
                                    <td><span class="badge bg-light text-dark border">Material</span></td>
                                    <td class="text-center">
                                        <span class="badge {{ inventario_estado_badge($material->estado_mate) }}">
                                            {{ $material->estado_mate ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ $material->observacion_mate ?: 'N/A' }}</td>
                                    <td class="text-center">N/A</td>
                                    <td class="text-center">{{ $material->fch_registrada ? \Carbon\Carbon::parse($material->fch_registrada)->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route($isClinica ? 'clinica.material.detalles' : 'material.detalles', ['token' => Crypt::encrypt($material->cod_mate)]) }}" class="btn btn-outline-primary btn-sm" title="Ver detalles del material">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($mobiliarios as $mobiliario)
                                <tr>
                                    <td class="text-center">{{ $contador++ }}</td>
                                    <td class="fw-bold text-primary">{{ $mobiliario->cod_mueble }}</td>
                                    <td class="fw-semibold text-dark">{{ $mobiliario->tipo_mueble }}</td>
                                    <td><span class="badge bg-light text-dark border">Mobiliario</span></td>
                                    <td class="text-center">
                                        <span class="badge {{ inventario_estado_badge($mobiliario->estado_mueb) }}">
                                            {{ $mobiliario->estado_mueb ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ $mobiliario->observacion ?: 'N/A' }}</td>
                                    <td class="text-center">{{ $mobiliario->vida_util ?: 'N/A' }}</td>
                                    <td class="text-center">{{ $mobiliario->fch_registro ? \Carbon\Carbon::parse($mobiliario->fch_registro)->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route($isClinica ? 'clinica.mobiliario.detalles' : 'mobiliario.detalles', ['token' => Crypt::encrypt($mobiliario->cod_mueble)]) }}" class="btn btn-outline-primary btn-sm" title="Ver detalles del mueble">
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
