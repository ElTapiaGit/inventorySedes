@extends('layouts.encargado')

@section('title', 'Gestión de Mantenimientos')

@section('content')
<div class="content mt-2">
    <!-- Encabezado Estandarizado -->
    <div class="encargado-page-header">
        <h1>
            <i class="bi bi-wrench-adjustable text-primary"></i> Gestión de Mantenimientos
        </h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary px-3 shadow-sm" id="btnNuevoTecnicoDirecto" data-bs-toggle="modal" data-bs-target="#modalNuevoTecnico">
                <i class="bi bi-person-plus me-1"></i> Nuevo Técnico
            </button>
            <button class="btn btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoMantenimiento">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Mantenimiento
            </button>
        </div>
    </div>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover table-bordered table-encargado text-center align-middle mb-0">
            <thead class="table-encargado text-center">
                <tr>
                    <th>#</th>
                    <th>Informe Inicial</th>
                    <th>Fecha de Inicio</th>
                    <th>Técnico</th>
                    <th>Responsable</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mantenimientos as $index => $mantenimiento)
                    <tr>
                        <td>{{ $mantenimientos->firstItem() + $index }}</td>
                        <td>{{ Str::limit($mantenimiento->informe_inicial, 40) }}</td>
                        <td>{{ \Carbon\Carbon::parse($mantenimiento->fch_inicio)->format('d/m/Y') }}</td>
                        <td>{{ $mantenimiento->tecnico->nombre ?? '—' }}</td>
                        <td>{{ $mantenimiento->personal->nombre ?? '—' }}</td>
                        <td>
                            @if($mantenimiento->finalMantenimiento)
                                <span class="badge bg-success">Finalizado</span>
                            @else
                                <span class="badge bg-warning text-dark">En Proceso</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('encargado.mantenimiento.show', encrypt($mantenimiento->id_mantenimiento_ini)) }}" class="btn btn-outline-info btn-sm" title="Ver Detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(!$mantenimiento->finalMantenimiento)
                                <button 
                                    class="btn btn-outline-primary btn-sm" 
                                    data-id="{{ $mantenimiento->id_mantenimiento_ini }}"
                                    data-url="{{ route('encargado.mantenimiento.finalizar', $mantenimiento->id_mantenimiento_ini) }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalFinalizarMantenimiento">
                                        <i class="bi bi-check2-square"></i> Finalizar
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $mantenimientos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>



@include('encargados.modal_nuevo_mantenimiento')
@include('encargados.modal_nuevo_tecnico')
@include('encargados.modal_finalizar_mantenimiento')
@endsection

