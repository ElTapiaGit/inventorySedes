@extends('layouts.admin')

@section('title', 'Tipos de Ambientes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/tipoyAmbiente.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagTipoAmbiente">  
    <div class="container-fluid">
        <!-- Cabecera de seccion con boton Volver -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Tipos de Ambientes
                </h2>
                <p class="text-muted small mb-0">Gestion de clasificaciones y categorias de ambientes.</p>
            </div>
            <a href="{{ route('ambiente.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Volver a Ambientes</span>
            </a>
        </div>

        <div class="row g-4">
            <!-- Tabla de Tipos de Ambiente -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-list-ul me-2 text-primary"></i>Listado de Tipos de Ambiente
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-ulat align-middle mb-0">
                                <thead class="table-ulat-header">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th>Tipo de Ambiente</th>
                                        <th class="text-center" style="width: 120px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $contador = 1;
                                    @endphp
                                    @forelse ($tipoAmbientes as $tipoAmbiente)
                                        <tr>
                                            <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                            <td class="fw-semibold text-dark">{{ $tipoAmbiente->nombre_amb }}</td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-primary" title="Editar" data-bs-toggle="modal" data-bs-target="#editTipoAmbienteModal" data-id="{{ $tipoAmbiente->id_tipoambiente }}" data-nombre="{{ $tipoAmbiente->nombre_amb }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Eliminar" onclick="eliminarTipoAmbiente('{{ Crypt::encryptString($tipoAmbiente->id_tipoambiente) }}')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">No hay tipos de ambientes registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario para Agregar Tipo de Ambiente -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-plus-circle me-2 text-primary"></i>Agregar Tipo de Ambiente
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('tipoambiente.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="tipo" class="form-label fw-semibold">Nombre del Tipo de Ambiente <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tipo" name="tipo" placeholder="Ej: Laboratorio, Aula, Auditorio" required>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-save"></i>
                                    <span>Guardar Tipo</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Editar Tipo de Ambiente -->
        <div class="modal fade" id="editTipoAmbienteModal" tabindex="-1" aria-labelledby="editTipoAmbienteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <form id="editTipoAmbienteForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header text-white" style="background-color: #1B4B82;">
                            <h5 class="modal-title fw-bold" id="editTipoAmbienteModalLabel">
                                <i class="bi bi-pencil-square me-2"></i>Editar Tipo de Ambiente
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="editTipo" class="form-label fw-semibold">Tipo de Ambiente <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editTipo" name="tipo" required>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalEditarTipoAmbiente = document.getElementById('editTipoAmbienteModal');
            if (modalEditarTipoAmbiente) {
                modalEditarTipoAmbiente.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var id = button.getAttribute('data-id');
                    var nombre = button.getAttribute('data-nombre');

                    var inputTipo = document.getElementById('editTipo');
                    inputTipo.value = nombre;

                    var formEditarTipoAmbiente = document.getElementById('editTipoAmbienteForm');
                    formEditarTipoAmbiente.action = "{{ route('tipoambiente.update', '') }}/" + id;
                });
            }
        });

        window.eliminarTipoAmbiente = function(id) {
            Swal.fire({
                title: '¿Esta seguro?',
                text: "Este tipo de ambiente se eliminara permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = document.createElement('form');
                    form.action = '{{ route('tipoambiente.destroy', '') }}/' + id;
                    form.method = 'POST';
                    form.innerHTML = '@csrf @method("DELETE")';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        };
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
                icon: 'info',
                title: 'Registro ya existente',
                text: '{{ session('errorregister') }}',
            });
        </script>
    @endif
@endpush
