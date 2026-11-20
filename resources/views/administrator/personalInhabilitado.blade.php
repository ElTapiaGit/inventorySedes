@extends('layouts.admin')

@section('title', 'Personal Inhabilitado')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/personal.css') }}">
@endpush

@section('content')
<main class="content p-3 pagAbiliarPers">
    <div class="container-fluid">
        <!-- Cabecera de seccion con boton Volver -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="bi bi-person-x me-2 text-primary"></i>Personal Inhabilitado
                </h2>
                <p class="text-muted small mb-0">Listado de personal dado de baja y reactivacion en el sistema.</p>
            </div>
            <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Volver a Personal</span>
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Apellido Paterno</th>
                                <th>Apellido Materno</th>
                                <th>Nombre</th>
                                <th>Celular</th>
                                <th>Tipo de Personal</th>
                                <th>Area de Trabajo</th> 
                                <th class="text-center" style="width: 130px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @forelse ($personales as $personal)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                    <td>{{ $personal->ap_paterno }}</td>
                                    <td>{{ $personal->ap_materno }}</td>
                                    <td class="fw-semibold text-dark">{{ $personal->nombre }}</td>
                                    <td>{{ $personal->celular }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $personal->tipoPersonal->descripcion_per ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <i class="bi bi-building me-1 text-secondary"></i>
                                        {{ $personal->edificio && $personal->edificio->sede ? $personal->edificio->sede->nombre : '-' }} - {{ $personal->edificio ? $personal->edificio->nombre_edi : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1" onclick="confirmReactivar({{ $personal->id_personal }}, '{{ $personal->nombre }} {{ $personal->ap_paterno }}')">
                                            <i class="bi bi-check-circle"></i>
                                            <span>Reactivar</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>No hay personal inhabilitado registrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <!-- Script para reactivar personal -->
    <script>
        function confirmReactivar(id, nombreCompleto) {
            Swal.fire({
                title: '¿Esta seguro?',
                text: "Esta a punto de reactivar a " + nombreCompleto + ".",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, reactivar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('/administrator/personal-inhabilitado/reactivar/') }}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(response => {
                        if (response.ok) {
                            Swal.fire(
                                'Reactivado',
                                'El personal ha sido reactivado exitosamente.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error',
                                'Hubo un problema al reactivar el personal.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endpush
