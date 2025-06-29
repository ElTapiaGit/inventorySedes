@extends('layouts.pagClinica')

@section('title', 'Reportes-clinica')

@section('content')
<main class="container p-3">
    <div class="container-fluid">
        <h2 class="mb-4">Opciones de Reportes</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Equipos de Odontologia</h5>
                        <p class="card-text">Reporte sobre la cantidad de equipos adquiridos para la clinica odontologicos.</p>
                        <a href="{{ route('reporte.clinica.equipos')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Contenido de Muebles y Materiales de la Clinica</h5>
                        <p class="card-text">Detalles sobre el contenido de los ambientes de la clinica.</p>
                        <a href="{{route('reporte.clinica.contenido')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Estado de Equipos odontologicos de la Clinica Odontologica</h5>
                        <p class="card-text">Lista de equipo segun el estado funcional y nombre de equipo de la Clinica Odontologica.</p>
                        <a href="{{route('report.estado.equiposClinica')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Equipos Descartados</h5>
                        <p class="card-text">Detalles de los Equipos deshechos/descartados.</p>
                        <a href="{{route('reporte.equiposDescartadOdont')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Accesorios que Mas se Cambian</h5>
                        <p class="card-text">Detalles de los accesorios que mas se reponen/cambian.</p>
                        <a href="{{route('reporte.accesorioCambiadOdont')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</main>
@endsection

@section('dropdown-items')
    @foreach($edificios as $edificio)
        @if($edificio->nombre_edi == 'Edificio Central')
            <li><a class="dropdown-item" href="{{ route('coordinator.inicio') }}">{{ $edificio->nombre_edi }}</a></li>
        @elseif($edificio->nombre_edi == 'Clinica Odontologia')
            <li><a class="dropdown-item" href="{{ route('coordinator.clinica.inicio') }}">{{ $edificio->nombre_edi }}</a></li>
        @else
            <li><a class="dropdown-item" href="#">{{ $edificio->nombre_edi }}</a></li>
        @endif
    @endforeach
@endsection