@push('scripts')
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Exito',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    <!-- SweetAlert2 Error -->
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalMantenimientoEl = document.getElementById('modalNuevoMantenimiento');
            const modalTecnicoEl = document.getElementById('modalNuevoTecnico');
            const formMantenimiento = document.getElementById('formNuevoMantenimiento');
            const formTecnico = document.getElementById('formNuevoTecnico');
            const selectTecnico = document.getElementById('selectTecnicoMantenimiento');
            const btnAbrirModalTecnico = document.getElementById('btnAbrirModalTecnico');
            const btnNuevoTecnicoDirecto = document.getElementById('btnNuevoTecnicoDirecto');
            const btnGuardarTecnico = document.getElementById('btnGuardarTecnico');
            const alertaErrores = document.getElementById('alertaErroresTecnico');
            const listaErrores = document.getElementById('listaErroresTecnico');

            let openedFromMantenimiento = false;

            // 1. Transicion limpia: Abrir modal de tecnico desde el boton (+) en mantenimiento
            if (btnAbrirModalTecnico && modalMantenimientoEl && modalTecnicoEl) {
                btnAbrirModalTecnico.addEventListener('click', function () {
                    this.blur();
                    if (document.activeElement && typeof document.activeElement.blur === 'function') {
                        document.activeElement.blur();
                    }

                    openedFromMantenimiento = true;
                    // Proteger datos del formulario de mantenimiento contra reseteo de form-protection.js
                    if (formMantenimiento) {
                        formMantenimiento.classList.add('no-reset');
                    }

                    const modalMantenimientoInstance = bootstrap.Modal.getInstance(modalMantenimientoEl) || new bootstrap.Modal(modalMantenimientoEl);
                    modalMantenimientoInstance.hide();

                    const modalTecnicoInstance = bootstrap.Modal.getInstance(modalTecnicoEl) || new bootstrap.Modal(modalTecnicoEl);
                    modalTecnicoInstance.show();
                });
            }

            // 2. Abrir directamente desde la cabecera
            if (btnNuevoTecnicoDirecto) {
                btnNuevoTecnicoDirecto.addEventListener('click', function () {
                    openedFromMantenimiento = false;
                });
            }

            // 3. Al ocultarse el modal de tecnico, si provino de mantenimiento, reabrir mantenimiento
            if (modalTecnicoEl) {
                modalTecnicoEl.addEventListener('hidden.bs.modal', function () {
                    limpiarErroresTecnico();
                    if (openedFromMantenimiento) {
                        openedFromMantenimiento = false;
                        if (modalMantenimientoEl) {
                            const modalMantenimientoInstance = bootstrap.Modal.getInstance(modalMantenimientoEl) || new bootstrap.Modal(modalMantenimientoEl);
                            modalMantenimientoInstance.show();

                            // Remover clase no-reset despues de reabrir para permitir reseteo normal al cerrar
                            setTimeout(() => {
                                if (formMantenimiento) {
                                    formMantenimiento.classList.remove('no-reset');
                                }
                            }, 500);
                        }
                    }
                });
            }

            function limpiarErroresTecnico() {
                if (alertaErrores) alertaErrores.classList.add('d-none');
                if (listaErrores) listaErrores.innerHTML = '';
                if (formTecnico) {
                    formTecnico.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    formTecnico.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
                }
            }

            // 4. Envio AJAX del formulario de tecnico
            if (formTecnico) {
                formTecnico.addEventListener('submit', function (e) {
                    e.preventDefault();
                    limpiarErroresTecnico();

                    const originalBtnHtml = btnGuardarTecnico ? btnGuardarTecnico.innerHTML : '';
                    if (btnGuardarTecnico) {
                        btnGuardarTecnico.disabled = true;
                        btnGuardarTecnico.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Guardando...';
                    }

                    const formData = new FormData(formTecnico);
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                               || formTecnico.querySelector('input[name="_token"]')?.value;

                    fetch(formTecnico.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token || ''
                        }
                    })
                    .then(async response => {
                        const data = await response.json().catch(() => ({}));
                        if (response.status === 201 && data.success) {
                            // Agregar y seleccionar en el select de mantenimiento
                            if (selectTecnico && data.tecnico) {
                                const opt = document.createElement('option');
                                opt.value = data.tecnico.id_tecnico;
                                opt.textContent = data.tecnico.nombre_completo;
                                opt.selected = true;
                                selectTecnico.appendChild(opt);
                            }

                            formTecnico.reset();

                            // Cerrar modal de tecnico
                            const modalTecnicoInstance = bootstrap.Modal.getInstance(modalTecnicoEl);
                            if (modalTecnicoInstance) {
                                modalTecnicoInstance.hide();
                            }

                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Tecnico registrado',
                                    text: data.message || 'Tecnico registrado correctamente.',
                                    timer: 2200,
                                    showConfirmButton: false
                                });
                            }
                        } else if (response.status === 422) {
                            // Errores de validacion
                            if (data.errors) {
                                if (listaErrores && alertaErrores) {
                                    alertaErrores.classList.remove('d-none');
                                    Object.keys(data.errors).forEach(field => {
                                        const msg = data.errors[field][0];
                                        const li = document.createElement('li');
                                        li.textContent = msg;
                                        listaErrores.appendChild(li);

                                        const input = formTecnico.querySelector(`[name="${field}"]`);
                                        const feedback = document.getElementById(`feedback_tecnico_${field}`);
                                        if (input) input.classList.add('is-invalid');
                                        if (feedback) feedback.textContent = msg;
                                    });
                                }
                            }
                        } else {
                            const errorMsg = data.message || 'Ocurrio un error inesperado al registrar el tecnico.';
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMsg
                                });
                            } else if (alertaErrores) {
                                alertaErrores.classList.remove('d-none');
                                listaErrores.innerHTML = `<li>${errorMsg}</li>`;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error al registrar tecnico:', error);
                        if (alertaErrores) {
                            alertaErrores.classList.remove('d-none');
                            listaErrores.innerHTML = '<li>Error de conexion con el servidor. Intente nuevamente.</li>';
                        }
                    })
                    .finally(() => {
                        if (btnGuardarTecnico) {
                            btnGuardarTecnico.disabled = false;
                            btnGuardarTecnico.innerHTML = originalBtnHtml;
                        }
                    });
                });
            }
        });
    </script>
@endpush
