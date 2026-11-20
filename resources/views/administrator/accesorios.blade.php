@extends('layouts.admin')

@section('title', 'Accesorios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/accesorios.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagAccesAdmin">
    <div class="container-fluid">
        <!-- Cabecera de seccion -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-plug me-2 text-primary"></i>Gestion de Accesorios
                </h2>
                <p class="text-muted small mb-0">Listado de accesorios para reposicion o cambio y navegacion entre modalidades.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('accesoriosadmin.unicos') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-cpu"></i>
                    <span>Accesorios Unicos</span>
                </a>
                <a href="{{ route('accesoriosadmin.conEquipo') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-pc-display"></i>
                    <span>Accesorios con Equipos</span>
                </a>
            </div>
        </div>

        @if($accesorios->isEmpty())
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5 text-center text-muted">
                    <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                    No hay accesorios pendientes para reponer o cambiar.
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                        <i class="bi bi-exclamation-triangle me-2 text-warning"></i>Lista de Accesorios para Reponer / Cambiar
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-ulat align-middle mb-0">
                            <thead class="table-ulat-header">
                                <tr>
                                    <th>Codigo</th>
                                    <th>Nombre del Accesorio</th>
                                    <th>Descripcion</th>
                                    <th>Observacion</th>
                                    <th class="text-center">Estado</th>
                                    <th>Vida Util</th>
                                    <th>Ubicacion</th>
                                    <th>Fecha Registro</th>
                                    <th>Ultimo Cambio</th>
                                    <th>Equipo Asignado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accesorios as $accesorio)
                                    <tr>
                                        <td class="fw-semibold text-dark">{{ $accesorio->cod_accesorio }}</td>
                                        <td>{{ $accesorio->nombre_acce }}</td>
                                        <td>{{ $accesorio->descripcion_acce ?: '-' }}</td>
                                        <td>{{ $accesorio->observacion_ace ?: '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ inventario_estado_badge($accesorio->estado_acce) }}">
                                                {{ $accesorio->estado_acce }}
                                            </span>
                                        </td>
                                        <td>{{ $accesorio->vida_util ?: 'N/A' }}</td>
                                        <td>{{ $accesorio->ubicacion ?: 'N/A' }}</td>
                                        <td>
                                            @if(!empty($accesorio->fch_registro_acce))
                                                {{ \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($accesorio->fecha_ultimo_cambio) && $accesorio->fecha_ultimo_cambio !== 'Sin cambios')
                                                {{ \Carbon\Carbon::parse($accesorio->fecha_ultimo_cambio)->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted small">Sin cambios</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($accesorio->equipo_codigo))
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-pc-display me-1 text-primary"></i>{{ $accesorio->equipo_codigo }}
                                                </span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 border-0 d-flex justify-content-center">
                    {{ $accesorios->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    @if($accesorios->isEmpty())
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin pendientes',
                text: "No hay accesorios pendientes para reponer o cambiar.",
            });
        </script>
    @endif
@endpush
