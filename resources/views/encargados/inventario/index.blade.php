@extends('layouts.encargado')

@section('title', 'Consulta de Inventario')

@section('content')
<div class="container-fluid px-3 py-2">
    <!-- Migas de pan -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 py-2 px-3 bg-white rounded-3 shadow-sm border">
            <li class="breadcrumb-item">
                <a href="{{ route('encargado.inicio') }}" class="text-decoration-none" style="color: #1B4B82;">
                    <i class="bi bi-house-door me-1"></i> Inicio
                </a>
            </li>
            <li class="breadcrumb-item active text-muted" aria-current="page">
                <i class="bi bi-box-seam me-1"></i> Consulta de Inventario
            </li>
        </ol>
    </nav>

    <!-- Encabezado de Pagina -->
    <div class="encargado-page-header">
        <div>
            <h1>
                <i class="bi bi-box-seam text-primary"></i> Inventario del Edificio
            </h1>
            <p class="text-muted mb-0 small mt-1">
                <i class="bi bi-geo-alt me-1 text-primary"></i>
                <strong>{{ $edificio->nombre_edi ?? 'Edificio Asignado' }}</strong>
                @if($edificio && $edificio->sede)
                    <span class="text-muted ms-1">| Sede: {{ $edificio->sede->nombre_sede ?? '' }}</span>
                @endif
                <span class="badge bg-light text-dark border ms-2">Aislamiento Territorial Activo</span>
            </p>
        </div>

        <!-- Accion Rapida: Registrar Nuevo Articulo -->
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle shadow-sm px-3" type="button" id="dropdownRegistrarArticulo" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #1B4B82; border-color: #1B4B82;">
                <i class="bi bi-plus-circle me-1"></i> Registrar Articulo
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownRegistrarArticulo">
                <li>
                    <a class="dropdown-item py-2" href="{{ route('equipo.create') }}">
                        <i class="bi bi-pc-display me-2 text-primary"></i> Nuevo Equipo
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('mobiliario.create') }}">
                        <i class="bi bi-chair me-2 text-primary"></i> Nuevo Mobiliario
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('material.create') }}">
                        <i class="bi bi-tools me-2 text-primary"></i> Nuevo Material
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('accesorio.create') }}">
                        <i class="bi bi-plug me-2 text-primary"></i> Nuevo Accesorio
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Alertas de Sesion -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert" style="border-left: 4px solid #198754 !important;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert" style="border-left: 4px solid #dc3545 !important;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <!-- Pestanas de Categoria (Tabs) -->
    <ul class="nav nav-pills mb-3 bg-white p-2 rounded-3 shadow-sm border" id="pills-tab" role="tablist">
        <li class="nav-item me-1" role="presentation">
            <a href="{{ route('encargado.inventario.index', ['tab' => 'equipos']) }}"
               class="nav-link px-3 py-2 fw-semibold {{ $tab === 'equipos' ? 'active text-white' : 'text-dark' }}"
               style="{{ $tab === 'equipos' ? 'background-color: #1B4B82;' : '' }}">
                <i class="bi bi-pc-display me-1"></i> Equipos
                <span class="badge {{ $tab === 'equipos' ? 'bg-white text-dark' : 'bg-light text-muted border' }} ms-1">
                    {{ $countEquipos }}
                </span>
            </a>
        </li>
        <li class="nav-item me-1" role="presentation">
            <a href="{{ route('encargado.inventario.index', ['tab' => 'mobiliarios']) }}"
               class="nav-link px-3 py-2 fw-semibold {{ $tab === 'mobiliarios' ? 'active text-white' : 'text-dark' }}"
               style="{{ $tab === 'mobiliarios' ? 'background-color: #1B4B82;' : '' }}">
                <i class="bi bi-chair me-1"></i> Mobiliarios
                <span class="badge {{ $tab === 'mobiliarios' ? 'bg-white text-dark' : 'bg-light text-muted border' }} ms-1">
                    {{ $countMobiliarios }}
                </span>
            </a>
        </li>
        <li class="nav-item me-1" role="presentation">
            <a href="{{ route('encargado.inventario.index', ['tab' => 'materiales']) }}"
               class="nav-link px-3 py-2 fw-semibold {{ $tab === 'materiales' ? 'active text-white' : 'text-dark' }}"
               style="{{ $tab === 'materiales' ? 'background-color: #1B4B82;' : '' }}">
                <i class="bi bi-tools me-1"></i> Materiales
                <span class="badge {{ $tab === 'materiales' ? 'bg-white text-dark' : 'bg-light text-muted border' }} ms-1">
                    {{ $countMateriales }}
                </span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('encargado.inventario.index', ['tab' => 'accesorios']) }}"
               class="nav-link px-3 py-2 fw-semibold {{ $tab === 'accesorios' ? 'active text-white' : 'text-dark' }}"
               style="{{ $tab === 'accesorios' ? 'background-color: #1B4B82;' : '' }}">
                <i class="bi bi-plug me-1"></i> Accesorios
                <span class="badge {{ $tab === 'accesorios' ? 'bg-white text-dark' : 'bg-light text-muted border' }} ms-1">
                    {{ $countAccesorios }}
                </span>
            </a>
        </li>
    </ul>

    <!-- Barra de Filtros Compacta -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3 bg-white rounded-3">
            <form method="GET" action="{{ route('encargado.inventario.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <!-- Buscador de texto -->
                <div class="{{ $tab === 'accesorios' ? 'col-md-7 col-lg-8' : 'col-md-4 col-lg-5' }}">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text"
                               name="q"
                               value="{{ request('q') }}"
                               class="form-control border-start-0"
                               placeholder="{{ $tab === 'accesorios' ? 'Buscar por codigo, nombre, descripcion o ubicacion...' : 'Buscar por codigo, nombre, marca o modelo...' }}"
                               aria-label="Buscar">
                    </div>
                </div>

                <!-- Filtro por Ambiente (Solo para categorias ubicadas en ambiente) -->
                @if($tab !== 'accesorios')
                    <div class="col-md-4 col-lg-3">
                        <select name="ambiente_id" class="form-select">
                            <option value="">Todos los Ambientes</option>
                            @foreach($ambientes as $amb)
                                <option value="{{ $amb->id_ambiente }}" {{ request('ambiente_id') == $amb->id_ambiente ? 'selected' : '' }}>
                                    {{ $amb->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filtro por Estado -->
                <div class="{{ $tab === 'accesorios' ? 'col-md-3 col-lg-2' : 'col-md-2 col-lg-2' }}">
                    <select name="estado" class="form-select">
                        <option value="">Todos los Estados</option>
                        <option value="Bueno" {{ request('estado') === 'Bueno' ? 'selected' : '' }}>Bueno</option>
                        <option value="Regular" {{ request('estado') === 'Regular' ? 'selected' : '' }}>Regular</option>
                        <option value="Malo" {{ request('estado') === 'Malo' ? 'selected' : '' }}>Malo</option>
                        <option value="En Mantenimiento" {{ request('estado') === 'En Mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                    </select>
                </div>

                <!-- Botones de Accion del Filtro -->
                <div class="col-md-2 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: #1B4B82; border-color: #1B4B82;">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                    @if(request()->filled('q') || request()->filled('ambiente_id') || request()->filled('estado'))
                        <a href="{{ route('encargado.inventario.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Articulos -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            @if($items->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                    <h5 class="text-muted fw-semibold">No se encontraron articulos en esta seccion</h5>
                    <p class="text-muted small mb-3">No hay registros que coincidan con los criterios de busqueda o el inventario esta vacio.</p>
                    @if(request()->filled('q') || request()->filled('ambiente_id') || request()->filled('estado'))
                        <a href="{{ route('encargado.inventario.index', ['tab' => $tab]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-clockwise me-1"></i> Restablecer Filtros
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-encargado align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 70px;" class="text-center">Foto</th>
                                <th style="width: 140px;">Codigo</th>
                                <th>Descripcion / Caracteristicas</th>
                                @if($tab === 'accesorios')
                                    <th>Ubicacion / Vinculacion</th>
                                @else
                                    <th>Ambiente</th>
                                @endif
                                <th style="width: 130px;" class="text-center">Estado</th>
                                <th style="width: 160px;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @php
                                    // Variables dinamicas segun pestana
                                    if ($tab === 'equipos') {
                                        $codigo = $item->cod_equipo;
                                        $nombrePrincipal = $item->nombre_equi;
                                        $subtitulo = trim(($item->marca ?? '') . ' ' . ($item->modelo ?? ''));
                                        $ambienteNombre = $item->ambiente->nombre ?? 'Sin ambiente';
                                        $estadoValor = $item->estado_equi ?? 'No definido';
                                        $tipoSingular = 'equipo';
                                        $tipoEtiqueta = 'Equipo';
                                        $cambiarFotoAction = route('equipo.cambiarFoto', $item->cod_equipo);
                                    } elseif ($tab === 'mobiliarios') {
                                        $codigo = $item->cod_mueble;
                                        $nombrePrincipal = $item->tipoMobiliario->tipo_mueble ?? 'Mobiliario';
                                        $subtitulo = $item->descripticion_mueb ?? '';
                                        $ambienteNombre = $item->ambiente->nombre ?? 'Sin ambiente';
                                        $estadoValor = $item->estado_mueb ?? 'No definido';
                                        $tipoSingular = 'mobiliario';
                                        $tipoEtiqueta = 'Mobiliario';
                                        $cambiarFotoAction = route('mobiliario.cambiarFoto', $item->cod_mueble);
                                    } elseif ($tab === 'materiales') {
                                        $codigo = $item->cod_mate;
                                        $nombrePrincipal = $item->tipo_mate ?? 'Material';
                                        $subtitulo = $item->descripcion_mate ?? '';
                                        $ambienteNombre = $item->ambiente->nombre ?? 'Sin ambiente';
                                        $estadoValor = $item->estado_mate ?? 'No definido';
                                        $tipoSingular = 'material';
                                        $tipoEtiqueta = 'Material';
                                        $cambiarFotoAction = route('material.cambiarFoto', $item->cod_mate);
                                    } else {
                                        $codigo = $item->cod_accesorio;
                                        $nombrePrincipal = $item->nombre_acce ?? 'Accesorio';
                                        $subtitulo = $item->descripcion_acce ?? '';
                                        $ambienteNombre = $item->ubicacion ?? 'Ubicacion no asignada';
                                        $estadoValor = $item->estado_acce ?? 'No definido';
                                        $tipoSingular = 'accesorio';
                                        $tipoEtiqueta = 'Accesorio';
                                        $cambiarFotoAction = route('accesorio.cambiarFoto', $item->cod_accesorio);
                                    }

                                    $fotoUrl = inventario_img_url($item->foto->ruta_foto ?? null);

                                    // Badge color segun estado
                                    $badgeClass = 'bg-secondary';
                                    if (in_array(strtolower($estadoValor), ['bueno', 'operativo'])) {
                                        $badgeClass = 'bg-success';
                                    } elseif (strtolower($estadoValor) === 'regular') {
                                        $badgeClass = 'bg-warning text-dark';
                                    } elseif (strtolower($estadoValor) === 'malo') {
                                        $badgeClass = 'bg-danger';
                                    } elseif (str_contains(strtolower($estadoValor), 'mantenimiento')) {
                                        $badgeClass = 'bg-info text-dark';
                                    }
                                @endphp
                                <tr>
                                    <!-- Miniatura de Foto -->
                                    <td class="text-center py-2">
                                        <img src="{{ $fotoUrl }}"
                                             alt="{{ $nombrePrincipal }}"
                                             class="rounded-3 border shadow-sm"
                                             style="width: 48px; height: 48px; object-fit: cover; background-color: #f8fafc;"
                                             loading="lazy">
                                    </td>

                                    <!-- Codigo -->
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace fw-semibold fs-6">
                                            {{ $codigo }}
                                        </span>
                                    </td>

                                    <!-- Descripcion y Atributos -->
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 320px;">
                                            {{ $nombrePrincipal }}
                                        </div>
                                        @if($subtitulo)
                                            <div class="small text-muted text-truncate" style="max-width: 340px;">
                                                {{ $subtitulo }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Ubicacion o Ambiente -->
                                    <td>
                                        @if($tab === 'accesorios')
                                            @if($item->equipos && $item->equipos->isNotEmpty())
                                                <div class="small">
                                                    <span class="badge bg-light text-primary border">
                                                        <i class="bi bi-link-45deg me-1"></i>
                                                        Vinculado a {{ $item->equipos->count() }} equipo(s)
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-muted small">
                                                    <i class="bi bi-geo-alt me-1"></i> {{ $item->ubicacion ?: 'Sin ubicacion' }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-dark small">
                                                <i class="bi bi-door-open me-1 text-primary"></i> {{ $ambienteNombre }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Estado -->
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }} px-2 py-1">
                                            {{ $estadoValor }}
                                        </span>
                                    </td>

                                    <!-- Botones de Accion -->
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('encargado.inventario.show', [$tipoSingular, $codigo]) }}"
                                               class="btn btn-sm btn-outline-primary shadow-sm"
                                               title="Ver Ficha Detallada">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary shadow-sm btn-cambiar-foto"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalCambiarFotoArticulo"
                                                    data-action="{{ $cambiarFotoAction }}"
                                                    data-codigo="{{ $codigo }}"
                                                    data-nombre="{{ $nombrePrincipal }}"
                                                    data-tipo="{{ $tipoEtiqueta }}"
                                                    data-foto-actual="{{ $fotoUrl }}"
                                                    title="Cambiar Fotografia">
                                                <i class="bi bi-camera"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginacion y Resumen -->
                <div class="card-footer bg-white border-0 py-3 px-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <small class="text-muted">
                        Mostrando del <strong>{{ $items->firstItem() ?? 0 }}</strong> al <strong>{{ $items->lastItem() ?? 0 }}</strong> de <strong>{{ $items->total() }}</strong> registros
                    </small>
                    <div>
                        {{ $items->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Universal de Cambio de Foto -->
@include('encargados.inventario.modal_cambiar_foto')

@endsection
