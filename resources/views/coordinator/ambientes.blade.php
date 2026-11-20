@extends('layouts.coordinator')

@section('title', 'Ambientes')

@section('content')
<main class="content px-3 py-3">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold fs-5 fs-sm-4" style="color: #0E0D39;">
                <i class="bi bi-door-open me-2 text-primary"></i>Ambientes {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }}
            </h4>
        </div>

        <!-- Barra de herramientas y filtros integrados -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
            <div class="row g-3 align-items-center">
                <!-- Buscador por nombre -->
                <div class="col-12 col-md-6">
                    <form method="GET" action="{{ route('coordinator.clinica.ambientes') }}">
                        <label for="search_ambiente" class="form-label text-muted small fw-semibold">Buscar por Nombre de Ambiente</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-search"></i></span>
                            <input type="text" id="search_ambiente" name="search" class="form-control" placeholder="Nombre del ambiente..." value="{{ request('search') }}" aria-label="Buscar por nombre">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('coordinator.clinica.ambientes') }}" class="btn btn-outline-secondary" title="Limpiar busqueda">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Filtro por piso -->
                <div class="col-12 col-md-6">
                    <form method="GET" action="{{ route('coordinator.clinica.ambientes') }}">
                        <label for="piso" class="form-label text-muted small fw-semibold">Filtrar por Piso</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-layers"></i></span>
                            <select name="piso" id="piso" class="form-select" aria-label="Seleccionar Piso">
                                <option value="">Todos los Pisos</option>
                                @foreach($pisos as $piso)
                                    <option value="{{ $piso->numero_piso }}" {{ $pisoSeleccionado == $piso->numero_piso ? 'selected' : '' }}>
                                        Piso {{ $piso->numero_piso }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <i class="bi bi-funnel me-1"></i> Filtrar
                            </button>
                            @if($pisoSeleccionado)
                                <a href="{{ route('coordinator.clinica.ambientes') }}" class="btn btn-outline-secondary" title="Limpiar filtro">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Ambientes -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-ulat align-middle mb-0">
                    <thead class="table-ulat-header">
                        <tr>
                            <th>Nro.</th>
                            <th>Piso</th>
                            <th>Nombre del Ambiente</th>
                            <th>Tipo de Ambiente</th>
                            <th>Descripcion</th>
                            <th>Contenido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $contador = 1;
                        @endphp
                        @forelse($ambientes as $ambiente)
                            <tr>
                                <td class="text-center">{{ $contador++ }}</td>
                                <td class="text-center fw-semibold">{{ $ambiente->numero_piso }}</td>
                                <td class="fw-bold text-dark">{{ $ambiente->nombre_ambiente }}</td>
                                <td>{{ $ambiente->tipo_ambiente }}</td>
                                <td class="small text-muted">{{ $ambiente->descripcion_ambiente ?: 'Sin descripcion' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('coordinator.clinica.contenido', ['id_ambiente' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-outline-primary btn-sm" title="Ver contenido del ambiente">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Ver Contenido
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No se encontraron ambientes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    @if(session('info'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Informacion',
                text: '{{ session('info') }}'
            });
        </script>
    @endif
@endpush
