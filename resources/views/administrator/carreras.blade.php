@extends('layouts.admin')

@section('title', 'Gestion de Carreras')

@push('styles')
    <style>
        .carreras-card {
            border: 0;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .table-carreras thead th {
            background-color: #1B4B82 !important;
            color: #ffffff !important;
            font-weight: 600;
            vertical-align: middle;
            border: none;
        }
        .logo-carrera-thumb {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 0.375rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 2px;
        }
    </style>
@endpush

@section('content')
<main class="content px-3 py-4">
    <div class="container-fluid">
        <!-- Encabezado de seccion -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-mortarboard me-2 text-primary"></i>Gestion de Carreras
                </h2>
                <p class="text-muted small mb-0">Administre las carreras universitarias, sus identidades institucionales y vinculaciones.</p>
            </div>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 mt-2 mt-sm-0" data-bs-toggle="modal" data-bs-target="#modalCrearCarrera">
                <i class="bi bi-plus-circle"></i>
                <span>Nueva Carrera</span>
            </button>
        </div>

        <!-- Tarjeta principal con tabla de carreras -->
        <div class="card carreras-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-carreras align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">#</th>
                                <th class="text-center" style="width: 80px;">Logo</th>
                                <th>Carrera</th>
                                <th class="text-center" style="width: 100px;">Codigo</th>
                                <th class="text-center" style="width: 110px;">Edificios</th>
                                <th class="text-center" style="width: 110px;">Ambientes</th>
                                <th class="text-center" style="width: 110px;">Personal</th>
                                <th class="text-center" style="width: 140px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($carreras as $index => $carrera)
                                @php
                                    $logoUrl = asset('img/logo.webp');
                                    if (!empty($carrera->logo)) {
                                        if (file_exists(public_path($carrera->logo))) {
                                            $logoUrl = asset($carrera->logo);
                                        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($carrera->logo)) {
                                            $logoUrl = asset('storage/' . $carrera->logo);
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                                    <td class="text-center">
                                        <img src="{{ $logoUrl }}" alt="Logo {{ $carrera->nombre_carrera }}" class="logo-carrera-thumb" onerror="this.onerror=null;this.src='{{ asset('img/logo.webp') }}';">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $carrera->nombre_carrera }}</div>
                                        <div class="small text-muted">ID: {{ $carrera->id_carrera }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($carrera->codigo)
                                            <span class="badge bg-secondary px-2 py-1">{{ $carrera->codigo }}</span>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1" title="Edificios asignados">
                                            <i class="bi bi-building me-1 text-primary"></i>{{ $carrera->edificios_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1" title="Ambientes asignados">
                                            <i class="bi bi-door-open me-1 text-success"></i>{{ $carrera->ambientes_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1" title="Personal asignado">
                                            <i class="bi bi-people me-1 text-info"></i>{{ $carrera->personal_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" title="Editar Carrera" 
                                                    data-bs-toggle="modal" data-bs-target="#modalEditarCarrera"
                                                    data-id="{{ $carrera->id_carrera }}"
                                                    data-nombre="{{ $carrera->nombre_carrera }}"
                                                    data-codigo="{{ $carrera->codigo ?? '' }}"
                                                    data-logo="{{ $logoUrl }}"
                                                    data-update-url="{{ route('admin.carreras.update', $carrera->id_carrera) }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Eliminar Carrera"
                                                    onclick="confirmarEliminarCarrera({{ $carrera->id_carrera }}, '{{ addslashes($carrera->nombre_carrera) }}', {{ $carrera->edificios_count + $carrera->ambientes_count + $carrera->personal_count }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle me-1"></i>No hay carreras registradas en el sistema.
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

<!-- Modal Crear Carrera -->
<div class="modal fade" id="modalCrearCarrera" tabindex="-1" aria-labelledby="modalCrearCarreraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.carreras.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalCrearCarreraLabel">
                        <i class="bi bi-plus-circle me-2"></i>Registrar Nueva Carrera
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="crear_nombre_carrera" class="form-label fw-semibold">Nombre de la Carrera <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombre_carrera') is-invalid @enderror" 
                               id="crear_nombre_carrera" name="nombre_carrera" value="{{ old('nombre_carrera') }}" 
                               maxlength="60" required placeholder="Ej. Odontologia, Optometria">
                        @error('nombre_carrera')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="crear_codigo" class="form-label fw-semibold">Codigo / Sigla</label>
                        <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                               id="crear_codigo" name="codigo" value="{{ old('codigo') }}" 
                               maxlength="10" placeholder="Ej. ODO, OPT">
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Identificador corto institucional (opcional).</div>
                    </div>

                    <div class="mb-3">
                        <label for="crear_logo" class="form-label fw-semibold">Logo Institucional</label>
                        <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                               id="crear_logo" name="logo" accept="image/png,image/jpeg,image/jpg,image/webp">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Formatos permitidos: PNG, JPG, JPEG, WEBP. Tamano maximo: 2MB.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <i class="bi bi-save"></i>
                        <span>Guardar Carrera</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Carrera (Dinamico, Fuera del Foreach) -->
<div class="modal fade" id="modalEditarCarrera" tabindex="-1" aria-labelledby="modalEditarCarreraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditarCarrera" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalEditarCarreraLabel">
                        <i class="bi bi-pencil-square me-2"></i>Editar Carrera
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_nombre_carrera" class="form-label fw-semibold">Nombre de la Carrera <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nombre_carrera" name="nombre_carrera" maxlength="60" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_codigo" class="form-label fw-semibold">Codigo / Sigla</label>
                        <input type="text" class="form-control" id="edit_codigo" name="codigo" maxlength="10">
                        <div class="form-text">Identificador corto institucional (opcional).</div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_logo" class="form-label fw-semibold">Reemplazar Logo (Opcional)</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img id="edit_logo_preview" src="{{ asset('img/logo.webp') }}" alt="Vista previa logo" class="logo-carrera-thumb" style="width: 50px; height: 50px;">
                            <span class="small text-muted">Logo actual cargado en el sistema.</span>
                        </div>
                        <input type="file" class="form-control" id="edit_logo" name="logo" accept="image/png,image/jpeg,image/jpg,image/webp">
                        <div class="form-text">Deje este campo vacio si no desea cambiar el logo actual.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <i class="bi bi-save"></i>
                        <span>Actualizar Carrera</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulario invisible para eliminacion segura -->
<form id="formEliminarCarrera" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuracion del modal dinamico de edicion
        const modalEditar = document.getElementById('modalEditarCarrera');
        if (modalEditar) {
            modalEditar.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (!button) return;

                const id = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                const codigo = button.getAttribute('data-codigo');
                const logo = button.getAttribute('data-logo');
                const updateUrl = button.getAttribute('data-update-url');

                const form = document.getElementById('formEditarCarrera');
                form.action = updateUrl;

                document.getElementById('edit_nombre_carrera').value = nombre || '';
                document.getElementById('edit_codigo').value = codigo || '';

                const preview = document.getElementById('edit_logo_preview');
                if (preview) {
                    preview.src = logo || '{{ asset('img/logo.webp') }}';
                }
            });
        }
    });

    // Confirmacion y bloqueo con SweetAlert2 para eliminacion
    function confirmarEliminarCarrera(id, nombre, dependencias) {
        if (dependencias > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Eliminacion Bloqueada',
                text: 'La carrera "' + nombre + '" cuenta con ' + dependencias + ' registro(s) dependientes (edificios, ambientes o personal). Reasigne o desvincule esos registros antes de intentar eliminarla.',
                confirmButtonColor: '#1B4B82',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        Swal.fire({
            title: '¿Confirmar eliminacion?',
            text: 'Se eliminara la carrera "' + nombre + '". Esta accion no se puede deshacer.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Si, eliminar carrera',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formEliminarCarrera');
                form.action = '{{ url('administrator/carreras') }}/' + id;
                form.submit();
            }
        });
    }
</script>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Operacion Exitosa!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>
@endif

@if(session('errordata'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Aviso',
                text: '{{ session('errordata') }}',
                confirmButtonColor: '#1B4B82'
            });
        });
    </script>
@endif

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Errores de Validacion',
                html: '<ul class="text-start mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#1B4B82'
            });
        });
    </script>
@endif
@endpush
