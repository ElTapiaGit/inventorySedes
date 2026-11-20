@extends('layouts.admin')

@section('title', 'Sedes y Edificios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/sedeAdmin.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagSede">  
    <div class="container-fluid">
        <!-- Encabezado de la seccion -->
        <div class="mb-4">
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-building me-2 text-primary"></i>Sedes y Edificios
            </h2>
            <p class="text-muted small mb-0">Administre las sedes universitarias, sus edificios asignados y vinculacion con carreras.</p>
        </div>

        <!-- Seccion Sedes -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                    <i class="bi bi-geo-alt me-2 text-primary"></i>Listado de Sedes
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="row g-3 align-items-start">
                    <div class="col-lg-7">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-ulat align-middle mb-0">
                                <thead class="table-ulat-header">
                                    <tr>
                                        <th>Sede</th>
                                        <th class="text-center" style="width: 120px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sedes as $sede)
                                        <tr>
                                            <td class="fw-semibold text-dark">{{ $sede->nombre }}</td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-primary" title="Editar Sede" data-bs-toggle="modal" data-bs-target="#modalEditarSede" data-id="{{ $sede->id_sede }}" data-nombre="{{ $sede->nombre }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Eliminar Sede" onclick="eliminarSede({{ $sede->id_sede }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-3">No hay sedes registradas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2">Registrar Nueva Sede</h6>
                            <form action="{{ route('sedes.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="nombreSedeNueva" class="form-label small fw-semibold">Nombre de la Sede <span class="text-danger">*</span></label>
                                    <input type="text" id="nombreSedeNueva" name="nombre" maxlength="50" class="form-control @error('nombre') is-invalid @enderror" 
                                           placeholder="Ej: Campus Central" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Agregar Sede</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seccion Edificios -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                    <i class="bi bi-building-check me-2 text-primary"></i>Listado de Edificios
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    <button id="btnAgregarEdificio" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-plus-circle"></i>
                        <span>Agregar Edificio</span>
                    </button>
                    <button id="btnAgregarPiso" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-layers"></i>
                        <span>Agregar Pisos</span>
                    </button>
                    <a href="{{ route('pisos.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-layers-half"></i>
                        <span>Detalles Pisos</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th>Edificio</th>
                                <th>Carrera Asignada</th>
                                <th class="text-center" style="width: 140px;">Niveles / Pisos</th>
                                <th>Direccion</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($edificios as $edificio)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $edificio->nombre_edi }}</td>
                                    <td>
                                        @if($edificio->carrera)
                                            <span class="badge bg-primary text-white">
                                                <i class="bi bi-mortarboard me-1"></i>{{ $edificio->carrera->nombre_carrera }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white">
                                                <i class="bi bi-diagram-3 me-1"></i>Compartido
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">
                                            {{ $edificio->pisos_count }} niveles
                                        </span>
                                    </td>
                                    <td>{{ $edificio->direccion }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary" title="Editar Edificio" data-bs-toggle="modal" data-bs-target="#modalEditarEdificio" data-id="{{ $edificio->id_edificio }}" data-nombre="{{ $edificio->nombre_edi }}" data-direccion="{{ $edificio->direccion }}" data-carrera="{{ $edificio->CARRERA_id_carrera ?? '' }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" title="Eliminar Edificio" onclick="eliminarEdificio('{{ $edificio->id_edificio }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No hay edificios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal para editar sede -->
        <div class="modal fade" id="modalEditarSede" tabindex="-1" aria-labelledby="modalEditarSedeLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #1B4B82;">
                        <h5 class="modal-title fw-bold" id="modalEditarSedeLabel">
                            <i class="bi bi-pencil-square me-2"></i>Editar Sede
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="formEditarSede" action="{{ old('edit_action') ?? '' }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id_sede" id="editIdSede">
                            <div class="mb-3">
                                <label for="editNombreSede" class="form-label fw-semibold">Nombre de la Sede <span class="text-danger">*</span></label>
                                <input type="text" name="nombredit" class="form-control @error('nombredit') is-invalid @enderror" id="editNombreSede" value="{{ old('nombredit') }}" required>
                                @error('nombredit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para agregar edificio -->
        <div id="modalAgregarEdificio" class="modal fade" tabindex="-1" aria-labelledby="modalAgregarEdificioLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #1B4B82;">
                        <h5 class="modal-title fw-bold" id="modalAgregarEdificioLabel">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Edificio
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="formAgregarEdificio" action="{{ route('edificios.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombreEdificio" class="form-label fw-semibold">Nombre del Edificio <span class="text-danger">*</span></label>
                                <input type="text" name="nombre_edi" class="form-control" id="nombreEdificio" required>
                            </div>
                            <div class="mb-3">
                                <label for="direccion" class="form-label fw-semibold">Direccion <span class="text-danger">*</span></label>
                                <input type="text" name="direccion" class="form-control" id="direccion" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombreSede" class="form-label fw-semibold">Nombre de la Sede <span class="text-danger">*</span></label>
                                <select name="SEDE_id_sede" class="form-select" id="nombreSede" required>
                                    @foreach($sedes as $sede)
                                        <option value="{{ $sede->id_sede }}">{{ $sede->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="carreraEdificio" class="form-label fw-semibold">Carrera Asignada:</label>
                                <select name="CARRERA_id_carrera" class="form-select" id="carreraEdificio">
                                    <option value="">Compartido / Institucional (Ninguna)</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre_carrera }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Si el edificio es exclusivo de una carrera seleccionela; de lo contrario dejelo en Compartido.</div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar edificio -->
        <div id="modalEditarEdificio" class="modal fade" tabindex="-1" aria-labelledby="modalEditarEdificioLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #1B4B82;">
                        <h5 class="modal-title fw-bold" id="modalEditarEdificioLabel">
                            <i class="bi bi-pencil-square me-2"></i>Editar Edificio
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="formEditarEdificio" action="" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editIdEdificio" name="id_edificio">
                            <div class="mb-3">
                                <label for="editNombreEdificio" class="form-label fw-semibold">Nombre del Edificio <span class="text-danger">*</span></label>
                                <input type="text" id="editNombreEdificio" name="nombre_edi" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="editDireccion" class="form-label fw-semibold">Direccion <span class="text-danger">*</span></label>
                                <input type="text" id="editDireccion" name="direccion" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="editCarreraEdificio" class="form-label fw-semibold">Carrera Asignada:</label>
                                <select name="CARRERA_id_carrera" class="form-select" id="editCarreraEdificio">
                                    <option value="">Compartido / Institucional (Ninguna)</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre_carrera }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para agregar pisos -->
        <div id="modalAgregarPiso" class="modal fade" tabindex="-1" aria-labelledby="modalAgregarPisoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #1B4B82;">
                        <h5 class="modal-title fw-bold" id="modalAgregarPisoLabel">
                            <i class="bi bi-layers me-2"></i>Agregar Pisos
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="formAgregarPiso" action="{{ route('pisos.store') }}" method="POST">
                            @csrf
                            <div id="pisosContainer">
                                <div class="piso-item mb-3 p-3 bg-light rounded border">
                                    <div class="mb-2">
                                        <label for="numero_piso_0" class="form-label fw-semibold">Nombre de Piso <span class="text-danger">*</span></label>
                                        <input type="text" id="numero_piso_0" name="numero_piso[]" class="form-control" placeholder="Ej: Piso 1" required>
                                    </div>
                                    <div>
                                        <label for="edificio_piso_0" class="form-label fw-semibold">Edificio <span class="text-danger">*</span></label>
                                        <select id="edificio_piso_0" name="Edificio_id_edificio[]" class="form-select" required>
                                            @foreach($edificios as $edificio)
                                                <option value="{{ $edificio->id_edificio }}">{{ $edificio->nombre_edi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button type="button" id="btnAgregarOtroPiso" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-plus"></i>
                                    <span>Agregar Otro Piso</span>
                                </button>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Pisos</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Exito',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>
    @endif

    <!-- Scripts para edicion y eliminacion de sedes -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalEditarSede = document.getElementById('modalEditarSede');
            if (modalEditarSede) {
                modalEditarSede.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var id = button.getAttribute('data-id');
                    var nombre = button.getAttribute('data-nombre');
                    
                    var inputId = document.getElementById('editIdSede');
                    var inputNombre = document.getElementById('editNombreSede');
                    
                    inputId.value = id;
                    inputNombre.value = nombre;

                    var formEditarSede = document.getElementById('formEditarSede');
                    formEditarSede.action = "{{ url('administrator/sedes') }}/" + id;
                });
            }

            window.eliminarSede = function(id) {
                Swal.fire({
                    title: '¿Esta seguro?',
                    text: 'No podra revertir esto.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = document.createElement('form');
                        form.action = '{{ route('sedes.destroy', '') }}/' + id;
                        form.method = 'POST';
                        form.innerHTML = '@csrf @method("DELETE")';
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            };
        });
    </script>

    <!-- Scripts para edicion y eliminacion de edificios -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalEditarEdificio = document.getElementById('modalEditarEdificio');
            if (modalEditarEdificio) {
                modalEditarEdificio.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var id = button.getAttribute('data-id');
                    var nombre = button.getAttribute('data-nombre');
                    var direccion = button.getAttribute('data-direccion');
                    var carrera = button.getAttribute('data-carrera');

                    var form = document.getElementById('formEditarEdificio');
                    form.action = '{{ route('edificios.update', '') }}/' + id;

                    document.getElementById('editIdEdificio').value = id;
                    document.getElementById('editNombreEdificio').value = nombre;
                    document.getElementById('editDireccion').value = direccion;
                    document.getElementById('editCarreraEdificio').value = carrera || '';
                });
            }

            var btnAgregarEdificio = document.getElementById('btnAgregarEdificio');
            if (btnAgregarEdificio) {
                btnAgregarEdificio.addEventListener('click', function() {
                    var modal = new bootstrap.Modal(document.getElementById('modalAgregarEdificio'));
                    modal.show();
                });
            }

            window.eliminarEdificio = function(id) {
                Swal.fire({
                    title: '¿Esta seguro?',
                    text: 'No podra revertir esto.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = document.createElement('form');
                        form.action = '{{ route('edificios.destroy', '') }}/' + id;
                        form.method = 'POST';
                        form.innerHTML = '@csrf @method("DELETE")';
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            };
        });
    </script>

    <!-- Script para agregar pisos -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btnAgregarPiso = document.getElementById('btnAgregarPiso');
            if (btnAgregarPiso) {
                btnAgregarPiso.addEventListener('click', function() {
                    var modal = new bootstrap.Modal(document.getElementById('modalAgregarPiso'));
                    modal.show();
                });
            }

            var btnAgregarOtroPiso = document.getElementById('btnAgregarOtroPiso');
            if (btnAgregarOtroPiso) {
                btnAgregarOtroPiso.addEventListener('click', function() {
                    var pisosContainer = document.getElementById('pisosContainer');
                    var pisoItem = document.querySelector('.piso-item').cloneNode(true);
                    pisoItem.querySelector('input').value = '';
                    pisosContainer.appendChild(pisoItem);
                });
            }
        });
    </script>

    @if(session('errordata'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Error',
                    text: '{{ session('errordata') }}',
                    showConfirmButton: true,
                });
            });
        </script>
    @endif
@endpush
