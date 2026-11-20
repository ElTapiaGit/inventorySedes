@extends('layouts.admin')

@section('title', 'Mantenimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/mantenimiento.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagMantAdmin">
    <div class="container-fluid">
        <!-- Cabecera de seccion -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-wrench-adjustable me-2 text-primary"></i>Mantenimiento de Articulos
                </h2>
                <p class="text-muted small mb-0">Listado de equipos, mobiliario y materiales programados o en mantenimiento.</p>
            </div>
            <a href="{{ route('historialAdmin.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-clock-history"></i>
                <span>Historial de Mantenimientos</span>
            </a>
        </div>

        @if($equipos->isEmpty() && $mobiliarios->isEmpty() && $materiales->isEmpty())
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5 text-center text-muted">
                    <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                    No hay articulos registrados actualmente para mantenimiento.
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                        <i class="bi bi-tools me-2 text-primary"></i>Articulos en Mantenimiento
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-ulat align-middle mb-0">
                            <thead class="table-ulat-header">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>Codigo</th>
                                    <th>Nombre del Articulo</th>
                                    <th>Tipo</th>
                                    <th>Observaciones</th>
                                    <th class="text-center">Estado</th>
                                    <th>Ambiente</th>
                                    <th>Edificio</th>
                                    <th>Sede</th>
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
                                        <td>{{ $equipo->observaciones_equi ?: '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ inventario_estado_badge($equipo->estado_equi) }}">
                                                {{ $equipo->estado_equi }}
                                            </span>
                                        </td>
                                        <td>{{ $equipo->nombre }}</td>
                                        <td>{{ $equipo->nombre_edi }}</td>
                                        <td>{{ $equipo->nombre_sede }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('equiposAdmin.detalles', ['token' => Crypt::encryptString($equipo->cod_equipo)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach($mobiliarios as $mobiliario)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                        <td class="fw-semibold text-dark">{{ $mobiliario->cod_mueble }}</td>
                                        <td>{{ $mobiliario->tipo_mueble }}</td>
                                        <td><span class="badge bg-light text-dark border">Mobiliario</span></td>
                                        <td>{{ $mobiliario->observacion ?: '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ inventario_estado_badge($mobiliario->estado_mueb) }}">
                                                {{ $mobiliario->estado_mueb }}
                                            </span>
                                        </td>
                                        <td>{{ $mobiliario->nombre }}</td>
                                        <td>{{ $mobiliario->nombre_edi }}</td>
                                        <td>{{ $mobiliario->nombre_sede }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('mobiliarioAdmin.detalles', ['cod_mueble' => Crypt::encryptString($mobiliario->cod_mueble)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
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
                                        <td>{{ $material->observacion_mate ?: '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ inventario_estado_badge($material->estado_mate) }}">
                                                {{ $material->estado_mate }}
                                            </span>
                                        </td>
                                        <td>{{ $material->nombre }}</td>
                                        <td>{{ $material->nombre_edi }}</td>
                                        <td>{{ $material->nombre_sede }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('maeterialAdmin.detalles', ['token' => Crypt::encryptString($material->cod_mate)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
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

            <!-- Paginacion independiente -->
            <div class="d-flex flex-wrap justify-content-center gap-3 mt-3">
                @if($equipos->hasPages())
                    <div class="text-center">
                        <small class="text-muted d-block mb-1">Equipos (Pagina {{ $equipos->currentPage() }} de {{ $equipos->lastPage() }})</small>
                        {{ $equipos->links('pagination::bootstrap-5') }}
                    </div>
                @endif
                @if($mobiliarios->hasPages())
                    <div class="text-center">
                        <small class="text-muted d-block mb-1">Mobiliarios (Pagina {{ $mobiliarios->currentPage() }} de {{ $mobiliarios->lastPage() }})</small>
                        {{ $mobiliarios->links('pagination::bootstrap-5') }}
                    </div>
                @endif
                @if($materiales->hasPages())
                    <div class="text-center">
                        <small class="text-muted d-block mb-1">Materiales (Pagina {{ $materiales->currentPage() }} de {{ $materiales->lastPage() }})</small>
                        {{ $materiales->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
            });
        </script>
    @endif
@endpush
