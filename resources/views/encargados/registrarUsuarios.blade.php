@extends('layouts.encargado')

@section('content')
<div class="content mt-2">

    <h1 class="text-center mb-4">Usuarios</h1>
            
            <div class="d-flex justify-content-center mb-4">
                <!-- Botones de registro -->
                <button class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#modalRegistrarUsuario">Registrar Usuario</button>
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalRegistrarTipoUsuario">Registrar Tipo Usuario</button>
            </div>

            <!-- Filtros de búsqueda -->
            <div class="justify-content-center mb-4">
                <form method="GET" action="{{ route('encargado.usuarios') }}" class="d-flex align-items-center">
                    <div class="input-group mx-2 w-25">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre" value="{{ request('nombre') }}">
                    </div>
            
                    <div class="input-group mx-2 w-25">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <select name="tipo_usuario" class="form-select">
                            <option value="">Filtrar por tipo de usuario</option>
                            @foreach($tipoUsuarios as $tipo)
                                <option value="{{ $tipo->id_tipo_usu }}" {{ request('tipo_usuario') == $tipo->id_tipo_usu ? 'selected' : '' }}>
                                    {{ $tipo->tipo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
            
                    <button type="submit" class="btn btn-primary mx-2">Buscar</button>
                </form>
            </div>

        <!-- Tabla de usuarios -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nros.</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Celular</th>
                    <th>Tipo de Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                   $contador=1;   
               @endphp
                @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{$contador++}}</td>
                        <td>{{ $usuario->nombre }}</td>
                        <td>{{ $usuario->apellidos }}</td>
                        <td>{{ $usuario->celular }}</td>
                        <td>{{ $usuario->tipoUsuario->tipo }}</td>
                        <td>
                            <!-- Botón de editar -->
                            <button type="button" class="btn btn-warning" title="Editar" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal" data-id="{{ $usuario->id_usuario }}" data-nombre="{{ $usuario->nombre }}" data-apellidos="{{ $usuario->apellidos }}" data-celular="{{ $usuario->celular }}" data-tipo="{{ $usuario->TIPO_USUARIO_id_tipo_usu }}">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            
                            <!-- Botón de eliminar -->
                            <form id="form-delete-{{ $usuario->id_usuario }}" action="{{ route('encargado.eliminarUsuario', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger" title="Eliminar" onclick="confirmDelete({{ $usuario->id_usuario }})">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <script>
                function confirmDelete(id) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "No podrás revertir esta acción.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('form-delete-' + id).submit();
                        }
                    })
                }
            </script>                
        </table>
    </div>
</div>

<!-- Modal para editar usuario -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editarUsuarioForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellidos" class="form-label">Apellidos</label>
                        <input type="text" id="apellidos" name="apellidos" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="celular" class="form-label">Celular</label>
                        <input type="text" id="celular" name="celular" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                        <select id="tipo_usuario" name="tipo_usuario" class="form-select" required>
                            @foreach($tipoUsuarios as $tipo)
                                <option value="{{ $tipo->id_tipo_usu }}">{{ $tipo->tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Script para llenar el formulario de edición con los datos del usuario -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var editarUsuarioModal = document.getElementById('editarUsuarioModal');

        editarUsuarioModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var nombre = button.getAttribute('data-nombre');
            var apellidos = button.getAttribute('data-apellidos');
            var celular = button.getAttribute('data-celular');
            var tipo = button.getAttribute('data-tipo');

            var form = document.getElementById('editarUsuarioForm');
            form.action = '/usuarios/' + id;

            form.querySelector('#nombre').value = nombre;
            form.querySelector('#apellidos').value = apellidos;
            form.querySelector('#celular').value = celular;
            form.querySelector('#tipo_usuario').value = tipo;
        });
    });
</script>

<!-- Modal Registrar Usuario -->
<div class="modal fade" id="modalRegistrarUsuario" tabindex="-1" aria-labelledby="modalRegistrarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistrarUsuarioLabel">Registrar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario de Registro de Usuario -->
                <form method="POST" action="{{route('encargado.register.usuarios')}}">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellidos" class="form-label">Apellidos</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                    </div>
                    <div class="mb-3">
                        <label for="celular" class="form-label">Celular</label>
                        <input type="text" class="form-control" id="celular" name="celular" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                        <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                            @foreach($tipoUsuarios as $tipo)
                                <option value="{{ $tipo->id_tipo_usu }}">{{ $tipo->tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Tipo Usuario -->
<div class="modal fade" id="modalRegistrarTipoUsuario" tabindex="-1" aria-labelledby="modalRegistrarTipoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistrarTipoUsuarioLabel">Registrar Tipo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario de Registro de Tipo de Usuario -->
                <form method="POST" action="{{route('encargado.register.tipousuarios')}}">
                    @csrf
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Usuario</label>
                        <input type="text" class="form-control" id="tipo" name="tipo" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <script>
        //mensaje cuando el mobiliario ya existe
        Swal.fire({
            icon: 'success',
            title: 'Exito',
            text: '{{ session('success') }}',
        });
    </script>
@endif

@if(session('info'))
    <script>
        //mensaje cuando el mobiliario ya existe
        Swal.fire({
            icon: 'info',
            title: 'Registro No Valido',
            text: '{{ session('info') }}',
        });
    </script>
@endif

@endsection
