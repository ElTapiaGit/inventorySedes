@extends('layouts.admin')

@section('title', 'Gestion de Ambientes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/tipoyAmbiente.css') }}">
@endpush

<!-- Dropdown de Sedes en la Navbar -->
@section('sede-dropdown')
    <li class="nav-item dropdown px-1">
        <a class="nav-link dropdown-toggle text-white" href="#" id="sedeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-geo-alt me-1"></i>Sedes
        </a>
        <ul class="dropdown-menu shadow" aria-labelledby="sedeDropdown">
            <li class="p-2">
                <form method="GET" action="{{ route('ambientes.index') }}">
                    <label for="sedeAmbNav" class="form-label small fw-semibold text-muted mb-1">Seleccionar Sede:</label>
                    <select name="sede" id="sedeAmbNav" class="form-select form-select-sm" onchange="this.form.submit()">
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
                <form method="GET" action="{{ route('ambientes.index') }}">
                    <label for="edificioAmbNav" class="form-label small fw-semibold text-muted mb-1">Seleccionar Edificio:</label>
                    <select name="edificio" id="edificioAmbNav" class="form-select form-select-sm" onchange="this.form.submit()">
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
<main class="content px-3 py-4 pagAmbcreaet">
    <div class="container-fluid">
        <!-- Cabecera de seccion -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-door-open me-2 text-primary"></i>Gestion de Ambientes
                </h2>
                <p class="text-muted small mb-0">
                    Sede: <strong>{{ $nombreSedeSeleccionada }}</strong> | Edificio: <strong>{{ $nombreEdificioSeleccionada }}</strong>
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#registroAmbienteModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nuevo Ambiente</span>
                </button>
                <a href="{{ route('ambiente.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver a Ambientes</span>
                </a>
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
                                <th>Tipo de Ambiente</th>
                                <th>Nombre</th>
                                <th>Carrera Asignada</th>
                                <th>Descripcion</th>
                                <th class="text-center" style="width: 100px;">Piso</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @forelse($ambientes as $ambiente)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                    <td>{{ $ambiente->tipo_ambiente }}</td>
                                    <td class="fw-semibold text-dark">{{ $ambiente->nombre }}</td>
                                    <td>
                                        @if($ambiente->carrera)
                                            <span class="badge bg-primary text-white">
                                                <i class="bi bi-mortarboard me-1"></i>{{ $ambiente->carrera->nombre_carrera }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white">
                                                <i class="bi bi-diagram-3 me-1"></i>Uso General
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $ambiente->descripcion_amb ?: '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $ambiente->numero_piso }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary btn-editar" title="Editar Ambiente" 
                                                    data-id="{{ $ambiente->id_ambiente }}" 
                                                    data-nombre="{{ $ambiente->nombre }}" 
                                                    data-descripcion="{{ $ambiente->descripcion_amb }}" 
                                                    data-tipo="{{ $ambiente->TIPO_AMBIENTE_id_ambiente }}" 
                                                    data-piso="{{ $ambiente->PISO_id_piso }}" 
                                                    data-carrera="{{ $ambiente->CARRERA_id_carrera ?? '' }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('ambientes.destroy', $ambiente->id_ambiente) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Eliminar Ambiente" class="btn btn-outline-danger eliminar-btn">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>No hay ambientes registrados para este edificio.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal para registrar nuevo ambiente -->
        <div class="modal fade" id="registroAmbienteModal" tabindex="-1" aria-labelledby="registroAmbienteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #1B4B82;">
                        <h5 class="modal-title fw-bold" id="registroAmbienteModalLabel">
                            <i class="bi bi-plus-circle me-2"></i>Registrar Nuevo Ambiente
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('ambientes.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="nombre_nuevo_amb" class="form-label fw-semibold">Nombre del Ambiente <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_nuevo_amb" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion_nuevo_amb" class="form-label fw-semibold">Descripcion <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="descripcion_nuevo_amb" name="descripcion" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="tipo_ambiente_nuevo" class="form-label fw-semibold">Tipo de Ambiente <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_ambiente_nuevo" name="tipo_ambiente" required>
                                    <option value="">Seleccione un tipo de ambiente</option>
                                    @foreach($tiposAmbiente as $tipo)
                                        <option value="{{ $tipo->id_tipoambiente }}">{{ $tipo->nombre_amb }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="carrera_ambiente_nuevo" class="form-label fw-semibold">Carrera Asignada</label>
                                <select class="form-select" id="carrera_ambiente_nuevo" name="CARRERA_id_carrera">
                                    <option value="">Uso General / Sin carrera especifica</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre_carrera }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Si el ambiente es de uso exclusivo para una carrera seleccionela; de lo contrario dejelo en Uso General.</div>
                            </div>
                            <div class="mb-3">
                                <label for="piso_nuevo_amb" class="form-label fw-semibold">Piso de {{ $nombreEdificioSeleccionada }} <span class="text-danger">*</span></label>
                                <select class="form-select" id="piso_nuevo_amb" name="piso" required>
                                    <option value="">Seleccione un piso</option>
                                    @foreach($pisos as $piso)
                                        <option value="{{ $piso->id_piso }}">{{ $piso->numero_piso }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 p-3 border-top">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Registrar Ambiente</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para editar ambiente -->
        <div class="modal fade" id="editarAmbienteModal" tabindex="-1" aria-labelledby="editarAmbienteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #1B4B82;">
                        <h5 class="modal-title fw-bold" id="editarAmbienteModalLabel">
                            <i class="bi bi-pencil-square me-2"></i>Editar Ambiente
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('ambientes.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <input type="hidden" id="editar_id" name="id">
                            <div class="mb-3">
                                <label for="editar_nombre" class="form-label fw-semibold">Nombre del Ambiente <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editar_nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="editar_descripcion" class="form-label fw-semibold">Descripcion <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="editar_descripcion" name="descripcion" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editar_tipo_ambiente" class="form-label fw-semibold">Tipo de Ambiente <span class="text-danger">*</span></label>
                                <select class="form-select" id="editar_tipo_ambiente" name="tipo_ambiente" required>
                                    <option value="">Seleccione un tipo de ambiente</option>
                                    @foreach($tiposAmbiente as $tipo)
                                        <option value="{{ $tipo->id_tipoambiente }}">{{ $tipo->nombre_amb }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editar_carrera" class="form-label fw-semibold">Carrera Asignada</label>
                                <select class="form-select" id="editar_carrera" name="CARRERA_id_carrera">
                                    <option value="">Uso General / Sin carrera especifica</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre_carrera }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editar_piso" class="form-label fw-semibold">Piso de {{ $nombreEdificioSeleccionada }} <span class="text-danger">*</span></label>
                                <select class="form-select" id="editar_piso" name="piso" required>
                                    <option value="">Seleccione un piso</option>
                                    @foreach($pisos as $piso)
                                        <option value="{{ $piso->id_piso }}">{{ $piso->numero_piso }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 p-3 border-top">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const descripcion = this.getAttribute('data-descripcion');
                    const tipo = this.getAttribute('data-tipo');
                    const piso = this.getAttribute('data-piso');
                    const carrera = this.getAttribute('data-carrera');
                    
                    document.getElementById('editar_id').value = id;
                    document.getElementById('editar_nombre').value = nombre;
                    document.getElementById('editar_descripcion').value = descripcion;
                    document.getElementById('editar_tipo_ambiente').value = tipo;
                    document.getElementById('editar_piso').value = piso;
                    document.getElementById('editar_carrera').value = carrera || '';
                    
                    new bootstrap.Modal(document.getElementById('editarAmbienteModal')).show();
                });
            });

            document.querySelectorAll('.eliminar-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
    
                    Swal.fire({
                        title: '¿Esta seguro?',
                        text: "Al eliminar no habra registros del ambiente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Exito',
                text: '{{ session('success') }}',
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

    @if(session('successdelete'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Exito',
                text: '{{ session('successdelete') }}',
            });
        </script>
    @endif

    @if(session('successregister'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Exito',
                text: '{{ session('successregister') }}',
            });
        </script>
    @endif

    @if(session('errorregister'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errorregister') }}',
            });
        </script>
    @endif

    @if(session('errordelete'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errordelete') }}',
            });
        </script>
    @endif
@endpush
