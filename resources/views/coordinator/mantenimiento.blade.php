@extends('layouts.coordinator')

@section('title', 'Mantenimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/mantenimiento.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 pagMantenimiento">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">MANTENIMIENTOS {{ isset($edificioActivo) ? '- ' . strtoupper($edificioActivo->nombre_edi) : '' }}</h3>
        </div>
        <div class="d-flex justify-content-center mb-4">
            <a href="{{ route($isClinica ? 'clinica.historialmantenimientos.index' : 'historialmantenimientos.index') }}" class="btn btn-primary mx-2">Historial de mantenimiento</a>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4>Equipos Para Mantenimiento:</h4>
            <a href="{{ route('print.equipos') }}" target="_blank" class="btn btn-success">Imprimir</a>
        </div>

        @if($equipos->isEmpty())
            <div class="alert alert-info text-center">
                No hay equipos registrados para mantenimiento.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="tablaMantenimiento">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                            <th>Ambiente</th>
                        </tr>
                    </thead>
                    <tbody class="tbodyMante">
                        @foreach($equipos as $equipo)
                            <tr>
                                <td>{{ $equipo->cod_equipo ?? $equipo->Cod_equipo }}</td>
                                <td>{{ $equipo->nombre_equi }}</td>
                                <td>{{ $equipo->observaciones_equi }}</td>
                                <td>{{ $equipo->estado_equi }}</td>
                                <td>{{ $equipo->nombre_ambiente }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $equipos->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mensaje cuando no hay equipos para mantenimiento -->
    @if($equipos->isEmpty())
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin equipos para mantenimiento',
                text: 'No hay equipos registrados para mantenimiento.',
                confirmButtonText: 'Entendido'
            });
        </script>
    @endif

    <!-- Mensaje de error de comunicación con la BD -->
    @if(session('errordata'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errordata') }}',
            });
        </script>
    @endif
@endpush
