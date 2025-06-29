@extends('layouts.coodinacion')

@section('title', 'Reportes')

@section('content')
<main class="container p-3">
    <div class="container-fluid">
        <h2 class="mb-4">Opciones de Reportes</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Equipos de Odontologia</h5>
                        <p class="card-text">Reporte sobre la cantidad y estado de los equipos odontologicos adquiridos.</p>
                        <a href="{{ route('reporte.equiposOdont')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Muebles y Materiales de los Ambientes Odontologia</h5>
                        <p class="card-text">Lista de mobiliarios y materiales adquiridos, y contenido de los laboratorios.</p>
                        <a href="{{route('reporte.ambiente.odontologia')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Estado de Equipos odontologicos del Edificio Principal</h5>
                        <p class="card-text">Lista de equipo segun el estado funcional y nombre de equipo del edificio principal.</p>
                        <a href="{{route('report.estado.equipos')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
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