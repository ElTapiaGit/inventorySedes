@extends('layouts.admin')

@section('title', 'Pisos de Edificios')

@section('content')
<main class="content px-3 pagPisos">  
    <div class="container-fluid">
        <h2 class="text-center fw-bold m-4">Registro  de Pisos</h2>
    
        <!-- Formulario para seleccionar edificio -->
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <form method="GET" action="{{ route('pisos.index') }}">
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                            <select name="edificio_id" id="selectEdificio" class="form-select" aria-label="Seleccione un edificio">
                                <option value="">Todos los Edificios</option>
                                @foreach($edificios as $edificio)
                                    <option value="{{ Crypt::encrypt($edificio->id_edificio) }}" {{ request('edificio_id') == Crypt::encrypt($edificio->id_edificio) ? 'selected' : '' }}>
                                        {{ $edificio->nombre_edi }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Mostrar Pisos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Tabla de pisos -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nros.</th>
                        <th>Nombre Piso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if($pisos->count() > 0)
                        @foreach($pisos as $index => $piso)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $piso->numero_piso }}</td>
                                <td>
                                    <!-- Botón para abrir el modal de edición -->
                                    <button type="button" class="btn btn-sm btn-warning edit-btn" title="Editar" data-id="{{ $piso->id_piso }}" data-numero="{{ $piso->numero_piso }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <!-- Botón para eliminar piso -->
                                    <form action="{{ route('pisos.destroy', $piso->id_piso) }}" id="delete-form-{{ $piso->id_piso }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" title="Eliminar" onclick="confirmDelete({{ $piso->id_piso }})">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center">No hay pisos disponibles.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>


        <!-- Modal de edición -->
        <div class="modal fade" id="editPisoModal" tabindex="-1" aria-labelledby="editPisoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPisoModalLabel">Editar Piso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editPisoForm" method="POST" action="{{ route('pisos.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editPisoId" name="piso_id">
                            <div class="mb-3">
                                <label for="editNumeroPiso" class="form-label">Nombre Piso</label>
                                <input type="text" class="form-control" id="editNumeroPiso" name="numero_piso" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- script para editar -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const editBtns = document.querySelectorAll('.edit-btn');
            
                editBtns.forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const numeroPiso = this.getAttribute('data-numero');
            
                        // Configura el formulario del modal con los datos del piso
                        document.getElementById('editPisoId').value = id;
                        document.getElementById('editNumeroPiso').value = numeroPiso;
            
                        // Muestra el modal
                        new bootstrap.Modal(document.getElementById('editPisoModal')).show();
                    });
                });
            });
        </script>
        <!-- script para eliminar piso -->
        <script>
            function confirmDelete(pisoId) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás deshacer esta acción!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si se confirma, envía el formulario
                        document.getElementById('delete-form-' + pisoId).submit();
                    }
                });
            }
        </script>
    </div>
    <!--mensaje de exito en editar-->    
    @if(session('successregister'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Exito!',
                text: '{{ session('successregister') }}',
            });
        </script>
    @endif

    <!--mensaje de error con la base de datos-->    
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Problemas!!!',
                text: '{{ session('error') }}',
            });
        </script>
    @endif
        
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(request('edificio_id') && $pisos->count() === 0)
        Swal.fire({
            icon: 'info',
            title: 'Información',
            text: 'No hay pisos disponibles para este edificio.'
        });
        @endif
    });
</script>
@endsection
