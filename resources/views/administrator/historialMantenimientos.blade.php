@extends('layouts.admin')

@section('title', 'Historial de Mantenimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/mantenimiento.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagHistoAdmin">
    <div class="container-fluid">
        <!-- Cabecera de seccion con boton Volver -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Historial de Mantenimientos
                </h2>
                <p class="text-muted small mb-0">Consulta cronologica de intervenciones tecnicas y cierres de mantenimiento.</p>
            </div>
            <a href="{{ route('mantenimientosAdmin.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Volver a Mantenimientos</span>
            </a>
        </div>

        <!-- Filtros responsivos (row g-2) -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
            <form action="{{ route('historialAdmin.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="filtro_cod_articulo" class="form-label small fw-semibold text-muted mb-1">Codigo de Articulo:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-hash"></i></span>
                            <input type="text" name="cod_equipo" id="filtro_cod_articulo" class="form-control" placeholder="Codigo" value="{{ request('cod_equipo') }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="filtro_fch_inicio" class="form-label small fw-semibold text-muted mb-1">Fecha de Inicio:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-calendar"></i></span>
                            <input type="date" name="fch_inicio" id="filtro_fch_inicio" class="form-control" value="{{ request('fch_inicio') }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="filtro_fch_fin" class="form-label small fw-semibold text-muted mb-1">Fecha de Fin:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-calendar-check"></i></span>
                            <input type="date" name="fch_fin" id="filtro_fch_fin" class="form-control" value="{{ request('fch_fin') }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-search"></i>
                            <span>Filtrar</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de Historial -->
        @if($mantenimientos->isEmpty())
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5 text-center text-muted">
                    <i class="bi bi-info-circle fs-1 d-block mb-2 text-secondary"></i>
                    No hay registros de historial de mantenimientos que coincidan con la busqueda.
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
                                    <th>Nombre del Articulo</th>
                                    <th>Tecnico</th>
                                    <th>Fecha Inicio</th>
                                    <th>Ambiente</th>
                                    <th>Edificio</th>
                                    <th>Sede</th>
                                    <th class="text-center" style="width: 80px;">Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mantenimientos as $index => $mantenimiento)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                        <td class="fw-semibold text-dark">{{ $mantenimiento->cod_articulo }}</td>
                                        <td>
                                            @if($mantenimiento->nombre_articulo_equipo)
                                                {{ $mantenimiento->nombre_articulo_equipo }}
                                            @elseif($mantenimiento->nombre_articulo_mobiliario)
                                                {{ $mantenimiento->nombre_articulo_mobiliario }}
                                            @elseif($mantenimiento->nombre_articulo_material)
                                                {{ $mantenimiento->nombre_articulo_material }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $mantenimiento->nombre_tecnico }} {{ $mantenimiento->ap_paterno }} {{ $mantenimiento->ap_materno }}</td>
                                        <td>
                                            <i class="bi bi-calendar-event me-1 text-primary"></i>
                                            {{ \Carbon\Carbon::parse($mantenimiento->fch_inicio)->format('d/m/Y') }}
                                        </td>
                                        <td>{{ $mantenimiento->nombre_ambiente }}</td>
                                        <td>{{ $mantenimiento->nombre_edificio }}</td>
                                        <td>{{ $mantenimiento->nombre_sede }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('detallesManteAdmin.index', ['id' => encrypt($mantenimiento->id_mantenimiento_ini)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 border-0 d-flex justify-content-center">
                    {{ $mantenimientos->appends(request()->input())->links('pagination::bootstrap-5') }}
                </div>
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
