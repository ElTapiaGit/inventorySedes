@extends('layouts.encargado')

@php
    $tabMap = [
        'equipo' => 'equipos',
        'mobiliario' => 'mobiliarios',
        'material' => 'materiales',
        'accesorio' => 'accesorios',
    ];
    $tabPlural = $tabMap[$tipo] ?? 'equipos';

    if ($tipo === 'equipo') {
        $codigo = $articulo->cod_equipo;
        $nombre = $articulo->nombre_equi;
        $estado = $articulo->estado_equi ?? 'No definido';
        $tipoLabel = 'Equipo';
        $ambienteInfo = $articulo->ambiente->nombre ?? 'Sin ambiente';
        $pisoInfo = $articulo->ambiente->piso->numero_piso ?? null;
    } elseif ($tipo === 'mobiliario') {
        $codigo = $articulo->cod_mueble;
        $nombre = $articulo->tipoMobiliario->tipo_mueble ?? 'Mobiliario';
        $estado = $articulo->estado_mueb ?? 'No definido';
        $tipoLabel = 'Mobiliario';
        $ambienteInfo = $articulo->ambiente->nombre ?? 'Sin ambiente';
        $pisoInfo = $articulo->ambiente->piso->numero_piso ?? null;
    } elseif ($tipo === 'material') {
        $codigo = $articulo->cod_mate;
        $nombre = $articulo->tipo_mate ?? 'Material';
        $estado = $articulo->estado_mate ?? 'No definido';
        $tipoLabel = 'Material';
        $ambienteInfo = $articulo->ambiente->nombre ?? 'Sin ambiente';
        $pisoInfo = $articulo->ambiente->piso->numero_piso ?? null;
    } else {
        $codigo = $articulo->cod_accesorio;
        $nombre = $articulo->nombre_acce ?? 'Accesorio';
        $estado = $articulo->estado_acce ?? 'No definido';
        $tipoLabel = 'Accesorio';
        $ambienteInfo = $articulo->ubicacion ?: 'No especificada';
        $pisoInfo = null;
    }

    $fotoUrl = inventario_img_url($articulo->foto->ruta_foto ?? null);

    $badgeClass = 'bg-secondary';
    if (in_array(strtolower($estado), ['bueno', 'operativo'])) {
        $badgeClass = 'bg-success';
    } elseif (strtolower($estado) === 'regular') {
        $badgeClass = 'bg-warning text-dark';
    } elseif (strtolower($estado) === 'malo') {
        $badgeClass = 'bg-danger';
    } elseif (str_contains(strtolower($estado), 'mantenimiento')) {
        $badgeClass = 'bg-info text-dark';
    }
@endphp

