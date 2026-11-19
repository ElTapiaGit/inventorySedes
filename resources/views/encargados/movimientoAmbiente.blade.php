@extends('layouts.encargado')

@section('title', 'Movimientos')

@section('content')
<div class="content mt-2">

    <!-- Encabezado Estandarizado -->
    <div class="encargado-page-header">
        <h1>
            <i class="bi bi-door-open text-primary"></i> Uso de Ambientes
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#registrarUsoModal">
                <i class="bi bi-plus-circle me-1"></i> Registrar Uso
            </button>
            <button class="btn btn-success px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#registrarUsuarioModal">
                <i class="bi bi-person-plus me-1"></i> Registrar Usuario
            </button>
        </div>
    </div>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="{{ route('movimiento.ambiente.index') }}" class="mb-4">
        <div class="row justify-content-center gap-2">
            <!-- Campo de búsqueda por nombre de usuario-->
            <!-- de las misma -->
            <div class="col-md-3">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="filtro_usuario_nombre" name="usuario_nombre" class="form-control" 
                        placeholder="Buscar por nombre completo del usuario" 
                        aria-label="Buscar por nombre completo del usuario"
                        value="{{ request('usuario_nombre') }}">
                </div>
            </div>

            <!-- Campo de búsqueda por fecha de uso -->
            <div class="col-md-3">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="date" id="filtro_fecha_uso" name="fecha_uso" class="form-control" 
                        aria-label="Buscar por fecha de uso"
                        value="{{ request('fecha_uso') }}">
                </div>
            </div>

            <!-- Botón de búsqueda -->
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Buscar</button>
            </div>
        </div>
    </form>

    <!-- Tabla de usos de ambientes -->
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover table-bordered table-encargado align-middle mb-0">
            <thead class="table-encargado text-center">
                <tr>
                    <th scope="col">Nro.</th>
                    <th scope="col">Ambiente</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Semestre</th>
                    <th scope="col">Usuario</th>
                    <th scope="col">Fecha de Uso</th>
                    <th scope="col">Hora de Uso</th>
                    <th scope="col">Personal</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    //$contador = 1;
                    $contador = $usos->firstItem(); // contador real de la página
                @endphp
                @foreach($usos as $uso)
                <tr>
                    <td class="text-center">{{ $contador++ }}</td>
                    <td class="{{ $uso->finalUsos ? '' : 'bg-success text-white fw-bold' }}">{{ $uso->ambiente->nombre }}</td>
                    <td>{{ $uso->descripcion }}</td>
                    <td class="text-center">{{ $uso->semestre }}</td>
                    <td>{{ $uso->usuario->nombre_completo }}</td>
                    <td>{{ \Carbon\Carbon::parse($uso->fch_uso)->format('d/m/Y') }}</td>
                    <td>{{ $uso->hora_uso }}</td>
                    <td>{{ $uso->personalInicio->nombre_completo }}</td> 
                
                    <td class="text-center">
                        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-2">
                            <a href="{{ route('uso.detalles', encrypt($uso->id_uso_ambiente)) }}"
                            class="btn btn-outline-info btn-sm w-md-auto  mb-md-0"
                            title="Más Información">
                                <i class="bi bi-info-circle"></i>
                            </a>

                            <button class="btn btn-outline-warning btn-sm finalizar-uso-btn w-md-auto"
                                    data-bs-toggle="modal"
                                    data-bs-target="#finalizarUsoModal"
                                    data-uso-id="{{ $uso->id_uso_ambiente }}"
                                    data-ambiente-nombre="{{ $uso->ambiente->nombre }}"
                                    title="Finalizar Uso">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Enlaces de paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $usos->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Modal para Registrar Uso -->
    <div class="modal fade" id="registrarUsoModal" tabindex="-1" aria-labelledby="registrarUsoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-sm rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="registrarUsoModalLabel">Registrar Uso de Ambiente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registrarUsoForm" method="POST" action="{{route('usos.store')}}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="ambiente" class="form-label">Ambiente</label>
                                <select class="form-select" id="ambiente" name="ambiente_id" required>
                                    <option value="">Seleccione Laboratorio</option>
                                    @foreach($ambientes as $ambiente)
                                        <option value="{{ $ambiente->id_ambiente }}">{{ $ambiente->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="usuario" class="form-label">Usuario</label>
                                <select class="form-select" id="usuario" name="usuario_id" required>
                                    <option value="">Seleccione Usuario</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id_usuario }}">{{ $usuario->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="semestre" class="form-label">Semestre</label>
                                <input type="text" class="form-control" id="semestre" name="semestre">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="fecha_uso_display" class="form-label">Fecha de Uso</label>
                                    <input type="text" id="fecha_uso_display" class="form-control-plaintext border px-2 py-1 rounded" value="{{ $currentDate }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="hora_uso_display" class="form-label">Hora de Uso</label>
                                    <input type="text" id="hora_uso_display" class="form-control-plaintext border px-2 py-1 rounded" value="{{ $currentTime }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success px-4 shadow">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Finalizar Uso -->
    <div class="modal fade" id="finalizarUsoModal" tabindex="-1" aria-labelledby="finalizarUsoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow-sm rounded-4">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="finalizarUsoModalLabel">Finalizar Uso de Ambiente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="finalizarUsoForm" method="POST" action="{{ route('uso.finalizarUso') }}">
                        @csrf
                        <div class="mb-3">
                            <!-- Campo oculto para el ID del uso de ambiente -->
                            <input type="hidden" id="uso_ambiente_id" name="uso_ambiente_id">

                            <label for="nombre_ambiente" class="form-label">Uso de Ambiente</label>
                            <input type="text" class="form-control" id="nombre_ambiente" name="nombre_ambiente" readonly>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="fch_fin" class="form-label">Fecha de Finalización</label>
                                <input type="date" class="form-control-plaintext border px-2 py-1 rounded text-center" id="fch_fin" name="fch_fin" value="{{ now()->format('Y-m-d') }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label for="hora_fin" class="form-label">Hora de Finalización</label>
                                <input type="time" class="form-control-plaintext border px-2 py-1 rounded text-center" id="hora_fin" name="hora_fin" value="{{ now()->format('H:i') }}" readonly>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-danger px-4 shadow">Finalizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para registrar nuevo usuario -->
    <div class="modal fade" id="registrarUsuarioModal" tabindex="-1" aria-labelledby="registrarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow-sm rounded-4">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="registrarUsuarioModalLabel">Registrar Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('uso.user.registrar') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>

                            <div class="col-md-6">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            </div>

                            <div class="col-md-6">
                                <label for="celular" class="form-label">Celular</label>
                                <input type="text" class="form-control" id="celular" name="celular" required>
                            </div>

                            <div class="col-md-6">
                                <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                                <select class="form-select" id="tipo_usuario" name="TIPO_USUARIO_id_tipo_usu" required>
                                    <option selected disabled value="">Seleccione un tipo</option>
                                    @foreach($tiposUsuario as $tipo)
                                        <option value="{{ $tipo->id_tipo_usu }}">{{ $tipo->tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success px-4 shadow">Registrar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Script para autollenar inputs del modal finalizar uso -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.finalizar-uso-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const usoAmbienteId = this.getAttribute('data-uso-id');
                    const ambienteNombre = this.getAttribute('data-ambiente-nombre');

                    document.getElementById('uso_ambiente_id').value = usoAmbienteId;
                    document.getElementById('nombre_ambiente').value = ambienteNombre;
                });
            });
        });
    </script>

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
@endpush