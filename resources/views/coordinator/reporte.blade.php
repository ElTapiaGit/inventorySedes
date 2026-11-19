@extends('layouts.coordinator')

@section('title', 'Reportes')

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="container p-3">
    <div class="container-fluid">
        <h2 class="mb-4">Opciones de Reportes {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }}</h2>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Equipos {{ isset($carreraCoordinada) ? 'de ' . $carreraCoordinada : '' }}</h5>
                        <p class="card-text">Reporte sobre la cantidad y estado de los equipos adquiridos.</p>
                        <a href="{{ route($isClinica ? 'reporte.clinica.equipos' : 'reporte.equiposOdont') }}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Muebles y Materiales</h5>
                        <p class="card-text">Lista de mobiliarios y materiales adquiridos y contenido de los ambientes.</p>
                        <a href="{{ route($isClinica ? 'reporte.clinica.contenido' : 'reporte.ambiente.odontologia') }}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Estado de Equipos</h5>
                        <p class="card-text">Lista de equipos según su estado funcional y ubicación.</p>
                        <a href="{{ route($isClinica ? 'report.estado.equiposClinica' : 'report.estado.equipos') }}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Equipos Descartados</h5>
                        <p class="card-text">Detalles de los equipos deshechos o descartados.</p>
                        <a href="{{ route('reporte.equiposDescartadOdont') }}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Accesorios Reemplazados</h5>
                        <p class="card-text">Detalles de los accesorios que más se reponen o cambian.</p>
                        <a href="{{ route('reporte.accesorioCambiadOdont') }}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
