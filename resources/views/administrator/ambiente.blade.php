@extends('layouts.admin')

@section('title', 'Ambientes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/ambienteAdmin.css') }}">
@endpush

<!-- Dropdown de Sedes en la Navbar -->
@section('sede-dropdown')
    <li class="nav-item dropdown px-1">
        <a class="nav-link dropdown-toggle text-white" href="#" id="sedeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-geo-alt me-1"></i>Sedes
        </a>
        <ul class="dropdown-menu shadow" aria-labelledby="sedeDropdown">
            <li class="p-2">
                <form method="GET" action="{{ route('ambiente.index') }}">
                    <label for="sedeNav" class="form-label small fw-semibold text-muted mb-1">Seleccionar Sede:</label>
                    <select name="sede" id="sedeNav" class="form-select form-select-sm" onchange="this.form.submit()">
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
                <form method="GET" action="{{ route('ambiente.index') }}">
                    <label for="edificioNav" class="form-label small fw-semibold text-muted mb-1">Seleccionar Edificio:</label>
                    <select name="edificio" id="edificioNav" class="form-select form-select-sm" onchange="this.form.submit()">
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
<main class="content px-3 py-4 pagAmbienteAdmin">  
    <div class="container-fluid">
        <!-- Cabecera de la pagina y accesos rapidos -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-door-open me-2 text-primary"></i>Ambientes
                </h2>
                <p class="text-muted small mb-0">
                    Sede: <strong>{{ $nombreSedeSeleccionada }}</strong> | Edificio: <strong>{{ $nombreEdificioSeleccionada }}</strong>
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('tipoambiente.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-grid-3x3-gap"></i>
                    <span>Tipos de Ambientes</span>
                </a>
                <a href="{{ route('ambientes.index') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-plus-circle"></i>
                    <span>Gestionar Ambientes</span>
                </a>
            </div>
        </div>

        <!-- Filtros y Busqueda Responsivos -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <form method="GET" action="{{ route('ambiente.index') }}">
                        <label for="search" class="form-label small fw-semibold text-muted mb-1">Buscar por nombre:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Nombre del Ambiente" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                                <i class="bi bi-search"></i>
                                <span>Buscar</span>
                            </button>
                        </div>
                        <input type="hidden" name="sede" value="{{ Crypt::encryptString($sedeSeleccionada) }}">
                        <input type="hidden" name="edificio" value="{{ Crypt::encryptString($edificioSeleccionado) }}">
                    </form>
                </div>
                <div class="col-12 col-md-6">
                    <form method="GET" action="{{ route('ambiente.index') }}">
                        <label for="piso" class="form-label small fw-semibold text-muted mb-1">Filtrar por piso:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-layers"></i></span>
                            <select name="piso" id="piso" class="form-select">
                                <option value="">Todos los Pisos</option>
                                @foreach($pisos as $piso)
                                    <option value="{{ $piso->numero_piso }}" {{ $pisoSeleccionado == $piso->numero_piso ? 'selected' : '' }}>
                                        {{ $piso->numero_piso }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                                <i class="bi bi-funnel"></i>
                                <span>Filtrar</span>
                            </button>
                        </div>
                        <input type="hidden" name="sede" value="{{ Crypt::encryptString($sedeSeleccionada) }}">
                        <input type="hidden" name="edificio" value="{{ Crypt::encryptString($edificioSeleccionado) }}">
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla principal de Ambientes -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th class="text-center" style="width: 100px;">Piso</th>
                                <th>Tipo de Ambiente</th>
                                <th>Nombre del Ambiente</th>
                                <th>Descripcion</th>
                                <th class="text-center" style="width: 90px;">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @forelse($ambientes as $ambiente)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $ambiente->numero_piso }}</span>
                                    </td>
                                    <td>{{ $ambiente->nombre_amb }}</td>
                                    <td class="fw-semibold text-dark">{{ $ambiente->nombre }}</td>
                                    <td>{{ $ambiente->descripcion_amb ?: '-' }}</td>
                                    <td class="text-center"> 
                                        <a href="{{ route('ambienteAdmin.contenido', ['id_ambiente' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Contenido del Ambiente">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>No hay ambientes registrados para el edificio seleccionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    @if ($noHayAmbientes)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin Ambientes',
                    text: 'No hay ambientes registrados para el edificio seleccionado.'
                });
            });
        </script>
    @endif

    @if ($search && $noAmbientesEncontrados)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Encontrado',
                    text: 'No se encontraron ambientes con el nombre especificado.'
                });
            });
        </script>
    @endif

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
