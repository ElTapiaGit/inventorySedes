@extends('layouts.admin')

@section('title', 'Materiales del Ambiente')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/ambienteAdmin.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagMateAdmin">
    <div class="container-fluid">
        <!-- Cabecera de seccion -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-box-seam me-2 text-primary"></i>Materiales del Ambiente
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
            <a href="{{ route('ambienteAdmin.contenido', ['id_ambiente' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-grid"></i>
                <span>Todos los Articulos</span>
            </a>
            <a href="{{ route('contenidoequipos.index', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-pc-display"></i>
                <span>Equipos</span>
            </a>
            <a href="{{ route('materialesAdmin.show', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-box-seam"></i>
                <span>Materiales ({{ count($materiales) }})</span>
            </a>
            <a href="{{ route('contenidomobiliario.index', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-inboxes"></i>
                <span>Mobiliario</span>
            </a>
        </div>

        @if($materiales->isEmpty())
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5 text-center text-muted">
                    <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary"></i>
                    No existen materiales registrados en el ambiente <strong>{{ $ambiente->nombre }}</strong>.
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
                                    <th>Tipo de Material</th>
                                    <th class="text-center">Estado</th>
                                    <th>Observaciones</th>
                                    <th>Fecha Registro</th>
                                    <th class="text-center" style="width: 80px;">Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach($materiales as $material)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                        <td class="fw-semibold text-dark">{{ $material->cod_mate }}</td>
                                        <td>{{ $material->tipo_mate }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ inventario_estado_badge($material->estado_mate) }}">
                                                {{ $material->estado_mate }}
                                            </span>
                                        </td>
                                        <td>{{ $material->observacion_mate ?: '-' }}</td>
                                        <td>{{ $material->fch_registrada ? \Carbon\Carbon::parse($material->fch_registrada)->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('maeterialAdmin.detalles', ['token' => Crypt::encryptString($material->cod_mate)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Ficha Tecnica">
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
