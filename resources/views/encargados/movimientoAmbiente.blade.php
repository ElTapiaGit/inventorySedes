@extends('layouts.encargado')

@section('title', 'Movimientos')

@section('content')
<div class="content mt-2">

    <h1 class="text-center mb-4">USO DE AMBIENTES</h1>

    <!-- Botones de acción -->
    <div class="d-flex justify-content-center mb-4">
        <button class="btn btn-primary mx-4" data-bs-toggle="modal" data-bs-target="#registrarUsoModal">Registrar Uso</button>

        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registrarUsuarioModal">Registrar Usuario</button>
    </div>

     <!-- Formulario de búsqueda -->
     <form method="GET" action="{{ route('movimiento.ambiente.index') }}" class="mb-4">
        <div class="row justify-content-center">
            <!-- Campo de búsqueda por nombre de usuario-->
            <!-- de las misma -->
            <div class="col-md-4">
                <div class="input-group mx-2">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="usuario_nombre" class="form-control" placeholder="Buscar por nombre completo del usuario" value="{{ request('usuario_nombre') }}">
                </div>
            </div>

            <!-- Campo de búsqueda por fecha de uso -->
            <div class="col-md-4">
                <div class="input-group mx-2">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="date" name="fecha_uso" class="form-control" value="{{ request('fecha_uso') }}">
                </div>
            </div>

            <!-- Botón de búsqueda -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>

    <!-- Tabla de usos de ambientes -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Ambiente</th>
                    <th>Descripción</th>
                    <th>Semestre</th>
                    <th>Usuario</th>
                    <th>Fecha de Uso</th>
                    <th>Hora de Uso</th>
                    <th>Personal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usos as $uso)
                <tr>
                    <td class="{{ $uso->finalUsos ? '' : 'bg-success text-white' }}">{{ $uso->ambiente->nombre }}</td>
                    <td>{{ $uso->descripcion }}</td>
                    <td>{{ $uso->semestre }}</td>
                    <td>{{ $uso->usuario->nombre_completo }}</td>
                    <td>{{ \Carbon\Carbon::parse($uso->fch_uso)->format('d/m/Y') }}</td>
                    <td>{{ $uso->hora_uso }}</td>
                    <td>{{ $uso->personalInicio->nombre_completo }}</td> 
                
                    <td>
                        <a href="{{ route('uso.detalles', encrypt($uso->id_uso_ambiente)) }}" class="btn btn-info btn-sm" title="Más Información">
                            <i class="bi bi-info-circle"></i> <!-- Icono de información -->
                        </a>

                        <button class="btn btn-warning btn-sm finalizar-uso-btn" data-bs-toggle="modal" data-bs-target="#finalizarUsoModal" 
                            data-uso-id="{{ $uso->id_uso_ambiente }}" data-ambiente-nombre="{{ $uso->ambiente->nombre }}" title="Finalizar Uso">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Enlaces de paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $usos->links() }}
        </div>
    </div>

    <!-- Modal para Registrar Uso -->
    <div class="modal fade" id="registrarUsoModal" tabindex="-1" aria-labelledby="registrarUsoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrarUsoModalLabel">Registrar Uso de Ambiente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registrarUsoForm" method="POST" action="{{route('usos.store')}}">
                        @csrf
                        <div class="mb-3">
                            <label for="ambiente" class="form-label">Ambiente</label>
                            <select class="form-select" id="ambiente" name="ambiente_id" required>
                                <option value="">Seleccione Laboratorio</option>
                                @foreach($ambientes as $ambiente)
                                    <option value="{{ $ambiente->id_ambiente }}">{{ $ambiente->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="semestre" class="form-label">Semestre</label>
                            <input type="text" class="form-control" id="semestre" name="semestre">
                        </div>
                        <div class="mb-3">
                            <label for="fch_uso" class="form-label">Fecha de Uso</label>
                            <p class="form-control-plaintext">{{ $currentDate }}</p>
                        </div>
                        <div class="mb-3">
                            <label for="hora_uso" class="form-label">Hora de Uso</label>
                            <p class="form-control-plaintext">{{ $currentTime }}</p>
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <select class="form-select" id="usuario" name="usuario_id" required>
                                <option value="">seleccione usuario</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id_usuario }}">{{ $usuario->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Finalizar Uso -->
    <div class="modal fade" id="finalizarUsoModal" tabindex="-1" aria-labelledby="finalizarUsoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="finalizarUsoModalLabel">Finalizar Uso de Ambiente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="finalizarUsoForm" method="POST" action="{{ route('uso.finalizarUso') }}">
                        @csrf
                        <div class="mb-3">
                            <!-- Campo oculto para el ID del uso de ambiente -->
                            <input type="hidden" id="uso_ambiente_id" name="uso_ambiente_id">

                            <label for="uso_ambiente" class="form-label">Uso de Ambiente</label>
                            <input type="text" class="form-control" id="nombre_ambiente" name="nombre_ambiente" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="fch_fin" class="form-label">Fecha de Finalización</label>
                            <input type="date" class="form-control" id="fch_fin" name="fch_fin" value="{{ now()->format('Y-m-d') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="hora_fin" class="form-label">Hora de Finalización</label>
                            <input type="time" class="form-control" id="hora_fin" name="hora_fin" value="{{ now()->format('H:i') }}" readonly>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Finalizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--script para autollenar los input-->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Escuchar clics en los botones de "Finalizar Uso"
            const buttons = document.querySelectorAll('.finalizar-uso-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    // Obtener el ID del uso de ambiente del atributo de datos
                    const usoAmbienteId = this.getAttribute('data-uso-id');
                    // Obtener el nombre del ambiente del atributo de datos
                    const ambienteNombre = this.getAttribute('data-ambiente-nombre');

                    // Establecer el valor del campo oculto en el modal
                    document.getElementById('uso_ambiente_id').value = usoAmbienteId;
                    document.getElementById('nombre_ambiente').value = ambienteNombre;
                });
            });
        });
    </script>


    <!-- Modal para registrar nuevo usuario -->
    <div class="modal fade" id="registrarUsuarioModal" tabindex="-1" aria-labelledby="registrarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrarUsuarioModalLabel">Registrar Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('uso.user.registrar') }}" method="POST">
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
                            <select class="form-select" id="tipo_usuario" name="TIPO_USUARIO_id_tipo_usu" required>
                                <option selected disabled>Seleccione un tipo de usuario</option>
                                @foreach($tiposUsuario as $tipo)
                                    <option value="{{ $tipo->id_tipo_usu }}">{{ $tipo->tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Error',
                text: '{{ $errors->first() }}',
            });
        </script>
    @endif

</div>
@endsection