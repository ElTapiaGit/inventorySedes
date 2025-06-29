@extends('layouts.admin')

@section('title', 'Sedes-Edificios')

@section('content')
    <main class="content px-3 pagSede">  
        <div class="container-fluid">
            <div class="mb-3">`
                    <h2 class="mb-4 fw-bold">Sedes: </h2>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Sedes</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sedes as $sede)
                                    <tr>
                                        <td>{{ $sede->nombre }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary me-2" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarSede" data-id="{{ $sede->id_sede }}" data-nombre="{{ $sede->nombre }}"><i class="bi bi-pencil"></i></button>
                                            <button class="btn btn-sm btn-danger" title="Eliminar" onclick="eliminarSede({{ $sede->id_sede }})"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('sedes.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <input type="text" name="nombre" maxlength="50" class="form-control  @error('nombre') is-invalid @enderror" 
                                        placeholder="Registrar Sede" required style="height: 40px; width: 200px;">
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary" style="height: 40px;">Agregar</button>
                            </form>
                        </div>
                    </div>
            </div>

            <!-- Mensaje de regsitro y edicion exito -->
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '{{ session('success') }}',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    });
                </script>
            @endif

            <hr>

            <!-- Modal para editar sede -->
            <div class="modal fade" id="modalEditarSede" tabindex="-1" aria-labelledby="modalEditarSedeLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarSedeLabel">Editar Sede</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Formulario de edición -->
                            <form id="formEditarSede" action="{{ old('edit_action') ?? '' }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id_sede" id="editIdSede">
                                <div class="mb-3">
                                    <label for="editNombreSede" class="form-label">Nombre de la Sede</label>
                                    <input type="text" name="nombredit" class="form-control @error('nombredit') is-invalid @enderror" id="editNombreSede" value="{{ old('nombredit') }}" required>
                                    @error('nombredit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--script para editar-->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Configurar modal de edición con los datos correctos
                    var modalEditarSede = document.getElementById('modalEditarSede');
                    modalEditarSede.addEventListener('show.bs.modal', function(event) {
                        var button = event.relatedTarget;
                        var id = button.getAttribute('data-id');
                        var nombre = button.getAttribute('data-nombre');
                        
                        var inputId = document.getElementById('editIdSede');
                        var inputNombre = document.getElementById('editNombreSede');
                        
                        inputId.value = id;
                        inputNombre.value = nombre;

                        // Establecer la acción del formulario para la edición
                        var formEditarSede = document.getElementById('formEditarSede');
                        formEditarSede.action = "{{ url('administrator/sedes') }}/" + id;
                    });
            
                    // Función para eliminar sede con SweetAlert2
                    window.eliminarSede = function(id) {
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: 'No podrás revertir esto!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, eliminar!'
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
                    }
                });
            </script>

            <!--DATOS DEL EDIFICIO-->
            <div class="mb-3">
                <h2 class="mb-4 fw-bold">Edificios: </h2>
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Edificios</th>
                                        <th>Nros. Niveles-Pisos</th>
                                        <th>Direccion</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($edificios as $edificio)
                                    <tr>
                                        <td>{{ $edificio->nombre_edi }}</td>
                                        <td>{{ $edificio->pisos_count }}</td>
                                        <td>{{ $edificio->direccion }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary me-2" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarEdificio" data-id="{{ $edificio->id_edificio }}" data-nombre="{{ $edificio->nombre_edi }}" data-direccion="{{ $edificio->direccion }}"><i class="bi bi-pencil"></i></button>
                                            <button class="btn btn-sm btn-danger" title="Eliminar" onclick="eliminarEdificio('{{ $edificio->id_edificio }}')"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <button id="btnAgregarEdificio" class="btn btn-primary" style="height: 40px;">Agregar Edificio</button>
                        </div>
                        <div class="mb-3">
                            <button id="btnAgregarPiso" class="btn btn-primary" style="height: 40px;">Agregar Pisos</button>
                        </div>
                        <div class="mb-3">
                            <a href="{{route('pisos.index')}}" class="btn btn-primary" style="height: 40px;">Detalles Pisos</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para agregar edificio -->
            <div id="modalAgregarEdificio" class="modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Agregar Edificio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formAgregarEdificio" action="{{ route('edificios.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="nombreEdificio" class="form-label">Nombre del Edificio</label>
                                    <input type="text" name="nombre_edi" class="form-control" id="nombreEdificio" required>
                                </div>
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <input type="text" name="direccion" class="form-control" id="direccion" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nombreSede" class="form-label">Nombre de la Sede:</label>
                                    <select name="SEDE_id_sede" class="form-control" id="nombreSede" required>
                                        @foreach($sedes as $sede)
                                        <option value="{{ $sede->id_sede }}">{{ $sede->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para editar edificio -->
            <div id="modalEditarEdificio" class="modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Edificio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditarEdificio" action="" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <input type="hidden" id="editIdEdificio" name="id_edificio">
                                    <label for="editNombreEdificio" class="form-label">Nombre del Edificio</label>
                                    <input type="text" id="editNombreEdificio" name="nombre_edi" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editDireccion" class="form-label">Dirección</label>
                                    <input type="text" id="editDireccion" name="direccion" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--script para editar edificio-->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Mostrar modal al hacer clic en el botón Editar
                    var modalEditarEdificio = document.getElementById('modalEditarEdificio');
                    modalEditarEdificio.addEventListener('show.bs.modal', function(event) {
                        var button = event.relatedTarget;
                        var id = button.getAttribute('data-id');
                        var nombre = button.getAttribute('data-nombre');
                        var direccion = button.getAttribute('data-direccion');

                        var form = document.getElementById('formEditarEdificio');
                        form.action = '{{ route('edificios.update', '') }}/' + id;

                        document.getElementById('editIdEdificio').value = id;
                        document.getElementById('editNombreEdificio').value = nombre;
                        document.getElementById('editDireccion').value = direccion;
                    });

                    // Mostrar modal al hacer clic en el botón Agregar Edificio
                    document.getElementById('btnAgregarEdificio').addEventListener('click', function() {
                        var modal = new bootstrap.Modal(document.getElementById('modalAgregarEdificio'));
                        modal.show();
                    });
            
                    // Función para eliminar edificio con SweetAlert2
                    window.eliminarEdificio = function(id) {
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: 'No podrás revertir esto!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, eliminar!'
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
                    }
                });
            </script>

            <!-- Modal para agregar pisos -->
            <div id="modalAgregarPiso" class="modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Agregar Pisos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formAgregarPiso" action="{{ route('pisos.store') }}" method="POST">
                                @csrf
                                <div id="pisosContainer">
                                    <div class="piso-item mb-3">
                                        <label for="nombrePiso" class="form-label">Nombre de Piso</label>
                                        <input type="text" name="numero_piso[]" class="form-control" required>
                                        <label for="edificio" class="form-label">Edificio</label>
                                        <select name="Edificio_id_edificio[]" class="form-control" required>
                                            @foreach($edificios as $edificio)
                                            <option value="{{ $edificio->id_edificio }}">{{ $edificio->nombre_edi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <button type="button" id="btnAgregarOtroPiso" class="btn btn-secondary mb-3">Agregar Otro Piso</button>
                                <button type="submit" class="btn btn-primary mb-3">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--script para agregar pisos-->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Mostrar modal al hacer clic en el botón
                    document.getElementById('btnAgregarPiso').addEventListener('click', function() {
                        var modal = new bootstrap.Modal(document.getElementById('modalAgregarPiso'));
                        modal.show();
                    });
            
                    // Agregar lógica para agregar otro piso
                    document.getElementById('btnAgregarOtroPiso').addEventListener('click', function() {
                        var pisosContainer = document.getElementById('pisosContainer');
                        var pisoItem = document.querySelector('.piso-item').cloneNode(true);
                        pisoItem.querySelector('input').value = ''; // Limpiar el valor del input
                        pisosContainer.appendChild(pisoItem);
                    });    
                });
            </script>
        </div>
    </main>

    <!-- Mensaje de error de conexion en la BD -->
    @if(session('errordata'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'info',
                    title: '¡ERROR!',
                    text: '{{ session('errordata') }}',
                    showConfirmButton: true,
                });
            });
        </script>
    @endif
@endsection


