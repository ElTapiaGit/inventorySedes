@extends('layouts.encargado')

@section('title', 'Registro de Materiales')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Encabezado de Pagina -->
    <div class="encargado-page-header">
        <div>
            <h2><i class="bi bi-tools text-primary me-2"></i>Registro de Materiales</h2>
            <p class="text-muted mb-0">Registre materiales, consumibles o herramientas asignadas a los laboratorios.</p>
        </div>
        <div>
            <a href="{{ route('encargado.inicio') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver al Inicio
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

    <!-- Formulario Unificado Atomico -->
    <form action="{{ route('material.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="row g-4">
            <!-- Columna Lateral: Fotografia del Material -->
            <div class="col-lg-4 col-md-5">
                <div class="card-articulo">
                    <div class="card-header">
                        <i class="bi bi-camera text-primary"></i> Fotografía del Material
                    </div>
                    <div class="card-body p-3">
                        <!-- Contenedor de Vista Previa -->
                        <div class="preview-photo-box mb-3" id="previewBox">
                            <img src="" alt="Vista previa del material" id="photoPreview" class="preview-photo-img d-none">
                            <div class="preview-empty-state" id="previewEmpty">
                                <i class="bi bi-image"></i>
                                <span>Sin fotografía seleccionada</span>
                            </div>
                        </div>

                        <!-- Boton para Limpiar / Descartar Foto -->
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
                                        <i class="bi bi-clock-history me-1"></i> Usar última foto registrada (#{{ $ultimaFotoId }})
                                    </button>
                                </div>
                            @endif
                        @endif

                        <!-- Galeria de Fotos de Materiales Existentes -->
                        @if(isset($fotosReutilizables) && $fotosReutilizables->count() > 0)
                            <div class="mt-3">
                                <div class="form-label fw-semibold text-secondary small d-flex justify-content-between align-items-center mb-1">
                                    <span><i class="bi bi-images me-1"></i> Fotos de materiales existentes</span>
                                    <span class="badge bg-light text-dark border">{{ $fotosReutilizables->count() }}</span>
                                </div>
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
                </div>
            </div>

            <!-- Columna Principal: Datos y Ubicacion -->
            <div class="col-lg-8 col-md-7">
                <!-- Seccion 1: Identificacion del Material -->
                <div class="card-articulo mb-4">
                    <div class="card-header">
                        <i class="bi bi-info-circle text-primary"></i> Identificación del Material
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="cod_mate" class="form-label fw-semibold">Código Material <span class="text-danger">*</span></label>
                                    <span class="char-counter" data-for="cod_mate">0 / 50</span>
                                </div>
                                <input type="text" class="form-control @error('cod_mate') is-invalid @enderror" id="cod_mate" name="cod_mate" maxlength="50" data-maxlength="50" value="{{ old('cod_mate') }}" placeholder="Ej. MAT-HERR-001" required>
                                @error('cod_mate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="tipo_mate" class="form-label fw-semibold">Tipo de Material <span class="text-danger">*</span></label>
                                    <span class="char-counter" data-for="tipo_mate">0 / 50</span>
                                </div>
                                <input type="text" class="form-control @error('tipo_mate') is-invalid @enderror" id="tipo_mate" name="tipo_mate" maxlength="50" data-maxlength="50" value="{{ old('tipo_mate') }}" placeholder="Ej. Herramienta / Redes / Químico" required>
                                @error('tipo_mate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="descripcion_mate" class="form-label fw-semibold">Descripción del Material <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('descripcion_mate') is-invalid @enderror" id="descripcion_mate" name="descripcion_mate" rows="3" placeholder="Detalle especificaciones, cantidad, componentes del kit, etc." required>{{ old('descripcion_mate') }}</textarea>
                                @error('descripcion_mate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="observacion_mate" class="form-label fw-semibold">Observaciones <small class="text-muted fw-normal">(Opcional)</small></label>
                                <textarea class="form-control @error('observacion_mate') is-invalid @enderror" id="observacion_mate" name="observacion_mate" rows="2" placeholder="Anotaciones adicionales o advertencias de uso (opcional)">{{ old('observacion_mate') }}</textarea>
                                @error('observacion_mate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seccion 2: Ubicacion y Estado -->
                <div class="card-articulo mb-4">
                    <div class="card-header">
                        <i class="bi bi-geo-alt text-primary"></i> Ubicación y Asignación
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="AMBIENTE_id_ambiente" class="form-label fw-semibold">Ambiente / Laboratorio <span class="text-danger">*</span></label>
                                <select class="form-select @error('AMBIENTE_id_ambiente') is-invalid @enderror" id="AMBIENTE_id_ambiente" name="AMBIENTE_id_ambiente" required>
                                    <option value="">-- Seleccionar Ambiente --</option>
                                    @foreach($ambientes as $ambiente)
                                        <option value="{{ $ambiente->id_ambiente }}" {{ old('AMBIENTE_id_ambiente') == $ambiente->id_ambiente ? 'selected' : '' }}>
                                            {{ $ambiente->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('AMBIENTE_id_ambiente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="estado_mate" class="form-label fw-semibold">Estado del Material <span class="text-danger">*</span></label>
                                <select class="form-select @error('estado_mate') is-invalid @enderror" id="estado_mate" name="estado_mate" required>
                                    <option value="Nuevo" {{ old('estado_mate', 'Bueno') == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                                    <option value="Bueno" {{ old('estado_mate', 'Bueno') == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                                    <option value="Regular" {{ old('estado_mate', 'Bueno') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="Para mantenimiento" {{ old('estado_mate', 'Bueno') == 'Para mantenimiento' ? 'selected' : '' }}>Para mantenimiento</option>
                                    <option value="Para reparar" {{ old('estado_mate', 'Bueno') == 'Para reparar' ? 'selected' : '' }}>Para reparar</option>
                                    <option value="Para descarte" {{ old('estado_mate', 'Bueno') == 'Para descarte' ? 'selected' : '' }}>Para descarte</option>
                                </select>
                                @error('estado_mate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Accion -->
                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('encargado.inicio') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Registrar Material
                    </button>
                </div>
            </div>
        </div>
    </form>
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
