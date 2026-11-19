@extends('layouts.coordinator')

@section('title', 'Accesorios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/accesorio.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 pagacces">
    <div class="container-fluid">
        <!-- Título de la página -->
        <div class="mb-4">
            <h3 class="text-center fw-bold">LISTA DE ACCESORIOS</h3>
        </div>
        <!-- Botones de navegación -->
        <div class="d-flex justify-content-center mb-4">
            <a href="{{ route($isClinica ? 'clinica.accesorios.equipo' : 'accesorios.equipo') }}" class="btn btn-primary mx-2">Equipo con Accesorios</a>
            <a href="{{ route($isClinica ? 'clinica.accesorios.unicos' : 'accesorios.unicos') }}" class="btn btn-secondary mx-2">Accesorios Únicos</a>
        </div>
        <!-- Formulario de búsqueda -->
        <div class="row mb-3">
            <div class="col-md-6 d-flex justify-content-center align-items-center my-3">
                <form method="GET" action="{{ route($isClinica ? 'clinica.accesorios.buscarCodigo' : 'accesorios.buscarCodigo') }}" class="personaliacces">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <label for="buscar_accesorio_codigo" class="visually-hidden">Buscar por código</label>
                        <input type="text" id="buscar_accesorio_codigo" class="form-control" name="codigo" placeholder="Buscar por código" value="{{ request('codigo') }}" aria-label="Buscar por código">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <form method="GET" action="{{ route($isClinica ? 'clinica.accesorios.buscarNombre' : 'accesorios.buscarNombre') }}" class="personaliacces">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <label for="buscar_accesorio_nombre" class="visually-hidden">Buscar por nombre</label>
                        <input type="text" id="buscar_accesorio_nombre" class="form-control" name="nombre" placeholder="Buscar por nombre" value="{{ request('nombre') }}" aria-label="Buscar por nombre">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Tabla de accesorios -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="tablaAcces">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Observaciones</th>
                        <th>Estado</th>
                        <th>Ubicación</th>
                        <th>Registrada</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accesorios as $accesorio)
                        <tr>
                            <td>{{ $accesorio->cod_accesorio }}</td>
                            <td>{{ $accesorio->nombre_acce }}</td>
                            <td>{{ $accesorio->observacion_ace }}</td>
                            <td>{{ $accesorio->estado_acce }}</td>
                            <td>{{ $accesorio->ubicacion }}</td>
                            <td>{{ \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route($isClinica ? 'clinica.accesorios.show' : 'accesorios.show', $accesorio->cod_accesorio) }}" title="Más Información">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $accesorios->links('pagination::bootstrap-5') }}
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mensaje cuando no se encuentra resultados de búsqueda -->
    @if (session('errorbuscar'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Búsqueda NO Encontrada',
                text: '{{ session('errorbuscar') }}'
            });
        </script>
    @endif

    <!-- Mensaje de error en la comunicación a BD -->
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
