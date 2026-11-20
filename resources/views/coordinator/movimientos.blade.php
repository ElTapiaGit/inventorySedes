@extends('layouts.coordinator')

@section('title', 'Movimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/movimientos.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 py-3 pagMovimientos">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold fs-5 fs-sm-4" style="color: #0E0D39;">
                <i class="bi bi-door-open me-2 text-primary"></i>Uso de Ambientes {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }}
            </h4>
        </div>
        
        <!-- Barra de filtros y busqueda -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
            <form method="POST" action="{{ route($isClinica ? 'clinica.movimientos.buscar' : 'movimientos.buscar') }}">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="usuario" class="form-label text-muted small fw-semibold">Nombre del Docente</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="usuario" name="usuario" list="usuarios" placeholder="Seleccione o escriba el docente..." aria-label="Nombre de Docente">
                            <datalist id="usuarios">
                                @foreach($nombresUsuarios as $usuario)
                                    <option value="{{ $usuario->nombre }}">{{ $usuario->nombre }}</option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="fecha" class="form-label text-muted small fw-semibold">Fecha (dd/mm/yyyy)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-calendar3"></i></span>
                            <input type="text" class="form-control" id="fecha" name="fecha" placeholder="dd/mm/yyyy" aria-label="Fecha (dd/mm/yyyy)">
                        </div>
                    </div>

                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button class="btn btn-primary d-flex align-items-center flex-grow-1 justify-content-center" type="submit">
                            <i class="bi bi-search me-1"></i> Buscar
                        </button>
                        <a href="{{ route($isClinica ? 'clinica.movimientos.index' : 'movimientos.index') }}" class="btn btn-outline-secondary" title="Ver todos">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de Uso de Ambientes -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                    <i class="bi bi-list-check me-2 text-primary"></i> Registros de Uso de Ambientes
                </h5>
                <span class="badge bg-secondary rounded-pill">{{ count($movimientos) }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-ulat align-middle mb-0">
                    <thead class="table-ulat-header">
                        <tr>
                            <th>Ambiente</th>
                            <th>Estado</th>
                            <th>Docente / Usuario</th>
                            <th>Actividad</th>
                            <th>Semestre</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $movimiento)
                            <tr>
                                <td class="fw-bold text-dark">{{ $movimiento->nombre_ambiente }}</td>
                                <td class="text-center">
                                    @if($movimiento->uso_finalizado == 0)
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-hourglass-split me-1"></i> En Curso
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i> Finalizado
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-semibold text-secondary">{{ $movimiento->nombre_usuario }}</td>
                                <td class="small text-muted">{{ $movimiento->descripcion ?: 'Sin descripcion' }}</td>
                                <td class="text-center">{{ $movimiento->semestre ?: 'N/A' }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($movimiento->fch_uso)->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $movimiento->hora_uso }}</td>
                                <td class="text-center">
                                    <a href="{{ route($isClinica ? 'clinica.movimientos.detalles' : 'movimientos.detalles', encrypt($movimiento->id_uso_ambiente)) }}" class="btn btn-outline-primary btn-sm" title="Ver ficha del movimiento">
                                        <i class="bi bi-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No se encontraron registros de uso de ambientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mensaje cuando no hay usuarios -->
    @if(session('error_usuario'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Atencion',
                text: '{{ session('error_usuario') }}'
            });
        </script>
    @endif

    <!-- Mensaje cuando no hay fecha -->
    @if(session('error_fecha'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Atencion',
                text: '{{ session('error_fecha') }}'
            });
        </script>
    @endif
@endpush
