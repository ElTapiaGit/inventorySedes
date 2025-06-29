@extends('layouts.admin')

@section('title', 'Reportes')

@section('content')
<main class="container p-3">
    <div class="container-fluid">
        <h2 class="mb-4">Opciones de Reportes</h2>
        <!-- Seccion de para mostrar los diferentes reportes -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Equipos Adquiridos</h5>
                        <p class="card-text">Reporte sobre la cantidad de equipos Adquiridos.</p>
                        <a href="{{ route('reportes.equipos-adquiridos') }}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Mobiliario y Materiales Adquiridos</h5>
                        <p class="card-text">Reporte sobre la cantidad de mobiliarios y materiales Adquiridos.</p>
                        <a href="{{ route('reportes.mobiliarioMate') }}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Accesorios Adquiridos</h5>
                        <p class="card-text">Reporte sobre la cantidad de accesorios Adquiridos.</p>
                        <a href="{{ route('reportes.accesorioAdquirido')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Resumen de Personal</h5>
                        <p class="card-text">Desglose del personal por tipo y estado.</p>
                        <a href="{{ route('reportes.personal') }}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>  

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Reporte de Ambientes</h5>
                        <p class="card-text">Desglose del ambientes por niveles, edificios y sedes.</p>
                        <a href="{{route('reportes.ambientes')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Accesorios que Mas se Cambian</h5>
                        <p class="card-text">Detalles de los accesorios que se reponen/cambian.</p>
                        <a href="{{ route('reportes.accesoriosMasRepuesto')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Articulos Descartados</h5>
                        <p class="card-text">detalles de los articulos deshechos/descartados.</p>
                        <a href="{{ route('reportes.articulosDescartados') }}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Estado de Equipos</h5>
                        <p class="card-text">reporte por estado funcional de equipos de los edificios.</p>
                        <a href="{{route('reportes.estado.equipos')}}" target="_blank" class="btn btn-primary">Ver Reporte</a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</main>
@endsection
