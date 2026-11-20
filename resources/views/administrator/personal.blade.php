@extends('layouts.admin')

@section('title', 'Personal')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/personal.css') }}">
@endpush

@section('content')
<main class="content p-3 pagPersonal">  
    <div class="container-fluid">
        <!-- Cabecera de seccion -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-people me-2 text-primary"></i>Personal de la Universidad
                </h2>
                <p class="text-muted small mb-0">Gestion y asignacion de personal institucional, roles, sedes y carreras.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalNuevoPersonal">
                    <i class="bi bi-person-plus"></i>
                    <span>Nuevo Personal</span>
                </button>
                <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalAsignarAcceso">
                    <i class="bi bi-key"></i>
                    <span>Asignar Acceso</span>
                </button>
                <a href="{{ route('inhabilitados.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-person-x"></i>
                    <span>Personal Inhabilitado</span>
                </a>
            </div>
        </div>

        <!-- Tarjeta principal de listado -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Tipo de Personal</th>
                                <th>Sede</th>
                                <th>Edificio</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Celular</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @forelse($personal as $persona)
                                @php
                                    $rolActual = strtolower($persona->tipoPersonal->descripcion_per ?? '');
                                    $asignadosIds = $persona->edificiosAsignados->pluck('id_edificio')->toArray();
                                    if (empty($asignadosIds) && $persona->EDIFICIO_id_edificio) {
                                        $asignadosIds = [(int) $persona->EDIFICIO_id_edificio];
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $persona->tipoPersonal->descripcion_per ?? 'Sin Rol' }}</span>
                                        @if($persona->carrera)
                                            <div class="mt-1">
                                                <span class="badge bg-primary text-white">
                                                    <i class="bi bi-mortarboard me-1"></i>{{ $persona->carrera->nombre_carrera }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $persona->edificio && $persona->edificio->sede ? $persona->edificio->sede->nombre : '-' }}</td>
                                    <td>
                                        @if($persona->tipoPersonal && $rolActual === 'encargado')
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
                                    <td class="fw-semibold text-dark">{{ $persona->nombre }}</td>
                                    <td>{{ $persona->ap_paterno }} {{ $persona->ap_materno }}</td>
                                    <td>{{ $persona->celular }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" title="Editar Personal"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditarPersonal"
                                                    data-id="{{ $persona->id_personal }}"
                                                    data-nombre="{{ $persona->nombre }}"
                                                    data-apellido-paterno="{{ $persona->ap_paterno }}"
                                                    data-apellido-materno="{{ $persona->ap_materno }}"
                                                    data-celular="{{ $persona->celular }}"
                                                    data-tipo-personal="{{ $persona->TIPO_PERSONAL_id_tipo_per }}"
                                                    data-carrera="{{ $persona->CARRERA_id_carrera ?? '' }}"
                                                    data-edificio="{{ $persona->EDIFICIO_id_edificio ?? '' }}"
                                                    data-edificios='@json($asignadosIds)'
                                                    data-update-url="{{ route('personal.update', $persona->id_personal) }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Inhabilitar" onclick="confirmInhabilitar('{{ $persona->id_personal }}', '{{ $persona->nombre }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $persona->id_personal }}" action="{{ route('personal.destroy', $persona->id_personal) }}" method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>No hay registros de personal disponibles.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Unico Editar Personal (Extrado fuera del foreach) -->
    <div class="modal fade" id="modalEditarPersonal" tabindex="-1" aria-labelledby="modalEditarPersonalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #1B4B82;">
                    <h5 class="modal-title fw-bold" id="modalEditarPersonalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Editar Personal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formEditarPersonal" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_apellido_paterno" class="form-label fw-semibold">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_apellido_paterno" name="apellido_paterno" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_apellido_materno" class="form-label fw-semibold">Apellido Materno</label>
                            <input type="text" class="form-control" id="edit_apellido_materno" name="apellido_materno">
                        </div>
                        <div class="mb-3">
                            <label for="edit_numero_celular" class="form-label fw-semibold">Numero de Celular <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_numero_celular" name="numero_celular" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tipo_personal" class="form-label fw-semibold">Tipo de Personal <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_tipo_personal" name="tipo_personal" required>
                                @foreach($tiposPersonal as $tipo)
                                    <option value="{{ $tipo->id_tipo_per }}" data-desc="{{ strtolower($tipo->descripcion_per) }}">{{ $tipo->descripcion_per }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="bloqueCarreraEdit" style="display: none;">
                            <label for="edit_carrera" class="form-label fw-semibold">Carrera Coordinada <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_carrera" name="CARRERA_id_carrera">
                                <option value="">Seleccionar Carrera</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre_carrera }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Asigne la carrera que coordinara este usuario.</div>
                        </div>
                        <div class="mb-3" id="bloqueEdificioUnicoEdit">
                            <label for="edit_edificio" class="form-label fw-semibold">Edificio Base: <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_edificio" name="edificio">
                                <option value="">-- Seleccione un Edificio --</option>
                                @foreach($edificios as $edificio)
                                    <option value="{{ $edificio->id_edificio }}">{{ $edificio->nombre_edi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="bloqueEdificiosMultiplesEdit" style="display: none;">
                            <label class="form-label fw-semibold">Edificios Asignados al Encargado <span class="text-danger">*</span></label>
                            <div class="border rounded p-3 bg-light" style="max-height: 180px; overflow-y: auto;">
                                @foreach($edificios as $edificio)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input check-edificio-edit" type="checkbox" name="edificios[]" 
                                               value="{{ $edificio->id_edificio }}" id="edificio_check_edit_{{ $edificio->id_edificio }}">
                                        <label class="form-check-label" for="edificio_check_edit_{{ $edificio->id_edificio }}">
                                            <i class="bi bi-building me-1 text-primary"></i>{{ $edificio->nombre_edi }}
                                            <small class="text-muted">({{ $edificio->sede->nombre ?? 'Sede' }})</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text">Marque los edificios donde el Encargado podra operar. El primero seleccionado sera el principal.</div>
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
    <!-- Fin Modal Unico Editar Personal -->

    <!-- Modal Nuevo Personal -->
    <div class="modal fade" id="modalNuevoPersonal" tabindex="-1" aria-labelledby="modalNuevoPersonalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #1B4B82;">
                    <h5 class="modal-title fw-bold" id="modalNuevoPersonalLabel">
                        <i class="bi bi-person-plus me-2"></i>Nuevo Personal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('personal.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nombrenew" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombrenew" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido_paternonew" class="form-label fw-semibold">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellido_paternonew" name="apellido_paterno" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido_maternonew" class="form-label fw-semibold">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_maternonew" name="apellido_materno">
                        </div>
                        <div class="mb-3">
                            <label for="numero_celularnew" class="form-label fw-semibold">Numero de Celular <span class="text-danger">*</span></label>
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
                                <option value="">-- Seleccione un Edificio --</option>
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
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal Nuevo Personal -->        

    <!-- Modal Asignar Acceso -->
    <div class="modal fade" id="modalAsignarAcceso" tabindex="-1" aria-labelledby="modalAsignarAccesoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #1B4B82;">
                    <h5 class="modal-title fw-bold" id="modalAsignarAccesoLabel">
                        <i class="bi bi-key me-2"></i>Asignar Acceso
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="asignarAccesoForm" action="{{ route('personal.asignarAcceso') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nombre_completo" class="form-label fw-semibold">Nombre Completo del Personal <span class="text-danger">*</span></label>
                            <select class="form-select" id="nombre_completo" name="nombre_completo" required>
                                <option value="">Seleccionar nombre completo</option>
                                @foreach($personal as $persona)
                                    <option value="{{ $persona->id_personal }}">{{ $persona->nombre }} {{ $persona->ap_paterno }} {{ $persona->ap_materno }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_acceso" class="form-label fw-semibold">Nombre de Acceso <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_acceso" name="nombre_acceso" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Contrasena <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="showPasswordToggle" aria-label="Mostrar u ocultar contrasena"><i class="bi bi-eye"></i></button>
                            </div>
                            <div id="passwordHelp" class="form-text"></div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Repetir Contrasena <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <div id="passwordConfirmationHelp" class="form-text"></div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Acceso</button>
                        </div>
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
                title: '¿Esta seguro?',
                text: "Esta a punto de inhabilitar a " + nombre + ".",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, inhabilitar',
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

            // Modal Unico Editar Personal
            const modalEditarPersonal = document.getElementById('modalEditarPersonal');
            if (modalEditarPersonal) {
                const tipoEdit = document.getElementById('edit_tipo_personal');
                const carreraEdit = document.getElementById('bloqueCarreraEdit');
                const singleEdiEdit = document.getElementById('bloqueEdificioUnicoEdit');
                const multiEdiEdit = document.getElementById('bloqueEdificiosMultiplesEdit');

                if (tipoEdit && carreraEdit && singleEdiEdit && multiEdiEdit) {
                    tipoEdit.addEventListener('change', function () {
                        toggleRoleFields(this, carreraEdit, singleEdiEdit, multiEdiEdit);
                    });
                }

                modalEditarPersonal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const form = document.getElementById('formEditarPersonal');
                    form.action = button.getAttribute('data-update-url');

                    document.getElementById('edit_nombre').value = button.getAttribute('data-nombre') || '';
                    document.getElementById('edit_apellido_paterno').value = button.getAttribute('data-apellido-paterno') || '';
                    document.getElementById('edit_apellido_materno').value = button.getAttribute('data-apellido-materno') || '';
                    document.getElementById('edit_numero_celular').value = button.getAttribute('data-celular') || '';

                    if (tipoEdit) {
                        tipoEdit.value = button.getAttribute('data-tipo-personal') || '';
                    }

                    const carreraSelect = document.getElementById('edit_carrera');
                    if (carreraSelect) {
                        carreraSelect.value = button.getAttribute('data-carrera') || '';
                    }

                    const edificioSelect = document.getElementById('edit_edificio');
                    if (edificioSelect) {
                        edificioSelect.value = button.getAttribute('data-edificio') || '';
                    }

                    let asignados = [];
                    try {
                        asignados = JSON.parse(button.getAttribute('data-edificios') || '[]');
                    } catch(e) {
                        asignados = [];
                    }

                    document.querySelectorAll('.check-edificio-edit').forEach(function(cb) {
                        cb.checked = asignados.includes(parseInt(cb.value));
                    });

                    toggleRoleFields(tipoEdit, carreraEdit, singleEdiEdit, multiEdiEdit);
                });
            }
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
                        passwordHelp.textContent = 'Contrasena valida';
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
                        passwordConfirmationHelp.textContent = 'Las contrasenas no coinciden';
                        passwordConfirmationHelp.classList.add('text-danger');
                        passwordConfirmationHelp.classList.remove('text-success');
                        isValid = false;
                    } else {
                        passwordConfirmationHelp.textContent = 'Las contrasenas coinciden';
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

    <!-- Mensajes de sesion -->
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

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Exito',
                text: '{{ session('success') }}',
            });
        </script>
    @endif

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
