<!--de la pagian de solo ambientes ambientes -->
@extends('layouts.admin')

@section('title', 'Ambiente-2')

<!--contenido para el menu desplegable-->
@section('sede-dropdown')
    <li class="nav-item dropdown px-1">
        <a class="nav-link dropdown-toggle white-text" href="#" id="sedeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Sedes
        </a>
        <ul class="dropdown-menu" aria-labelledby="sedeDropdown">
            <li class="dropdown-item">
                <form method="GET" action="{{ route('ambientes.index') }}">
                    <div class="mb-3">
                        <label for="sede" class="form-label">Sede</label>
                        <select name="sede" id="sede" class="form-control" onchange="this.form.submit()">
                            @foreach($sedes as $sede)
                                <option value="{{ Crypt::encryptString($sede->id_sede) }}" {{ $sedeSeleccionada == $sede->id_sede ? 'selected' : '' }}>
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </li>
        </ul>
    </li>
@endsection
@section('edificio-dropdown')
    <li class="nav-item dropdown px-4">
        <a class="nav-link dropdown-toggle white-text" href="#" id="edificioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Edificios
        </a>
        <ul class="dropdown-menu" aria-labelledby="edificioDropdown">
            <li class="dropdown-item">
                <form method="GET" action="{{ route('ambientes.index') }}">
                    <div class="mb-3">
                        <label for="edificio" class="form-label">Edificio</label>
                        <select name="edificio" id="edificio" class="form-control" onchange="this.form.submit()">
                            @foreach($edificios as $edificio)
                                <option value="{{ Crypt::encryptString($edificio->id_edificio) }}" {{ $edificioSeleccionado == $edificio->id_edificio ? 'selected' : '' }}>
                                    {{ $edificio->nombre_edi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </li>
        </ul>
    </li>
@endsection

@section('content')
<main class="content p-3 pagAmbcreaet">
    <div class="container-fluid">

        <h1 class="mb-4 text-center fw-bold">Lista de Ambientes <br> {{ $nombreSedeSeleccionada }}: {{ $nombreEdificioSeleccionada }}</h1>

        <!-- Botón para registrar nuevos ambientes -->
        <div class="mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registroAmbienteModal">Registrar Nuevo Ambiente</button>
        </div>

        <!-- Modal para registrar nuevo ambiente -->
        <div class="modal fade" id="registroAmbienteModal" tabindex="-1" aria-labelledby="registroAmbienteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registroAmbienteModalLabel">Registrar Nuevo Ambiente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('ambientes.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Ambiente</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="tipo_ambiente" class="form-label">Tipo de Ambiente</label>
                                <select class="form-control" id="tipo_ambiente" name="tipo_ambiente" required>
                                    <option value="">Seleccione un tipo de ambiente</option>
                                    @foreach($tiposAmbiente as $tipo)
                                        <option value="{{ $tipo->id_tipoambiente }}">{{ $tipo->nombre_amb }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="piso" class="form-label">Piso de {{$nombreEdificioSeleccionada}}</label>
                                <select class="form-control" id="piso" name="piso" required>
                                    <option value="">Seleccione un piso</option>
                                    @foreach($pisos as $piso)
                                        <option value="{{ $piso->id_piso }}">{{ $piso->numero_piso }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
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
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nros.</th>
                                <th>Tipo de Ambiente</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Piso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @foreach($ambientes as $ambiente)
                                <tr>
                                    <td>{{ $contador++ }}</td>
                                    <td>{{ $ambiente->tipo_ambiente }}</td>
                                    <td>{{ $ambiente->nombre }}</td>
                                    <td style="text-align: justify;">{{ $ambiente->descripcion_amb }}</td>
                                    <td>{{ $ambiente->numero_piso }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-2 btn-editar" title="Editar" data-id="{{ $ambiente->id_ambiente }}" data-nombre="{{ $ambiente->nombre }}" data-descripcion="{{ $ambiente->descripcion_amb }}" data-tipo="{{ $ambiente->TIPO_AMBIENTE_id_ambiente }}" data-piso="{{ $ambiente->PISO_id_piso }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <!-- Formulario para eliminar -->
                                        <form action="{{ route('ambientes.destroy', $ambiente->id_ambiente) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar" class="btn btn-sm btn-danger eliminar-btn"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal para editar ambiente -->
        <div class="modal fade" id="editarAmbienteModal" tabindex="-1" aria-labelledby="editarAmbienteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarAmbienteModalLabel">Editar Ambiente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('ambientes.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" id="editar_id" name="id">
                            <div class="mb-3">
                                <label for="editar_nombre" class="form-label">Nombre del Ambiente</label>
                                <input type="text" class="form-control" id="editar_nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="editar_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="editar_descripcion" name="descripcion" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editar_tipo_ambiente" class="form-label">Tipo de Ambiente</label>
                                <select class="form-control" id="editar_tipo_ambiente" name="tipo_ambiente" required>
                                    <option value="">Seleccione un tipo de ambiente</option>
                                    @foreach($tiposAmbiente as $tipo)
                                        <option value="{{ $tipo->id_tipoambiente }}">{{ $tipo->nombre_amb }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editar_piso" class="form-label">Piso de {{$nombreEdificioSeleccionada}}</label>
                                <select class="form-control" id="editar_piso" name="piso" required>
                                    <option value="">Seleccione un piso</option>
                                    @foreach($pisos as $piso)
                                        <option value="{{ $piso->id_piso }}">{{ $piso->numero_piso }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--script para abrir el modal editar-->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.btn-editar').forEach(button => {
                    button.addEventListener('click', function () {
                        const id = this.getAttribute('data-id');
                        const nombre = this.getAttribute('data-nombre');
                        const descripcion = this.getAttribute('data-descripcion');
                        const tipo = this.getAttribute('data-tipo');
                        const piso = this.getAttribute('data-piso');
                        
                        // obtener los valores para el modal
                        document.getElementById('editar_id').value = id;
                        document.getElementById('editar_nombre').value = nombre;
                        document.getElementById('editar_descripcion').value = descripcion;
                        document.getElementById('editar_tipo_ambiente').value = tipo;
                        document.getElementById('editar_piso').value = piso;
                        
                        // mostrar el modal
                        new bootstrap.Modal(document.getElementById('editarAmbienteModal')).show();
                    });

                    // Script para mostrar el mensaje de éxito y actualizar la página
                    @if(session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: '{{ session('success') }}',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    @endif

                    // Script para mostrar el mensaje de error
                    @if(session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '{{ session('error') }}',
                        });
                    @endif
                });
            });
        </script>
        
        <!--script para eliminar registro-->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.eliminar-btn').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault(); // Evitar que el formulario se envíe inmediatamente
                        const form = this.closest('form');
        
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "Al eliminar no habra registros del ambiente.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // Enviar el formulario si se confirma
                            }
                        });
                    });
                });

                //mensaje de exito al eliminar registro
                @if(session('successdelete'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito se elimino correctamente',
                        text: '{{ session('successdelete') }}',
                    }).then(() => {
                        window.location.reload.reload(); // Recargar la página después de mostrar el mensaje
                    });
                @endif
            });
        </script>
    </div>
</main>
<!--mensaje de registro exitoso-->
@if(session('successregister'))
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '{{ session('successregister') }}',
        });
    </script>
@endif

    <!--mensaje de error al editar el registro-->
    @if(session('errorregister'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errorregister') }}',
            });
        </script>
    @endif
<!--mensaje de error al editar el registro-->
@if(session('errordelete'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('errordelete') }}',
    });
</script>
@endif
@endsection
