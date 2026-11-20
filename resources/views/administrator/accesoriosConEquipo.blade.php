@extends('layouts.admin')

@section('title', 'Accesorios con Equipos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/accesorios.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagAccesEquip">
    <div class="container-fluid">
        <!-- Cabecera de seccion -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-pc-display-horizontal me-2 text-primary"></i>Accesorios con Equipos
                </h2>
                <p class="text-muted small mb-0">Listado de accesorios asociados y emparejados a equipos informaticos y biomedicos.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('accesoriosadmin.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>Reposicion</span>
                </a>
                <a href="{{ route('accesoriosadmin.unicos') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-cpu"></i>
                    <span>Accesorios Unicos</span>
                </a>
                <a href="{{ route('accesoriosadmin.conEquipo') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-pc-display"></i>
                    <span>Con Equipos</span>
                </a>
            </div>
        </div>

        <!-- Formulario de busqueda -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
            <form action="{{ route('accesoriosadmin.conEquipo') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="codigoConEq" class="form-label small fw-semibold text-muted mb-1">Codigo de Accesorio:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-hash"></i></span>
                            <input type="text" name="codigo" id="codigoConEq" placeholder="Buscar por Codigo" class="form-control" value="{{ request('codigo') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-5">
                        <label for="nombreConEq" class="form-label small fw-semibold text-muted mb-1">Nombre de Accesorio:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-search"></i></span>
                            <input type="text" name="nombre" id="nombreConEq" placeholder="Buscar por Nombre" class="form-control" value="{{ request('nombre') }}">
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
        @if($accesorios->count() > 0)
            <div class="card border-0 shadow-sm rounded-3">
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
                                    <th>Equipo Asignado</th>
                                    <th class="text-center" style="width: 80px;">Detalles</th>
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
                                        <td>{{ $accesorio->fch_registro_acce ? \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            @foreach($accesorio->equipos as $equipo)
                                                <span class="badge bg-light text-dark border d-inline-block me-1 mb-1">
                                                    <i class="bi bi-pc-display me-1 text-primary"></i>{{ $equipo->cod_equipo }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('accesoriosadmin.detalles', ['codigo' => Crypt::encrypt($accesorio->cod_accesorio)]) }}" class="btn btn-sm btn-outline-primary" title="Ver Ficha Tecnica">
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
                    {{ $accesorios->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5 text-center text-muted">
                    <i class="bi bi-search fs-1 d-block mb-2 text-secondary"></i>
                    No se encontraron accesorios con equipos que coincidan con los criterios de busqueda.
                </div>
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    @if($accesorios->count() === 0 && (request('codigo') || request('nombre')))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin resultados',
                text: 'No se encontraron accesorios con equipos que coincidan con los criterios de busqueda.',
            });
        </script>
    @endif
@endpush
