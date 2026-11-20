@extends('layouts.admin')

@section('title', 'Contenido del Ambiente')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/ambienteAdmin.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagContAdmin">
    <div class="container-fluid">
        <!-- Cabecera de seccion -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-boxes me-2 text-primary"></i>Contenido del Ambiente
                </h2>
                <p class="text-muted small mb-0">
                    {{ $ambiente->tipoAmbiente->nombre_amb ?? 'Ambiente' }} - <strong>{{ $ambiente->nombre }}</strong>
                </p>
            </div>
            <a href="{{ route('ambiente.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Volver a Ambientes</span>
            </a>
        </div>

        <!-- Botonera de Categorias -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="{{ route('ambienteAdmin.contenido', ['id_ambiente' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-grid-fill"></i>
                <span>Todos los Articulos</span>
            </a>
            <a href="{{ route('contenidoequipos.index', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-pc-display"></i>
                <span>Equipos ({{ count($equipos) }})</span>
            </a>
            <a href="{{ route('materialesAdmin.show', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-box-seam"></i>
                <span>Materiales ({{ count($materiales) }})</span>
            </a>
            <a href="{{ route('contenidomobiliario.index', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-inboxes"></i>
                <span>Mobiliario ({{ count($mobiliarios) }})</span>
            </a>
        </div>

        @if($equipos->isEmpty() && $materiales->isEmpty() && $mobiliarios->isEmpty())
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    El ambiente <strong>{{ $ambiente->nombre }}</strong> no tiene registros de equipos, materiales ni mobiliarios.
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-ulat align-middle mb-0">
                            <thead class="table-ulat-header">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>Codigo</th>
                                    <th>Articulo / Nombre</th>
                                    <th>Tipo / Categoria</th>
                                    <th class="text-center">Estado</th>
                                    <th>Observaciones</th>
                                    <th>Vida Util</th>
                                    <th>Fecha Registro</th>
                                    <th class="text-center" style="width: 80px;">Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach($equipos as $equipo)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                        <td class="fw-semibold text-dark">{{ $equipo->cod_equipo }}</td>
                                        <td>{{ $equipo->nombre_equi }}</td>
                                        <td><span class="badge bg-light text-dark border">Equipo</span></td>
                                        <td class="text-center">
                                            <span class="badge {{ inventario_estado_badge($equipo->estado_equi) }}">
                                                {{ $equipo->estado_equi }}
                                            </span>
                                        </td>
                                        <td>{{ $equipo->observaciones_equi ?: '-' }}</td>
                                        <td>{{ $equipo->vida_util ?: 'N/A' }}</td>
                                        <td>{{ $equipo->fch_registro ? \Carbon\Carbon::parse($equipo->fch_registro)->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('equiposAdmin.detalles', ['token' => Crypt::encryptString($equipo->cod_equipo)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach($materiales as $material)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                        <td class="fw-semibold text-dark">{{ $material->cod_mate }}</td>
                                        <td>{{ $material->tipo_mate }}</td>
                                        <td><span class="badge bg-light text-dark border">Material</span></td>
                                        <td class="text-center">
                                            <span class="badge {{ inventario_estado_badge($material->estado_mate) }}">
                                                {{ $material->estado_mate }}
                                            </span>
                                        </td>
                                        <td>{{ $material->observacion_mate ?: '-' }}</td>
                                        <td>-</td>
                                        <td>{{ $material->fch_registrada ? \Carbon\Carbon::parse($material->fch_registrada)->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('maeterialAdmin.detalles', ['token' => Crypt::encryptString($material->cod_mate)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach($mobiliarios as $mobiliario)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                        <td class="fw-semibold text-dark">{{ $mobiliario->cod_mueble }}</td>
                                        <td>{{ optional($mobiliario->tipoMobiliario)->tipo_mueble ?? 'Mobiliario' }}</td>
                                        <td><span class="badge bg-light text-dark border">Mobiliario</span></td>
                                        <td class="text-center">
                                            <span class="badge {{ inventario_estado_badge($mobiliario->estado_mueb) }}">
                                                {{ $mobiliario->estado_mueb }}
                                            </span>
                                        </td>
                                        <td>{{ $mobiliario->observacion ?: '-' }}</td>
                                        <td>{{ $mobiliario->vida_util ?: 'N/A' }}</td>
                                        <td>{{ $mobiliario->fch_registro ? \Carbon\Carbon::parse($mobiliario->fch_registro)->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('mobiliarioAdmin.detalles', ['cod_mueble' => Crypt::encryptString($mobiliario->cod_mueble)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</main>
@endsection
