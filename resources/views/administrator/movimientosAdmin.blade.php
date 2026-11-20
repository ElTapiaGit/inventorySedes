@extends('layouts.admin')

@section('title', 'Movimientos de Ambientes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/movimiento.css') }}">
@endpush

<!-- Dropdown de Sedes en la Navbar -->
@section('sede-dropdown')
    <li class="nav-item dropdown px-1">
        <a class="nav-link dropdown-toggle text-white" href="#" id="sedeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-geo-alt me-1"></i>Sedes
        </a>
        <ul class="dropdown-menu shadow" aria-labelledby="sedeDropdown">
            <li class="p-2">
                <form method="GET" action="{{ route('movimientosAdmin.index') }}">
                    <label for="sedeMovNav" class="form-label small fw-semibold text-muted mb-1">Seleccionar Sede:</label>
                    <select name="sede" id="sedeMovNav" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($sedes as $sede)
                            <option value="{{ Crypt::encryptString($sede->id_sede) }}" {{ $sedeSeleccionada == $sede->id_sede ? 'selected' : '' }}>
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
                <form method="GET" action="{{ route('movimientosAdmin.index') }}">
                    <label for="edificioMovNav" class="form-label small fw-semibold text-muted mb-1">Seleccionar Edificio:</label>
                    <select name="edificio" id="edificioMovNav" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($edificios as $edificio)
                            <option value="{{ Crypt::encryptString($edificio->id_edificio) }}" {{ $edificioSeleccionado == $edificio->id_edificio ? 'selected' : '' }}>
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
<main class="content px-3 py-4 pagMovAdmin">
    <div class="container-fluid">
        <!-- Cabecera de la pagina -->
        <div class="mb-4">
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-arrow-left-right me-2 text-primary"></i>Uso y Movimiento de Ambientes
            </h2>
            <p class="text-muted small mb-0">
                Sede: <strong>{{ $nombreSedeSeleccionada }}</strong> | Edificio: <strong>{{ $nombreEdificioSeleccionada }}</strong>
            </p>
        </div>

        <!-- Formularios de busqueda -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <form method="POST" action="{{ route('movimientosAdmin.buscar') }}">
                        @csrf
                        <label for="nombre_usuario" class="form-label small fw-semibold text-muted mb-1">Buscar por usuario:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" placeholder="Nombre de usuario" value="{{ request('nombre_usuario') }}">
                            <button class="btn btn-primary d-inline-flex align-items-center gap-1" type="submit">
                                <i class="bi bi-search"></i>
                                <span>Buscar</span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-md-6">
                    <form method="POST" action="{{ route('movimientosAdmin.buscar') }}">
                        @csrf
                        <label for="fecha_inicio" class="form-label small fw-semibold text-muted mb-1">Buscar por fecha:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-calendar"></i></span>
                            <input type="text" id="fecha_inicio" name="fecha_inicio" placeholder="Buscar fecha: dd/mm/aaaa" class="form-control" value="{{ request('fecha_inicio') }}">
                            <button class="btn btn-primary d-inline-flex align-items-center gap-1" type="submit">
                                <i class="bi bi-search"></i>
                                <span>Buscar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla principal de Movimientos -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                    <i class="bi bi-list-check me-2 text-primary"></i>Registros de Uso de Ambientes
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th>Ambiente</th>
                                <th>Usuario / Docente</th>
                                <th>Actividad / Motivo</th>
                                <th>Semestre</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center" style="width: 80px;">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movimientos as $movimiento)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $movimiento->nombre_ambiente }}</td>
                                    <td>{{ $movimiento->nombre_usuario }}</td>
                                    <td>{{ $movimiento->descripcion ?: '-' }}</td>
                                    <td>{{ $movimiento->semestre ?: '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($movimiento->fch_uso)->format('d/m/Y') }}</td>
                                    <td>{{ $movimiento->hora_uso }}</td>
                                    <td class="text-center">
                                        @if($movimiento->fch_fin)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Finalizado
                                            </span>
                                        @else
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-hourglass-split me-1"></i>En Curso
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('movimientosAdmin.detalles', encrypt($movimiento->id_uso_ambiente)) }}" class="btn btn-sm btn-outline-primary" title="Ver Ficha Tecnica">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>No hay registros de movimientos para los criterios seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-center">
                {{ $movimientos->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    @if(session('error_usuario'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Atencion',
                text: '{{ session('error_usuario') }}'
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

    @if(session('errordata'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Atencion',
                text: '{{ session('errordata') }}'
            });
        </script>
    @endif
@endpush
