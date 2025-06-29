@extends('layouts.admin')

@section('title', 'Personal')

@section('content')
    <main class="content p-3 pagPersonal">  
        <div class="container-fluid">
            <div class="mb-3">
                <h2 class="text-center fw-bold">PERSONAL DE LA UNIVERSIDAD LATINOAMERICANA</h2>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-center mt-3 mb-3">
                    <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#modalNuevoPersonal">Nuevo Personal</button>
                    <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#modalAsignarAcceso">Asignar Acceso</button>
                    <a href="{{route('inhabilitados.index')}}" class="btn btn-primary">Personal Debaja</a>
                </div>
            </div>

            <h3 class="mb-4">Lista de Personal</h3>
            <div class="row">
                <div class="col-md-auto">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nros.</th>
                                    <th>Tipo de Personal</th>
                                    <th>Sede</th>
                                    <th>Edificio</th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Número de Celular</th>
                                    <th >Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach($personal as $persona)
                                <tr>
                                    <td>{{ $contador++ }}</td>
                                    <td>{{ $persona->tipoPersonal->descripcion_per }}</td>
                                    <td>{{ $persona->edificio->sede->nombre }}</td>
                                    <td>{{ $persona->edificio->nombre_edi }}</td>
                                    <td>{{ $persona->nombre }}</td>
                                    <td>{{ $persona->ap_paterno }} {{ $persona->ap_materno }}</td>
                                    <td>{{ $persona->celular }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-2" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarPersonal{{ $persona->id_personal }}"><i class="bi bi-pencil"></i></button>
                                        
                                        <button class="btn btn-sm btn-danger" title="Eliminar" onclick="confirmInhabilitar('{{ $persona->id_personal }}', '{{ $persona->nombre }}')"><i class="bi bi-trash"></i></button>
                                        <form id="delete-form-{{ $persona->id_personal }}" action="{{ route('personal.destroy', $persona->id_personal) }}" method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal Editar Personal -->
                                <div class="modal fade" id="modalEditarPersonal{{ $persona->id_personal }}" tabindex="-1" aria-labelledby="modalEditarPersonalLabel{{ $persona->id_personal }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalEditarPersonalLabel{{ $persona->id_personal }}">Editar Personal</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('personal.update', $persona->id_personal) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-3">
                                                        <label for="nombre{{ $persona->id_personal }}" class="form-label">Nombre</label>
                                                        <input type="text" class="form-control" id="nombre{{ $persona->id_personal }}" name="nombre" value="{{ $persona->nombre }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="apellido_paterno{{ $persona->id_personal }}" class="form-label">Apellido Paterno</label>
                                                        <input type="text" class="form-control" id="apellido_paterno{{ $persona->id_personal }}" name="apellido_paterno" value="{{ $persona->ap_paterno }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="apellido_materno{{ $persona->id_personal }}" class="form-label">Apellido Materno</label>
                                                        <input type="text" class="form-control" id="apellido_materno{{ $persona->id_personal }}" name="apellido_materno" value="{{ $persona->ap_materno }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="numero_celular{{ $persona->id_personal }}" class="form-label">Número de Celular</label>
                                                        <input type="text" class="form-control" id="numero_celular{{ $persona->id_personal }}" name="numero_celular" value="{{ $persona->celular }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="tipo_personal{{ $persona->id_personal }}" class="form-label">Tipo de Personal</label>
                                                        <select class="form-select" id="tipo_personal{{ $persona->id_personal }}" name="tipo_personal" required>
                                                            @foreach($tiposPersonal as $tipo)
                                                                <option value="{{ $tipo->id_tipo_per }}" {{ $persona->TIPO_PERSONAL_id_tipo_per == $tipo->id_tipo_per ? 'selected' : '' }}>{{ $tipo->descripcion_per }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edificio{{ $persona->id_personal }}" class="form-label">Edificio:</label>
                                                        <select class="form-select" id="edificio{{ $persona->id_personal }}" name="edificio" required>
                                                            @foreach($edificios as $edificio)
                                                                <option value="{{ $edificio->id_edificio }}" {{ $persona->EDIFICIO_id_edificio == $edificio->id_edificio ? 'selected' : '' }}>{{$edificio->nombre_edi}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nuevo Personal -->
        <div class="modal fade" id="modalNuevoPersonal" tabindex="-1" aria-labelledby="modalNuevoPersonalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalNuevoPersonalLabel">Nuevo Personal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('personal.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombrenew" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombrenew" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellido_paternonew" class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" id="apellido_paternonew" name="apellido_paterno" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellido_maternonew" class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" id="apellido_maternonew" name="apellido_materno">
                            </div>
                            <div class="mb-3">
                                <label for="numero_celularnew" class="form-label">Número de Celular</label>
                                <input type="text" class="form-control" id="numero_celularnew" name="numero_celular" required>
                            </div>
                            <div class="mb-3">
                                <label for="tipo_personalnew" class="form-label">Tipo de Personal</label>
                                <select class="form-select" id="tipo_personalnew" name="tipo_personal" required>
                                    @foreach($tiposPersonal as $tipo)
                                        <option value="{{ $tipo->id_tipo_per }}">{{ $tipo->descripcion_per }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edificionew" class="form-label">Edificio:</label>
                                <select class="form-select" id="edificionew" name="edificio" required>
                                    @foreach($edificios as $edificio)
                                        <option value="{{ $edificio->id_edificio }}">{{ $edificio->nombre_edi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin Modal Nuevo Personal -->        

        <!-- Modal Asignar Acceso -->
        <div class="modal fade" id="modalAsignarAcceso" tabindex="-1" aria-labelledby="modalAsignarAccesoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAsignarAccesoLabel">Asignar Acceso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="asignarAccesoForm" action="{{ route('personal.asignarAcceso') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombre_completo" class="form-label">Nombre Completo del Personal</label>
                                <select class="form-select" id="nombre_completo" name="nombre_completo" required>
                                    <option value="">Seleccionar nombre completo</option>
                                    @foreach($personal as $persona)
                                        <option value="{{ $persona->id_personal }}">{{ $persona->nombre }} {{ $persona->ap_paterno }} {{ $persona->ap_materno }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nombre_acceso" class="form-label">Nombre de Acceso</label>
                                <input type="text" class="form-control" id="nombre_acceso" name="nombre_acceso" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="showPasswordToggle"><i class="bi bi-eye"></i></button>
                                </div>
                                <div id="passwordHelp" class="form-text"></div>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Repetir Contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <div id="passwordConfirmationHelp" class="form-text"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin Modal Asignar Acceso -->
    </main>

    <!--script para verificar si desea inactivar personal-->
    <script>
        function confirmInhabilitar(id, nombre) {
            Swal.fire({
                title: '¿Está seguro?',
                text: "Está a punto de inhabilitar a " + nombre + ".",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, inhabilitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>

    <!--para validar las contraseñas al asignar acceso -->
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const showPasswordToggle = document.getElementById('showPasswordToggle');
            const passwordInput = document.getElementById('password');
            const passwordHelp = document.getElementById('passwordHelp');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const passwordConfirmationHelp = document.getElementById('passwordConfirmationHelp');
            const form = document.getElementById('asignarAccesoForm');
    
            showPasswordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                showPasswordToggle.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
            });
    
            function validatePassword() {
                let isValid = true;
                if (passwordInput.value.length < 6) {
                    passwordHelp.textContent = 'Por seguridad se necesita al menos 6 caracteres';
                    passwordHelp.classList.add('text-danger');
                    passwordHelp.classList.remove('text-success');
                    isValid = false;
                } else {
                    passwordHelp.textContent = 'Contraseña válida';
                    passwordHelp.classList.add('text-success');
                    passwordHelp.classList.remove('text-danger');
                }
                return isValid;
            }
    
            function validatePasswordConfirmation() {
                let isValid = true;
                if (passwordConfirmationInput.value !== passwordInput.value) {
                    passwordConfirmationHelp.textContent = 'Las contraseñas no coinciden';
                    passwordConfirmationHelp.classList.add('text-danger');
                    passwordConfirmationHelp.classList.remove('text-success');
                    isValid = false;
                } else {
                    passwordConfirmationHelp.textContent = 'Las contraseñas coinciden';
                    passwordConfirmationHelp.classList.add('text-success');
                    passwordConfirmationHelp.classList.remove('text-danger');
                }
                return isValid;
            }
    
            passwordInput.addEventListener('input', validatePassword);
            passwordConfirmationInput.addEventListener('input', validatePasswordConfirmation);
    
            form.addEventListener('submit', function(event) {
                const isPasswordValid = validatePassword();
                const isPasswordConfirmationValid = validatePasswordConfirmation();
    
                if (!isPasswordValid || !isPasswordConfirmationValid) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, corrige los errores en el formulario',
                    });
                }
            });
        });
    </script>

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
<!--Menasje de error al registrar -->
@if(session('errorregister'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('errorregister') }}',
        });
    </script>
@endif
<!--Menasje de error de BD -->
@if(session('errordata'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('errordata') }}',
        });
    </script>
@endif
<!--mensaje de exitoso todo-->    
@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '{{ session('success') }}',
        });
    </script>
@endif
<!--mensaje de error todo-->    
@if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
        });
    </script>
@endif
@endsection