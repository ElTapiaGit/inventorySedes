@extends('layouts.encargado')

@section('title', 'Componentes y Accesorios del Equipo')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Encabezado de Pagina -->
    <div class="encargado-page-header">
        <div>
            <h2>
                <i class="bi bi-cpu text-primary me-2"></i>Componentes y Accesorios
            </h2>
            <p class="text-muted mb-0">
                Equipo: <strong class="text-dark">{{ $equipo->nombre_equi }}</strong> 
                (Código: <span class="badge bg-primary">{{ $equipo->cod_equipo }}</span> 
                @if($equipo->marca || $equipo->modelo)
                    | {{ $equipo->marca }} {{ $equipo->modelo }}
                @endif
                @if($equipo->ambiente)
                    | <i class="bi bi-geo-alt"></i> {{ $equipo->ambiente->nombre }}
                @endif
                )
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('equipo.create') }}" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Equipo
            </a>
            <a href="{{ route('encargado.inicio') }}" class="btn btn-outline-secondary">
                <i class="bi bi-check-circle me-1"></i> Finalizar y Volver
            </a>
        </div>
    </div>

    <!-- Alertas de Validacion del Servidor -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <h6 class="mb-0 fw-bold">Por favor corrija los siguientes errores:</h6>
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <!-- Seccion 1: Componentes Internos del Equipo -->
    <div class="row g-4 mb-5">
        <!-- Formulario de Registro de Componente -->
        <div class="col-lg-5">
            <div class="card-articulo h-100">
                <div class="card-header">
                    <i class="bi bi-memory text-primary"></i> Registrar Componente Interno
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('componente.store') }}" method="POST" novalidate>
                        @csrf
                        <input type="hidden" name="cod_equipo" value="{{ $equipo->cod_equipo }}">

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="nombre_compo" class="form-label fw-semibold">Nombre del Componente <span class="text-danger">*</span></label>
                                <span class="char-counter" data-for="nombre_compo">0 / 50</span>
                            </div>
                            <input type="text" class="form-control @error('nombre_compo') is-invalid @enderror" id="nombre_compo" name="nombre_compo" maxlength="50" data-maxlength="50" value="{{ old('nombre_compo') }}" placeholder="Ej. Memoria RAM DDR4 16GB / SSD 512GB" required>
                            @error('nombre_compo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion_compo" class="form-label fw-semibold">Descripción Técnica <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('descripcion_compo') is-invalid @enderror" id="descripcion_compo" name="descripcion_compo" rows="3" placeholder="Detalle marca, frecuencia, interfaz (SATA/NVMe), etc." required>{{ old('descripcion_compo') }}</textarea>
                            @error('descripcion_compo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="estado_compo" class="form-label fw-semibold">Estado del Componente <span class="text-danger">*</span></label>
                            <select class="form-select @error('estado_compo') is-invalid @enderror" id="estado_compo" name="estado_compo" required>
                                <option value="Nuevo" {{ old('estado_compo', 'Bueno') == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                                <option value="Bueno" {{ old('estado_compo', 'Bueno') == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                                <option value="Regular" {{ old('estado_compo', 'Bueno') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                <option value="Para mantenimiento" {{ old('estado_compo', 'Bueno') == 'Para mantenimiento' ? 'selected' : '' }}>Para mantenimiento</option>
                                <option value="Para reparar" {{ old('estado_compo', 'Bueno') == 'Para reparar' ? 'selected' : '' }}>Para reparar</option>
                                <option value="Para descarte" {{ old('estado_compo', 'Bueno') == 'Para descarte' ? 'selected' : '' }}>Para descarte</option>
                            </select>
                            @error('estado_compo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Guardar Componente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Componentes Registrados -->
        <div class="col-lg-7">
            <div class="card-articulo h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-check text-primary"></i> Componentes Registrados en este Equipo</span>
                    <span class="badge bg-secondary">{{ $componentes->count() }} componente(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-encargado mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Componente</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($componentes as $index => $comp)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-semibold text-dark">{{ $comp->nombre_compo }}</td>
                                        <td><small class="text-muted">{{ $comp->descripcion_compo }}</small></td>
                                        <td>
                                            <span class="badge bg-success">{{ $comp->estado_compo }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                            No hay componentes registrados para este equipo todavía.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seccion 2: Accesorios Vinculados al Equipo -->
    <div class="row g-4 mb-4">
        <!-- Formulario Unificado Atomico de Accesorio -->
        <div class="col-12">
            <div class="card-articulo">
                <div class="card-header">
                    <i class="bi bi-plug text-primary"></i> Registrar y Vincular Nuevo Accesorio al Equipo
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('equipo.accesorios.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        <input type="hidden" name="cod_equipo" value="{{ $equipo->cod_equipo }}">

                        <div class="row g-4">
                            <!-- Columna Lateral: Fotografia del Accesorio -->
                            <div class="col-lg-4 col-md-5">
                                <label class="form-label fw-semibold text-secondary small">
                                    <i class="bi bi-camera me-1"></i> Fotografía del Accesorio
                                </label>

                                <!-- Contenedor de Vista Previa -->
                                <div class="preview-photo-box mb-3" id="previewBox">
                                    <img src="" alt="Vista previa del accesorio" id="photoPreview" class="preview-photo-img d-none">
                                    <div class="preview-empty-state" id="previewEmpty">
                                        <i class="bi bi-image"></i>
                                        <span>Sin fotografía seleccionada</span>
                                    </div>
                                </div>

                                <!-- Boton Limpiar Foto -->
                                <div class="mb-3 text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger d-none" id="btnLimpiarFoto">
                                        <i class="bi bi-trash3 me-1"></i> Quitar Fotografía
                                    </button>
                                </div>

                                <!-- Subir Nueva Foto -->
                                <div class="mb-3">
                                    <label for="foto" class="form-label fw-semibold">Subir Nueva Foto</label>
                                    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp">
                                    <div class="form-text text-muted">Formatos: JPG, PNG o WEBP (Máx. 5MB).</div>
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Campo Oculto para Foto Reutilizada -->
                                <input type="hidden" name="existing_foto_id" id="existing_foto_id" value="{{ old('existing_foto_id') }}">
                                @error('existing_foto_id')
                                    <div class="text-danger small mt-1 mb-2">{{ $message }}</div>
                                @enderror

                                <!-- Acceso Rapido a Ultima Foto Registrada -->
                                @if($ultimaFotoId)
                                    @php
                                        $ultimaFotoObj = $fotosReutilizables->firstWhere('id_foto', $ultimaFotoId);
                                        $urlUltima = $ultimaFotoObj ? (Str::startsWith($ultimaFotoObj->ruta_foto, 'inventario/') ? asset('storage/' . $ultimaFotoObj->ruta_foto) : asset($ultimaFotoObj->ruta_foto)) : '';
                                    @endphp
                                    @if($urlUltima)
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary w-100" id="btnUsarUltimaFoto" data-foto-id="{{ $ultimaFotoId }}" data-foto-url="{{ $urlUltima }}">
                                                <i class="bi bi-clock-history me-1"></i> Usar última foto (#{{ $ultimaFotoId }})
                                            </button>
                                        </div>
                                    @endif
                                @endif

                                <!-- Galeria de Fotos de Accesorios Existentes -->
                                @if(isset($fotosReutilizables) && $fotosReutilizables->count() > 0)
                                    <div class="mt-3">
                                        <label class="form-label fw-semibold text-secondary small d-flex justify-content-between align-items-center mb-1">
                                            <span><i class="bi bi-images me-1"></i> Fotos de accesorios</span>
                                            <span class="badge bg-light text-dark border">{{ $fotosReutilizables->count() }}</span>
                                        </label>
                                        <div class="reusable-gallery-scroll">
                                            <div class="row g-2">
                                                @foreach($fotosReutilizables as $fr)
                                                    @php
                                                        $urlFoto = Str::startsWith($fr->ruta_foto, 'inventario/') ? asset('storage/' . $fr->ruta_foto) : asset($fr->ruta_foto);
                                                    @endphp
                                                    <div class="col-4">
                                                        <div class="reusable-thumb-item {{ old('existing_foto_id') == $fr->id_foto ? 'selected' : '' }}" data-foto-id="{{ $fr->id_foto }}" data-foto-url="{{ $urlFoto }}" title="Foto #{{ $fr->id_foto }}">
                                                            <img src="{{ $urlFoto }}" alt="Foto #{{ $fr->id_foto }}" loading="lazy">
                                                            <span class="thumb-check-badge"><i class="bi bi-check"></i></span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Columna de Datos del Accesorio -->
                            <div class="col-lg-8 col-md-7">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="cod_accesorio" class="form-label fw-semibold">Código del Accesorio <span class="text-danger">*</span></label>
                                            <span class="char-counter" data-for="cod_accesorio">0 / 50</span>
                                        </div>
                                        <input type="text" class="form-control @error('cod_accesorio') is-invalid @enderror" id="cod_accesorio" name="cod_accesorio" maxlength="50" data-maxlength="50" value="{{ old('cod_accesorio') }}" placeholder="Ej. ACC-{{ $equipo->cod_equipo }}-01" required>
                                        @error('cod_accesorio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="nombre_acce" class="form-label fw-semibold">Nombre del Accesorio <span class="text-danger">*</span></label>
                                            <span class="char-counter" data-for="nombre_acce">0 / 80</span>
                                        </div>
                                        <input type="text" class="form-control @error('nombre_acce') is-invalid @enderror" id="nombre_acce" name="nombre_acce" maxlength="80" data-maxlength="80" value="{{ old('nombre_acce') }}" placeholder="Ej. Mouse USB Dell / Teclado Mecánico" required>
                                        @error('nombre_acce')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="descripcion_acce" class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('descripcion_acce') is-invalid @enderror" id="descripcion_acce" name="descripcion_acce" rows="2" placeholder="Detalle marca, modelo, conector..." required>{{ old('descripcion_acce') }}</textarea>
                                        @error('descripcion_acce')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="observacion_ace" class="form-label fw-semibold">Observación <small class="text-muted fw-normal">(Opcional)</small></label>
                                        <textarea class="form-control @error('observacion_ace') is-invalid @enderror" id="observacion_ace" name="observacion_ace" rows="2" placeholder="Notas sobre el estado físico o complementos (opcional)">{{ old('observacion_ace') }}</textarea>
                                        @error('observacion_ace')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="estado_acce" class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                                        <select class="form-select @error('estado_acce') is-invalid @enderror" id="estado_acce" name="estado_acce" required>
                                            <option value="Nuevo" {{ old('estado_acce', 'Bueno') == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                                            <option value="Bueno" {{ old('estado_acce', 'Bueno') == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                                            <option value="Regular" {{ old('estado_acce', 'Bueno') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                            <option value="Para reponer" {{ old('estado_acce', 'Bueno') == 'Para reponer' ? 'selected' : '' }}>Para reponer</option>
                                            <option value="Para cambiar" {{ old('estado_acce', 'Bueno') == 'Para cambiar' ? 'selected' : '' }}>Para cambiar</option>
                                        </select>
                                        @error('estado_acce')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="vida_util" class="form-label fw-semibold">Vida Útil <small class="text-muted fw-normal">(Opcional)</small></label>
                                            <span class="char-counter" data-for="vida_util">0 / 45</span>
                                        </div>
                                        <input type="text" class="form-control @error('vida_util') is-invalid @enderror" id="vida_util" name="vida_util" maxlength="45" data-maxlength="45" value="{{ old('vida_util') }}" placeholder="Ej. 3 años">
                                        @error('vida_util')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="ubicacion" class="form-label fw-semibold">Ubicación <span class="text-danger">*</span></label>
                                            <span class="char-counter" data-for="ubicacion">0 / 120</span>
                                        </div>
                                        <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" id="ubicacion" name="ubicacion" maxlength="120" data-maxlength="120" value="{{ old('ubicacion', 'Junto al equipo ' . $equipo->cod_equipo) }}" required>
                                        @error('ubicacion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-save me-1"></i> Registrar Accesorio
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Accesorios Vinculados -->
        <div class="col-12">
            <div class="card-articulo">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-link-45deg text-primary"></i> Accesorios Vinculados a este Equipo</span>
                    <span class="badge bg-secondary">{{ $equipo->accesorios->count() }} accesorio(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-encargado mb-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                    <th>Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($equipo->accesorios as $acc)
                                    <tr>
                                        <td><span class="badge bg-light text-dark border">{{ $acc->cod_accesorio }}</span></td>
                                        <td class="fw-semibold text-dark">{{ $acc->nombre_acce }}</td>
                                        <td><small class="text-muted">{{ $acc->descripcion_acce ?? '-' }}</small></td>
                                        <td><small>{{ $acc->ubicacion }}</small></td>
                                        <td><span class="badge bg-success">{{ $acc->estado_acce }}</span></td>
                                        <td>
                                            @if($acc->foto)
                                                @php
                                                    $urlFotoAcc = Str::startsWith($acc->foto->ruta_foto, 'inventario/') ? asset('storage/' . $acc->foto->ruta_foto) : asset($acc->foto->ruta_foto);
                                                @endphp
                                                <img src="{{ $urlFotoAcc }}" alt="{{ $acc->nombre_acce }}" class="rounded border" width="40" height="40" style="object-fit: cover;">
                                            @else
                                                <span class="text-muted small">Sin foto</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                            No hay accesorios vinculados a este equipo todavía.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/encargado/registro-articulo.js') }}"></script>

@if(session('aviso'))
    <script>
        Swal.fire({
            icon: 'info',
            text: '{{ session('aviso') }}',
            showConfirmButton: false,
            timer: 1800
        });
    </script>
@endif

@if(session('info'))
    <script>
        Swal.fire({
            icon: 'info',
            title: 'Atención',
            text: '{{ session('info') }}'
        });
    </script>
@endif

@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '{{ session('success') }}'
        });
    </script>
@endif

@if(session('errordata'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('errordata') }}'
        });
    </script>
@endif
@endpush