@section('title', 'Ficha Tecnica: ' . $codigo)

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
            <li class="breadcrumb-item">
                <a href="{{ route('encargado.inventario.index', ['tab' => $tabPlural]) }}" class="text-decoration-none" style="color: #1B4B82;">
                    <i class="bi bi-box-seam me-1"></i> Inventario
                </a>
            </li>
            <li class="breadcrumb-item active text-muted" aria-current="page">
                Ficha Tecnica: {{ $codigo }}
            </li>
        </ol>
    </nav>

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

    <div class="row g-4">
        <!-- Columna Izquierda: Fotografia Oficial y Acciones -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                        <i class="bi bi-image me-2 text-primary"></i> Fotografia Oficial
                    </h5>
                </div>
                <div class="card-body p-3 text-center">
                    <div class="border rounded-3 p-2 bg-light mb-3 d-flex align-items-center justify-content-center" style="min-height: 260px; max-height: 320px; overflow: hidden;">
                        <img src="{{ $fotoUrl }}"
                             alt="{{ $nombre }}"
                             class="img-fluid rounded-2 shadow-sm"
                             style="max-height: 290px; object-fit: contain;">
                    </div>

                    <div class="small text-muted mb-3 d-flex justify-content-between px-2">
                        <span><i class="bi bi-hash me-1"></i> ID Foto: <strong>{{ $articulo->FOTO_id_foto ?? 'N/A' }}</strong></span>
                        <span><i class="bi bi-tag me-1"></i> {{ $tipoLabel }}</span>
                    </div>

                    <!-- Boton Universal: Cambiar Fotografia -->
                    <button type="button"
                            class="btn btn-primary w-100 py-2 shadow-sm fw-semibold mb-2 btn-cambiar-foto"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCambiarFotoArticulo"
                            data-action="{{ $cambiarFotoRoute }}"
                            data-codigo="{{ $codigo }}"
                            data-nombre="{{ $nombre }}"
                            data-tipo="{{ $tipoLabel }}"
                            data-foto-actual="{{ $fotoUrl }}"
                            style="background-color: #1B4B82; border-color: #1B4B82;">
                        <i class="bi bi-camera me-1"></i> Cambiar Fotografia
                    </button>

                    @if($tipo === 'equipo')
                        <!-- Enlace directo para agregar componentes o accesorios -->
                        <a href="{{ route('equipo.show', $articulo->cod_equipo) }}"
                           class="btn btn-outline-primary w-100 py-2 shadow-sm fw-semibold mb-2">
                            <i class="bi bi-cpu me-1"></i> Agregar Componentes / Accesorios
                        </a>
                    @endif

                    <a href="{{ route('encargado.inventario.index', ['tab' => $tabPlural]) }}"
                       class="btn btn-outline-secondary w-100 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <!-- Informacion de Ubicacion Territorial -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-geo-alt me-2 text-primary"></i> Asignacion Territorial
                    </h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <span class="text-muted d-block">Edificio:</span>
                            <strong>{{ $edificio->nombre_edi ?? 'Edificio Asignado' }}</strong>
                        </li>
                        @if($edificio && $edificio->sede)
                            <li class="mb-2">
                                <span class="text-muted d-block">Sede:</span>
                                <strong>{{ $edificio->sede->nombre_sede ?? 'No especificada' }}</strong>
                            </li>
                        @endif
                        @if($pisoInfo !== null)
                            <li class="mb-2">
                                <span class="text-muted d-block">Piso:</span>
                                <strong>Piso {{ $pisoInfo }}</strong>
                            </li>
                        @endif
                        <li>
                            <span class="text-muted d-block">Ambiente / Ubicacion:</span>
                            <span class="badge bg-light text-primary border fs-6 mt-1">{{ $ambienteInfo }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Especificaciones Tecnicas y Componentes -->
        <div class="col-lg-8">
            <!-- Ficha Tecnica Principal -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <span class="badge bg-light text-dark border font-monospace me-2 fs-6">{{ $codigo }}</span>
                        <span class="badge {{ $badgeClass }} fs-6">{{ $estado }}</span>
                    </div>
                    <span class="text-muted small">
                        <i class="bi bi-shield-check me-1 text-success"></i> Articulo Registrado
                    </span>
                </div>
                <div class="card-body p-4">
                    <h4 class="fw-bold text-dark mb-4">
                        {{ $nombre }}
                    </h4>

                    <div class="row g-3">
                        @if($tipo === 'equipo')
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Nombre del Equipo</div>
                                <div class="fw-semibold text-dark">{{ $articulo->nombre_equi }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Estado Fisico</div>
                                <div><span class="badge {{ $badgeClass }}">{{ $articulo->estado_equi }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Marca</div>
                                <div class="text-dark">{{ $articulo->marca ?: 'No especificada' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Modelo</div>
                                <div class="text-dark">{{ $articulo->modelo ?: 'No especificado' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Vida Util Estimada</div>
                                <div class="text-dark">
                                    {{ $articulo->vida_util ? $articulo->vida_util . ' ano(s)' : 'No especificada' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Ambiente Asignado</div>
                                <div class="text-dark">
                                    <i class="bi bi-door-open text-primary me-1"></i> {{ $articulo->ambiente->nombre ?? 'Sin ambiente' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Descripcion</div>
                                <div class="p-3 bg-light rounded-2 text-dark">
                                    {{ $articulo->descripcion_equi ?: 'Sin descripcion registrada.' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Observaciones</div>
                                <div class="p-3 bg-light rounded-2 text-dark">
                                    {{ $articulo->observaciones_equi ?: 'Sin observaciones registradas.' }}
                                </div>
                            </div>

                        @elseif($tipo === 'mobiliario')
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Tipo de Mobiliario</div>
                                <div class="fw-semibold text-dark">{{ $articulo->tipoMobiliario->tipo_mueble ?? 'No especificado' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Estado Fisico</div>
                                <div><span class="badge {{ $badgeClass }}">{{ $articulo->estado_mueb }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Vida Util Estimada</div>
                                <div class="text-dark">
                                    {{ $articulo->vida_util ? $articulo->vida_util . ' ano(s)' : 'No especificada' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Ambiente Asignado</div>
                                <div class="text-dark">
                                    <i class="bi bi-door-open text-primary me-1"></i> {{ $articulo->ambiente->nombre ?? 'Sin ambiente' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Descripcion</div>
                                <div class="p-3 bg-light rounded-2 text-dark">
                                    {{ $articulo->descripticion_mueb ?: 'Sin descripcion registrada.' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Observaciones</div>
                                <div class="p-3 bg-light rounded-2 text-dark">
                                    {{ $articulo->observacion ?: 'Sin observaciones registradas.' }}
                                </div>
                            </div>

                        @elseif($tipo === 'material')
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Tipo de Material</div>
                                <div class="fw-semibold text-dark">{{ $articulo->tipo_mate }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Estado Fisico</div>
                                <div><span class="badge {{ $badgeClass }}">{{ $articulo->estado_mate }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Ambiente Asignado</div>
                                <div class="text-dark">
                                    <i class="bi bi-door-open text-primary me-1"></i> {{ $articulo->ambiente->nombre ?? 'Sin ambiente' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Descripcion</div>
                                <div class="p-3 bg-light rounded-2 text-dark">
                                    {{ $articulo->descripcion_mate ?: 'Sin descripcion registrada.' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Observaciones</div>
                                <div class="p-3 bg-light rounded-2 text-dark">
                                    {{ $articulo->observacion_mate ?: 'Sin observaciones registradas.' }}
                                </div>
                            </div>

                        @elseif($tipo === 'accesorio')
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Nombre del Accesorio</div>
                                <div class="fw-semibold text-dark">{{ $articulo->nombre_acce }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Estado Fisico</div>
                                <div><span class="badge {{ $badgeClass }}">{{ $articulo->estado_acce }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Ubicacion</div>
                                <div class="text-dark">
                                    <i class="bi bi-geo-alt text-primary me-1"></i> {{ $articulo->ubicacion ?: 'No especificada' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Vida Util Estimada</div>
                                <div class="text-dark">
                                    {{ $articulo->vida_util ? $articulo->vida_util . ' ano(s)' : 'No especificada' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Descripcion</div>
                                <div class="p-3 bg-light rounded-2 text-dark">
                                    {{ $articulo->descripcion_acce ?: 'Sin descripcion registrada.' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Observaciones</div>
                                <div class="p-3 bg-light rounded-2 text-dark">
                                    {{ $articulo->observacion_ace ?: 'Sin observaciones registradas.' }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tablas Relacionales segun Tipo -->
            @if($tipo === 'equipo')
                <!-- Componentes Asociados al Equipo -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="bi bi-cpu me-2 text-primary"></i> Componentes Integrados
                            <span class="badge bg-light text-primary border ms-1">{{ $articulo->componentes->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @if($articulo->componentes->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-info-circle fs-3 d-block mb-1"></i>
                                Este equipo no tiene componentes internos registrados.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-encargado mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Componente</th>
                                            <th>Descripcion</th>
                                            <th class="text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($articulo->componentes as $comp)
                                            <tr>
                                                <td class="fw-semibold text-dark">{{ $comp->nombre_compo }}</td>
                                                <td class="small text-muted">{{ $comp->descripcion_compo ?: 'Sin descripcion' }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark border">{{ $comp->estado_compo ?: 'Bueno' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Accesorios Vinculados al Equipo -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="bi bi-plug me-2 text-primary"></i> Accesorios Vinculados
                            <span class="badge bg-light text-primary border ms-1">{{ $articulo->accesorios->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @if($articulo->accesorios->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-info-circle fs-3 d-block mb-1"></i>
                                Este equipo no tiene accesorios externos vinculados.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-encargado mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;" class="text-center">Foto</th>
                                            <th>Codigo</th>
                                            <th>Accesorio</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center" style="width: 100px;">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($articulo->accesorios as $acc)
                                            <tr>
                                                <td class="text-center">
                                                    <img src="{{ inventario_img_url($acc->foto->ruta_foto ?? null) }}"
                                                         alt="{{ $acc->nombre_acce }}"
                                                         class="rounded border shadow-sm"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                </td>
                                                <td class="font-monospace small fw-semibold">{{ $acc->cod_accesorio }}</td>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $acc->nombre_acce }}</div>
                                                    <small class="text-muted">{{ Str::limit($acc->descripcion_acce, 40) }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark border">{{ $acc->estado_acce ?: 'Bueno' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('encargado.inventario.show', ['accesorio', $acc->cod_accesorio]) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Ver Ficha del Accesorio">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            @elseif($tipo === 'accesorio')
                <!-- Equipos a los que pertenece el Accesorio -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="bi bi-pc-display me-2 text-primary"></i> Equipos Vinculados
                            <span class="badge bg-light text-primary border ms-1">{{ $articulo->equipos->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @if($articulo->equipos->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-info-circle fs-3 d-block mb-1"></i>
                                Este accesorio es independiente o se encuentra libre (no esta vinculado a ningun equipo especifico).
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-encargado mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Equipo</th>
                                            <th>Ambiente</th>
                                            <th class="text-center" style="width: 100px;">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($articulo->equipos as $eq)
                                            <tr>
                                                <td class="font-monospace fw-semibold">{{ $eq->cod_equipo }}</td>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $eq->nombre_equi }}</div>
                                                    <small class="text-muted">{{ trim(($eq->marca ?? '') . ' ' . ($eq->modelo ?? '')) }}</small>
                                                </td>
                                                <td class="small">
                                                    <i class="bi bi-door-open text-primary me-1"></i>
                                                    {{ $eq->ambiente->nombre ?? 'Sin ambiente' }}
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('encargado.inventario.show', ['equipo', $eq->cod_equipo]) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Ver Ficha del Equipo">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Universal de Cambio de Foto -->
@include('encargados.inventario.modal_cambiar_foto')

@endsection
