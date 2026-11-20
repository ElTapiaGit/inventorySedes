@extends('layouts.admin')

@section('title', 'Pisos de Edificios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/sedeAdmin.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagPisos">  
    <div class="container-fluid">
        <!-- Cabecera de seccion con boton Volver -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-layers me-2 text-primary"></i>Pisos de Edificios
                </h2>
                <p class="text-muted small mb-0">Gestion y configuracion de pisos por edificio.</p>
            </div>
            <a href="{{ route('sedes.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Volver a Sedes y Edificios</span>
            </a>
        </div>
    
        <!-- Formulario selector de edificio -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                    <form method="GET" action="{{ route('pisos.index') }}">
                        <label for="selectEdificio" class="form-label small fw-semibold text-muted mb-1">Filtrar por Edificio:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-building"></i></span>
                            <select name="edificio_id" id="selectEdificio" class="form-select" aria-label="Seleccione un edificio">
                                <option value="">Todos los Edificios</option>
                                @foreach($edificios as $edificio)
                                    <option value="{{ Crypt::encrypt($edificio->id_edificio) }}" {{ request('edificio_id') == Crypt::encrypt($edificio->id_edificio) ? 'selected' : '' }}>
                                        {{ $edificio->nombre_edi }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                                <i class="bi bi-funnel"></i>
                                <span>Mostrar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de pisos -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th class="text-center" style="width: 60px;">#</th>
                                <th>Nombre del Piso / Nivel</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pisos as $index => $piso)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                    <td class="fw-semibold text-dark">{{ $piso->numero_piso }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary edit-btn" title="Editar Piso" data-id="{{ $piso->id_piso }}" data-numero="{{ $piso->numero_piso }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('pisos.destroy', $piso->id_piso) }}" id="delete-form-{{ $piso->id_piso }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger" title="Eliminar Piso" onclick="confirmDelete({{ $piso->id_piso }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>No hay pisos disponibles para el filtro seleccionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de edicion -->
        <div class="modal fade" id="editPisoModal" tabindex="-1" aria-labelledby="editPisoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #1B4B82;">
                        <h5 class="modal-title fw-bold" id="editPisoModalLabel">
                            <i class="bi bi-pencil-square me-2"></i>Editar Piso
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editPisoForm" method="POST" action="{{ route('pisos.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editPisoId" name="piso_id">
                            <div class="mb-3">
                                <label for="editNumeroPiso" class="form-label fw-semibold">Nombre Piso <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editNumeroPiso" name="numero_piso" required>
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
    </div>
</main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editBtns = document.querySelectorAll('.edit-btn');
            editBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const numeroPiso = this.getAttribute('data-numero');
        
                    document.getElementById('editPisoId').value = id;
                    document.getElementById('editNumeroPiso').value = numeroPiso;
        
                    new bootstrap.Modal(document.getElementById('editPisoModal')).show();
                });
            });
        });

        function confirmDelete(pisoId) {
            Swal.fire({
                title: '¿Esta seguro?',
                text: "No podra deshacer esta accion.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + pisoId).submit();
                }
            });
        }
    </script>

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

    @if(session('errordata'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errordata') }}',
            });
        </script>
    @endif
@endpush
