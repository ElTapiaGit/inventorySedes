@extends('layouts.encargado')

@section('title', 'Gestión de Mantenimientos')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bolder">Mantenimientos Registrados</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoMantenimiento">
            <i class="bi bi-plus-circle"></i> Nuevo Mantenimiento
        </button>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
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
                title: '¡Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            });
        </script>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover text-center align-middle">
            <thead class="table-success">
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
@include('encargados.modal_finalizar_mantenimiento')
@endsection
