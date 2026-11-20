@extends('layouts.admin')

@section('title', 'Accesorios Unicos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/accesorios.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagAccesUnico">
    <div class="container-fluid">
        <!-- Cabecera de seccion -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-cpu me-2 text-primary"></i>Accesorios Unicos
                </h2>
                <p class="text-muted small mb-0">Listado general y busqueda de accesorios no vinculados permanentemente a un unico equipo.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('accesoriosadmin.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>Reposicion</span>
                </a>
                <a href="{{ route('accesoriosadmin.unicos') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-cpu"></i>
                    <span>Accesorios Unicos</span>
                </a>
                <a href="{{ route('accesoriosadmin.conEquipo') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-pc-display"></i>
                    <span>Con Equipos</span>
                </a>
            </div>
        </div>

        <!-- Formulario de busqueda -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
            <form method="GET" action="{{ route('accesoriosadmin.unicos') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="buscarCodigo" class="form-label small fw-semibold text-muted mb-1">Codigo de Accesorio:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-hash"></i></span>
                            <input type="text" id="buscarCodigo" name="codigo" class="form-control" placeholder="Buscar por Codigo" value="{{ request('codigo') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-5">
                        <label for="buscarNombre" class="form-label small fw-semibold text-muted mb-1">Nombre de Accesorio:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscarNombre" name="nombre" class="form-control" placeholder="Buscar por Nombre" value="{{ request('nombre') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-search"></i>
                            <span>Buscar</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de accesorios -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Descripcion</th>
                                <th>Observacion</th>
                                <th class="text-center">Estado</th>
                                <th>Vida Util</th>
                                <th>Ubicacion</th>
                                <th>Fecha Registro</th>
                                <th class="text-center" style="width: 80px;">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accesorios as $accesorio)
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
                                    <td>{{ $accesorio->fch_registro_acce ? \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('accesoriosadmin.detalles', ['codigo' => Crypt::encrypt($accesorio->cod_accesorio)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>No hay accesorios unicos registrados que coincidan con la busqueda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-center">
                {{ $accesorios->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
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
