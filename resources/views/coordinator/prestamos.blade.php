@extends('layouts.coordinator')

@section('title', 'Prestamos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/prestamos.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 py-3 pagPrestamo">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold fs-5 fs-sm-4" style="color: #0E0D39;">
                <i class="bi bi-arrow-left-right me-2 text-primary"></i>Prestamos Realizados {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }}
            </h4>
        </div>
    
        <!-- Barra de filtros y buscadores integrados -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
            <div class="row g-3 align-items-center">
                <!-- Buscador por solicitante -->
                <div class="col-12 col-md-6 col-lg-5">
                    <form method="GET" action="{{ route($isClinica ? 'clinica.prestamos.buscar' : 'prestamos.buscar') }}">
                        <label for="usuario_prestamo" class="form-label text-muted small fw-semibold">Buscar por Prestatario / Solicitante</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="usuario_prestamo" name="usuario" placeholder="Nombre prestatario..." value="{{ request('usuario') }}" aria-label="Nombre Prestatario">
                            <button class="btn btn-primary d-flex align-items-center" type="submit">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                            @if(request('usuario'))
                                <a href="{{ route($isClinica ? 'clinica.prestamos.index' : 'prestamos.index') }}" class="btn btn-outline-secondary" title="Limpiar busqueda">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Buscador por rango de fechas -->
                <div class="col-12 col-md-6 col-lg-7">
                    <form method="GET" action="{{ route($isClinica ? 'clinica.prestamos.buscar' : 'prestamos.buscar') }}">
                        <label class="form-label text-muted small fw-semibold">Filtrar por Rango de Fechas</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-calendar3"></i></span>
                            <label for="fecha_inicio" class="visually-hidden">Fecha de inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" placeholder="Desde" value="{{ request('fecha_inicio') }}" aria-label="Fecha de inicio">
                            <span class="input-group-text bg-light text-muted">a</span>
                            <label for="fecha_fin" class="visually-hidden">Fecha de fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" placeholder="Hasta" value="{{ request('fecha_fin') }}" aria-label="Fecha de fin">
                            <button class="btn btn-primary d-flex align-items-center" type="submit">
                                <i class="bi bi-funnel me-1"></i> Filtrar
                            </button>
                            @if(request('fecha_inicio') || request('fecha_fin'))
                                <a href="{{ route($isClinica ? 'clinica.prestamos.index' : 'prestamos.index') }}" class="btn btn-outline-secondary" title="Limpiar fechas">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Prestamos -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                    <i class="bi bi-list-check me-2 text-primary"></i> Registros de Prestamos
                </h5>
                <span class="badge bg-secondary rounded-pill">{{ $prestamos->total() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-ulat align-middle mb-0">
                    <thead class="table-ulat-header">
                        <tr>
                            <th>Prestatario</th>
                            <th>Descripcion</th>
                            <th>Fecha Prestamo</th>
                            <th>Encargado</th>
                            <th>Estado / Devolucion</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prestamos as $prestamo)
                            <tr>
                                <td class="fw-bold text-dark">{{ $prestamo->nombre_solicitante }}</td>
                                <td class="small text-muted">{{ $prestamo->descripcion_prestamo ?: 'Sin descripcion' }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}</td>
                                <td>{{ $prestamo->encargado ?: 'N/A' }}</td>
                                <td class="text-center">
                                    @if($prestamo->fch_devolucion)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>{{ \Carbon\Carbon::parse($prestamo->fch_devolucion)->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock-history me-1"></i>Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route($isClinica ? 'clinica.prestamos.detalles' : 'prestamos.detalles', ['token' => Crypt::encrypt($prestamo->id_prestamo)]) }}" class="btn btn-outline-primary btn-sm" title="Ver ficha del prestamo">
                                        <i class="bi bi-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No se encontraron prestamos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $prestamos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mensajes cuando no hay resultados en la busqueda -->
    @if(session('error_usuario'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Atencion',
                text: '{{ session('error_usuario') }}'
            }).then(() => {
                window.location.href = "{{ route($isClinica ? 'clinica.prestamos.index' : 'prestamos.index') }}";
            });
        </script>
    @endif

    @if(session('error_fecha'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Atencion',
                text: '{{ session('error_fecha') }}'
            });
        </script>
    @endif

    <!-- Mensaje de error de comunicacion con la BD -->
    @if(session('errordata'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errordata') }}',
            });
        </script>
    @endif
@endpush
