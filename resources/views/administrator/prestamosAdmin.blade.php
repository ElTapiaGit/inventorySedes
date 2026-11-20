@extends('layouts.admin')

@section('title', 'Prestamos Realizados')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/prestamos.css') }}">
@endpush

<!-- Dropdown de Sedes en la Navbar -->
@section('sede-dropdown')
    <li class="nav-item dropdown px-1">
        <a class="nav-link dropdown-toggle text-white" href="#" id="sedeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-geo-alt me-1"></i>Sedes
        </a>
        <ul class="dropdown-menu shadow" aria-labelledby="sedeDropdown">
            <li class="p-2">
                <form method="GET" action="{{ route('prestamosAdmin.index') }}">
                    <label for="sedePrestNav" class="form-label small fw-semibold text-muted mb-1">Seleccionar Sede:</label>
                    <select name="sede" id="sedePrestNav" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id_sede }}" {{ $sedeSeleccionada == $sede->id_sede ? 'selected' : '' }}>
                                {{ $sede->nombre }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </li>
        </ul>
    </li>
@endsection

<!-- Dropdown de Edificios en la Navbar -->
@section('edificio-dropdown')
    <li class="nav-item dropdown px-2">
        <a class="nav-link dropdown-toggle text-white" href="#" id="edificioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-building me-1"></i>Edificios
        </a>
        <ul class="dropdown-menu shadow" aria-labelledby="edificioDropdown">
            <li class="p-2">
                <form method="GET" action="{{ route('prestamosAdmin.index') }}">
                    <label for="edificioPrestNav" class="form-label small fw-semibold text-muted mb-1">Seleccionar Edificio:</label>
                    <select name="edificio" id="edificioPrestNav" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($edificios as $edificio)
                            <option value="{{ $edificio->id_edificio }}" {{ $edificioSeleccionado == $edificio->id_edificio ? 'selected' : '' }}>
                                {{ $edificio->nombre_edi }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </li>
        </ul>
    </li>
@endsection

@section('content')
<main class="content px-3 py-4 pagPrestAdmin">
    <div class="container-fluid">
        <!-- Cabecera de la pagina -->
        <div class="mb-4">
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-journal-arrow-up me-2 text-primary"></i>Prestamos Realizados
            </h2>
            <p class="text-muted small mb-0">
                Sede: <strong>{{ $nombreSedeSeleccionada }}</strong> | Edificio: <strong>{{ $nombreEdificioSeleccionada }}</strong>
            </p>
        </div>

        <!-- Formularios de busqueda (sin @csrf en GET) -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <form method="GET" action="{{ route('prestamosAdmin.buscar') }}">
                        <label for="usuario_prestamo" class="form-label small fw-semibold text-muted mb-1">Buscar por prestatario:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="usuario_prestamo" name="usuario" placeholder="Nombre del Prestatario" value="{{ request('usuario') }}">
                            <button class="btn btn-primary d-inline-flex align-items-center gap-1" type="submit">
                                <i class="bi bi-search"></i>
                                <span>Buscar</span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-md-6">
                    <form method="GET" action="{{ route('prestamosAdmin.buscar') }}">
                        <label for="fecha_prestamo" class="form-label small fw-semibold text-muted mb-1">Buscar por fecha de prestamo:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-calendar"></i></span>
                            <input type="text" class="form-control" id="fecha_prestamo" name="fecha" placeholder="Fecha: aaaa-mm-dd" value="{{ request('fecha') }}">
                            <button class="btn btn-primary d-inline-flex align-items-center gap-1" type="submit">
                                <i class="bi bi-search"></i>
                                <span>Buscar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla principal de Prestamos -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                    <i class="bi bi-list-check me-2 text-primary"></i>Listado de Prestamos
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th>Prestatario</th>
                                <th>Descripcion del Prestamo</th>
                                <th>Fecha Prestada</th>
                                <th>Encargado Responsable</th>
                                <th class="text-center">Estado Devolucion</th>
                                <th class="text-center" style="width: 80px;">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prestamos as $prestamo)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $prestamo->nombre_solicitante }}</td>
                                    <td>{{ $prestamo->descripcion_prestamo ?: '-' }}</td>
                                    <td>
                                        <i class="bi bi-calendar-event me-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}
                                    </td>
                                    <td>{{ $prestamo->personal->nombre_completo ?? 'No asignado' }}</td>
                                    <td class="text-center">
                                        @if(optional($prestamo->devolucion)->fch_devolucion)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Devuelto ({{ \Carbon\Carbon::parse($prestamo->devolucion->fch_devolucion)->format('d/m/Y') }})
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock-history me-1"></i>Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('prestamosAdmin.detalles', ['id_prestamo' => Crypt::encrypt($prestamo->id_prestamo)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Ficha Tecnica">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>No hay registros de prestamos para los criterios seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-center">
                {{ $prestamos->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    @if(session('errorbuscar'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin Resultados',
                text: '{{ session('errorbuscar') }}',
            });
        </script>
    @endif

    @if(session('errordata'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Atencion',
                text: '{{ session('errordata') }}',
            });
        </script>
    @endif
@endpush
