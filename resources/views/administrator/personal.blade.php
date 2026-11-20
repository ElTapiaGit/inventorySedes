@extends('layouts.admin')

@section('title', 'Personal')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/personal.css') }}">
@endpush

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
                                    <td>
                                        <span class="fw-semibold">{{ $persona->tipoPersonal->descripcion_per ?? 'Sin Rol' }}</span>
                                        @if($persona->carrera)
                                            <div class="mt-1">
                                                <span class="badge bg-primary text-white"><i class="bi bi-mortarboard me-1"></i>{{ $persona->carrera->nombre_carrera }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $persona->edificio && $persona->edificio->sede ? $persona->edificio->sede->nombre : '-' }}</td>
                                    <td>
                                        @if($persona->tipoPersonal && strtolower($persona->tipoPersonal->descripcion_per) === 'encargado')
                                            @php
                                                $edificiosAsignados = $persona->edificiosAsignados;
                                                if ($edificiosAsignados->isEmpty() && $persona->edificio) {
                                                    $edificiosAsignados = collect([$persona->edificio]);
                                                }
                                            @endphp
                                            @foreach($edificiosAsignados as $edi)
                                                <span class="badge bg-light text-dark border me-1 mb-1 d-inline-block">
                                                    <i class="bi bi-building me-1 text-primary"></i>{{ $edi->nombre_edi }}
                                                    @if(isset($edi->pivot) && $edi->pivot->es_principal)
                                                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7em;">Principal</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-building me-1 text-secondary"></i>{{ $persona->edificio ? $persona->edificio->nombre_edi : 'Sin Edificio' }}
                                            </span>
                                        @endif
                                    </td>
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
                                                    @php
                                                        $rolActual = strtolower($persona->tipoPersonal->descripcion_per ?? '');
                                                        $esCoord = str_starts_with($rolActual, 'coordinador');
                                                        $esEnc = ($rolActual === 'encargado');
                                                        $asignadosIds = $persona->edificiosAsignados->pluck('id_edificio')->toArray();
                                                        if (empty($asignadosIds) && $persona->EDIFICIO_id_edificio) {
                                                            $asignadosIds = [(int) $persona->EDIFICIO_id_edificio];
                                                        }
                                                    @endphp
                                                    <div class="mb-3">
                                                        <label for="tipo_personal{{ $persona->id_personal }}" class="form-label fw-semibold">Tipo de Personal <span class="text-danger">*</span></label>
                                                        <select class="form-select select-tipo-personal-edit" id="tipo_personal{{ $persona->id_personal }}" name="tipo_personal" data-id="{{ $persona->id_personal }}" required>
                                                            @foreach($tiposPersonal as $tipo)
                                                                <option value="{{ $tipo->id_tipo_per }}" data-desc="{{ strtolower($tipo->descripcion_per) }}" {{ $persona->TIPO_PERSONAL_id_tipo_per == $tipo->id_tipo_per ? 'selected' : '' }}>{{ $tipo->descripcion_per }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3" id="bloqueCarreraEdit{{ $persona->id_personal }}" style="{{ $esCoord ? '' : 'display: none;' }}">
                                                        <label for="carreraedit{{ $persona->id_personal }}" class="form-label fw-semibold">Carrera Coordinada <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="carreraedit{{ $persona->id_personal }}" name="CARRERA_id_carrera">
                                                            <option value="">Seleccionar Carrera</option>
                                                            @foreach($carreras as $carrera)
                                                                <option value="{{ $carrera->id_carrera }}" {{ $persona->CARRERA_id_carrera == $carrera->id_carrera ? 'selected' : '' }}>{{ $carrera->nombre_carrera }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="form-text">Asigne la carrera que coordinara este usuario.</div>
                                                    </div>
                                                    <div class="mb-3" id="bloqueEdificioUnicoEdit{{ $persona->id_personal }}" style="{{ $esEnc ? 'display: none;' : '' }}">
                                                        <label for="edificio{{ $persona->id_personal }}" class="form-label fw-semibold">Edificio Base: <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="edificio{{ $persona->id_personal }}" name="edificio">
                                                            @foreach($edificios as $edificio)
                                                                <option value="{{ $edificio->id_edificio }}" {{ $persona->EDIFICIO_id_edificio == $edificio->id_edificio ? 'selected' : '' }}>{{$edificio->nombre_edi}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3" id="bloqueEdificiosMultiplesEdit{{ $persona->id_personal }}" style="{{ $esEnc ? '' : 'display: none;' }}">
                                                        <label class="form-label fw-semibold">Edificios Asignados al Encargado <span class="text-danger">*</span></label>
                                                        <div class="border rounded p-3 bg-light" style="max-height: 180px; overflow-y: auto;">
                                                            @foreach($edificios as $edificio)
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input check-edificio-edit-{{ $persona->id_personal }}" type="checkbox" name="edificios[]" 
                                                                           value="{{ $edificio->id_edificio }}" id="edificio_check_edit_{{ $persona->id_personal }}_{{ $edificio->id_edificio }}"
                                                                           {{ in_array($edificio->id_edificio, $asignadosIds) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="edificio_check_edit_{{ $persona->id_personal }}_{{ $edificio->id_edificio }}">
                                                                        <i class="bi bi-building me-1 text-primary"></i>{{ $edificio->nombre_edi }}
                                                                        <small class="text-muted">({{ $edificio->sede->nombre ?? 'Sede' }})</small>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="form-text">Marque los edificios donde el Encargado podra operar. El primero seleccionado sera el principal.</div>
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
                                <label for="tipo_personalnew" class="form-label fw-semibold">Tipo de Personal <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_personalnew" name="tipo_personal" required>
                                    @foreach($tiposPersonal as $tipo)
                                        <option value="{{ $tipo->id_tipo_per }}" data-desc="{{ strtolower($tipo->descripcion_per) }}">{{ $tipo->descripcion_per }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3" id="bloqueCarreraNew" style="display: none;">
                                <label for="carreranew" class="form-label fw-semibold">Carrera Coordinada <span class="text-danger">*</span></label>
                                <select class="form-select" id="carreranew" name="CARRERA_id_carrera">
                                    <option value="">Seleccionar Carrera</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre_carrera }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Asigne la carrera que coordinara este usuario.</div>
                            </div>
                            <div class="mb-3" id="bloqueEdificioUnicoNew">
                                <label for="edificionew" class="form-label fw-semibold">Edificio Base: <span class="text-danger">*</span></label>
                                <select class="form-select" id="edificionew" name="edificio">
                                    @foreach($edificios as $edificio)
                                        <option value="{{ $edificio->id_edificio }}">{{ $edificio->nombre_edi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3" id="bloqueEdificiosMultiplesNew" style="display: none;">
                                <label class="form-label fw-semibold">Edificios Asignados al Encargado <span class="text-danger">*</span></label>
                                <div class="border rounded p-3 bg-light" style="max-height: 180px; overflow-y: auto;">
                                    @foreach($edificios as $idx => $edificio)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-edificio-new" type="checkbox" name="edificios[]" 
                                                   value="{{ $edificio->id_edificio }}" id="edificio_check_new_{{ $edificio->id_edificio }}"
                                                   {{ $idx === 0 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edificio_check_new_{{ $edificio->id_edificio }}">
                                                <i class="bi bi-building me-1 text-primary"></i>{{ $edificio->nombre_edi }}
                                                <small class="text-muted">({{ $edificio->sede->nombre ?? 'Sede' }})</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text">Marque los edificios donde el Encargado podra operar. El primero seleccionado sera el principal.</div>
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
@endsection

@push('scripts')
    <!-- Script para verificar si desea inactivar personal -->
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
            });
        }
    </script>

    <!-- Script para alternar campos condicionales segun el rol del personal -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function toggleRoleFields(selectEl, carreraBlock, singleEdiBlock, multiEdiBlock) {
                if (!selectEl || !carreraBlock || !singleEdiBlock || !multiEdiBlock) return;
                
                const selectedOpt = selectEl.options[selectEl.selectedIndex];
                const desc = (selectedOpt ? (selectedOpt.getAttribute('data-desc') || selectedOpt.text) : '').toLowerCase().trim();
                const isCoord = desc.startsWith('coordinador');
                const isEnc = (desc === 'encargado');

                if (isCoord) {
                    carreraBlock.style.display = '';
                    singleEdiBlock.style.display = '';
                    multiEdiBlock.style.display = 'none';
                    
                    const carreraSelect = carreraBlock.querySelector('select');
                    if (carreraSelect) carreraSelect.required = true;
                    
                    const singleSelect = singleEdiBlock.querySelector('select');
                    if (singleSelect) {
                        singleSelect.disabled = false;
                        singleSelect.required = true;
                    }
                    multiEdiBlock.querySelectorAll('input[type="checkbox"]').forEach(c => c.disabled = true);
                } else if (isEnc) {
                    carreraBlock.style.display = 'none';
                    singleEdiBlock.style.display = 'none';
                    multiEdiBlock.style.display = '';

                    const carreraSelect = carreraBlock.querySelector('select');
                    if (carreraSelect) {
                        carreraSelect.required = false;
                        carreraSelect.value = '';
                    }

                    const singleSelect = singleEdiBlock.querySelector('select');
                    if (singleSelect) {
                        singleSelect.disabled = true;
                        singleSelect.required = false;
                    }
                    multiEdiBlock.querySelectorAll('input[type="checkbox"]').forEach(c => c.disabled = false);
                } else {
                    carreraBlock.style.display = 'none';
                    singleEdiBlock.style.display = '';
                    multiEdiBlock.style.display = 'none';

                    const carreraSelect = carreraBlock.querySelector('select');
                    if (carreraSelect) {
                        carreraSelect.required = false;
                        carreraSelect.value = '';
                    }

                    const singleSelect = singleEdiBlock.querySelector('select');
                    if (singleSelect) {
                        singleSelect.disabled = false;
                        singleSelect.required = true;
                    }
                    multiEdiBlock.querySelectorAll('input[type="checkbox"]').forEach(c => c.disabled = true);
                }
            }

            // Modal Nuevo Personal
            const tipoNew = document.getElementById('tipo_personalnew');
            const carreraNew = document.getElementById('bloqueCarreraNew');
            const singleEdiNew = document.getElementById('bloqueEdificioUnicoNew');
            const multiEdiNew = document.getElementById('bloqueEdificiosMultiplesNew');

            if (tipoNew && carreraNew && singleEdiNew && multiEdiNew) {
                tipoNew.addEventListener('change', function () {
                    toggleRoleFields(this, carreraNew, singleEdiNew, multiEdiNew);
                });
                toggleRoleFields(tipoNew, carreraNew, singleEdiNew, multiEdiNew);
            }

            // Modales Editar Personal
            document.querySelectorAll('.select-tipo-personal-edit').forEach(function (select) {
                const id = select.getAttribute('data-id');
                const carreraEdit = document.getElementById('bloqueCarreraEdit' + id);
                const singleEdiEdit = document.getElementById('bloqueEdificioUnicoEdit' + id);
                const multiEdiEdit = document.getElementById('bloqueEdificiosMultiplesEdit' + id);

                if (carreraEdit && singleEdiEdit && multiEdiEdit) {
                    select.addEventListener('change', function () {
                        toggleRoleFields(this, carreraEdit, singleEdiEdit, multiEdiEdit);
                    });
                    toggleRoleFields(select, carreraEdit, singleEdiEdit, multiEdiEdit);
                }
            });
        });
    </script>

    <!-- Script para validar contraseñas al asignar acceso -->
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const showPasswordToggle = document.getElementById('showPasswordToggle');
            const passwordInput = document.getElementById('password');
            const passwordHelp = document.getElementById('passwordHelp');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const passwordConfirmationHelp = document.getElementById('passwordConfirmationHelp');
            const form = document.getElementById('asignarAccesoForm');
    
            if (showPasswordToggle && passwordInput) {
                showPasswordToggle.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    showPasswordToggle.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
                });
            }
    
            function validatePassword() {
                let isValid = true;
                if (passwordInput && passwordHelp) {
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
                }
                return isValid;
            }
    
            function validatePasswordConfirmation() {
                let isValid = true;
                if (passwordConfirmationInput && passwordInput && passwordConfirmationHelp) {
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
                }
                return isValid;
            }
    
            if (passwordInput && passwordConfirmationInput && form) {
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
            }
        });
    </script>

    <!-- Mensaje de registro exitoso -->
    @if(session('successregister'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('successregister') }}',
            });
        </script>
    @endif

    <!-- Mensaje de error al registrar -->
    @if(session('errorregister'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errorregister') }}',
            });
        </script>
    @endif

    <!-- Mensaje de error de BD -->
    @if(session('errordata'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errordata') }}',
            });
        </script>
    @endif

    <!-- Mensaje de exitoso general -->    
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
            });
        </script>
    @endif

    <!-- Mensaje de error general -->    
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
            });
        </script>
    @endif
@endpush