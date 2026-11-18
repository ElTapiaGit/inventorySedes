@extends('layouts.encargado')

@section('title', 'Gestión de Mantenimientos')

@section('content')
<div class="content mt-2">
    <!-- Encabezado Estandarizado -->
    <div class="encargado-page-header">
        <h1>
            <i class="bi bi-wrench-adjustable text-primary"></i> Gestión de Mantenimientos
        </h1>
        <div>
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
@include('encargados.modal_finalizar_mantenimiento')
@endsection

@push('scripts')
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
@endpush
