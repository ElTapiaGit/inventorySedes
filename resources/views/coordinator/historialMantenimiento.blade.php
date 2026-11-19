@extends('layouts.coordinator')

@section('title', 'Historial de Mantenimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/mantenimiento.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 pagHistorial">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">HISTORIAL DE MANTENIMIENTOS {{ isset($edificioActivo) ? '- ' . strtoupper($edificioActivo->nombre_edi) : '' }}</h3>
        </div>

        <form action="{{ route($isClinica ? 'clinica.historialmantenimientos.index' : 'historialmantenimientos.index') }}" method="GET" class="d-flex justify-content-center">
            <div class="row justify-content-center">
                <div class="col-md-3 mb-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <label for="cod_equipo" class="visually-hidden">Código Artículo</label>
                        <input type="text" name="cod_equipo" id="cod_equipo" class="form-control" placeholder="Código Artículo" value="{{ request('cod_equipo') ?? request('cod_articulo') }}" aria-label="Código Artículo">
                    </div>
                </div>

                <div class="col-md-3 mb-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <label for="fch_inicio" class="visually-hidden">Fecha Inicio</label>
                        <input type="date" name="fch_inicio" id="fch_inicio" class="form-control" value="{{ request('fch_inicio') }}" aria-label="Fecha Inicio">
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <label for="fch_fin" class="visually-hidden">Fecha Fin</label>
                        <input type="date" name="fch_fin" id="fch_fin" class="form-control" value="{{ request('fch_fin') }}" aria-label="Fecha Fin">
                    </div>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>

        <div class="mt-3">
            <h4>Historial de Mantenimientos:</h4>
        </div>

        @if($mantenimientos->isEmpty())
            <div class="alert alert-info text-center mt-3">
                No hay historial de mantenimientos registrados.
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="tablaHistorial">
                    <tr>
                        <th>Código</th>
                        <th>Nombre Artículo</th>
                        <th>Técnico Responsable</th>
                        <th>Fecha Inicio</th>
                        <th>Ambiente</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mantenimientos as $mantenimiento)
                        <tr>
                            <td>{{ $mantenimiento->cod_articulo }}</td>
                            <td>{{ $mantenimiento->nombre_articulo }}</td>
                            <td>{{ $mantenimiento->nombre_tecnico }}</td>
                            <td>{{ \Carbon\Carbon::parse($mantenimiento->fch_inicio)->format('d/m/Y') }}</td>
                            <td>{{ $mantenimiento->nombre_ambiente }}</td>
                            <td>
                                <a href="{{ route($isClinica ? 'clinica.detallesmantenimientos.mostrar' : 'detallesmantenimientos.mostrar', ['id' => encrypt($mantenimiento->id_mantenimiento_ini)]) }}" title="Detalles">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $mantenimientos->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mensaje sin resultado de búsqueda -->
    @if(session('errorbuscar'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin Resultados',
                text: '{{ session('errorbuscar') }}',
            });
        </script>
    @endif
@endpush
