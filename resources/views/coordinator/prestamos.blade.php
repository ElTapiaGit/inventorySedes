@extends('layouts.coordinator')

@section('title', 'Préstamos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/prestamos.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 pagPrestamo">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">PRESTAMOS REALIZADOS {{ isset($edificioActivo) ? '- ' . strtoupper($edificioActivo->nombre_edi) : '' }}</h3>
        </div>
    
        <div class="row mb-3 g-2">
            <!-- Formulario de búsqueda por nombre de usuario -->
            <div class="col-md-6 d-flex justify-content-center">
                <form method="GET" action="{{ route($isClinica ? 'clinica.prestamos.buscar' : 'prestamos.buscar') }}" class="d-flex align-items-center">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <label for="usuario_prestamo" class="visually-hidden">Nombre Prestatario</label>
                        <input type="text" class="form-control" id="usuario_prestamo" name="usuario" placeholder="Nombre Prestatario" value="{{ request('usuario') }}" aria-label="Nombre Prestatario">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>

            <!-- Formulario de búsqueda por fecha -->
            <div class="col-md-6 d-flex justify-content-center">
                <form method="GET" action="{{ route($isClinica ? 'clinica.prestamos.buscar' : 'prestamos.buscar') }}" class="d-flex align-items-center">
                    @csrf
                    <label for="fecha_inicio" class="visually-hidden">Fecha de inicio</label>
                    <input type="date" class="form-control me-2 w-50" id="fecha_inicio" name="fecha_inicio" placeholder="Fecha de inicio" value="{{ request('fecha_inicio') }}" aria-label="Fecha de inicio">
                    <label for="fecha_fin" class="visually-hidden">Fecha de fin</label>
                    <input type="date" class="form-control me-2 w-50" id="fecha_fin" name="fecha_fin" placeholder="Fecha de fin" value="{{ request('fecha_fin') }}" aria-label="Fecha de fin">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="tablaPrestamo">
                    <tr>
                        <th>Prestatarios</th>
                        <th>Descripción</th>
                        <th>Fecha Prestada</th>
                        <th>Encargado</th>
                        <th>Fecha Devolución</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestamos as $prestamo)
                        <tr>
                            <td>{{ $prestamo->nombre_solicitante }}</td>
                            <td>{{ $prestamo->descripcion_prestamo }}</td>
                            <td>{{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}</td>
                            <td>{{ $prestamo->encargado }}</td>
                            <td>{{ \Carbon\Carbon::parse($prestamo->fch_devolucion)->format('d/m/Y') ?? 'No devuelto' }}</td>
                            <td class="text-center">
                                <a href="{{ route($isClinica ? 'clinica.prestamos.detalles' : 'prestamos.detalles', ['token' => Crypt::encrypt($prestamo->id_prestamo)]) }}" title="Detalles">
                                    <i class="bi bi-plus-square-fill"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $prestamos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <!-- Script para cambiar fondo de celda si hay devolucion -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const celdasFechaDevolucion = document.querySelectorAll('table tbody tr td:nth-child(5)');
    
            celdasFechaDevolucion.forEach(celda => {
                if (celda.textContent.trim() === 'No devuelto' || celda.textContent.trim() === '') {
                    celda.style.backgroundColor = '#f45963';
                    celda.style.color = 'white';
                }
            });
        });
    </script>

    <!-- Mensajes cuando no hay resultados en la búsqueda -->
    @if(session('error_usuario'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Oops...',
                text: '{{ session('error_usuario') }}'
            }).then(() => {
                window.location.href = "{{ route($isClinica ? 'clinica.prestamos.index' : 'prestamos.index') }}";
            });
        </script>
    @endif

    @if(session('error_fecha'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Oops...',
                text: '{{ session('error_fecha') }}'
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
