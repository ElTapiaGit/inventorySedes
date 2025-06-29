@extends('layouts.admin')

@section('title', 'Detalles Prestamos')

@section('content')
<main class="content px-3 pagDetallesprestamo">
    <div class="container-fluid">

        <div class="card mb-4 my-4">
            <div class="card-header">
                <h4 class="card-title">Información del Préstamo</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Prestatario:</strong> {{ $prestamo->nombre_solicitante }}</li>
                    <li class="list-group-item"><strong>Fecha Prestada:</strong> {{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}</li>
                    <li class="list-group-item"><strong>Hora:</strong> {{ $prestamo->hora_prestamo }}</li>
                    <li class="list-group-item"><strong>Descripción del Préstamo:</strong> <br> {{ $prestamo->descripcion_prestamo }}</li>
                    <li class="list-group-item"><strong>Encargado Responsable:</strong> {{ $prestamo->personal->nombrecompleto}}</li>
                </ul>
            </div>
        </div>
        <!-- para detalles de articulos prestados -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Detalles de Artículos Prestados</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                            <th>Ambiente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detalles_prestamos as $detalle)
                            <tr>
                                <td>{{ $detalle['cod_equipo']}}</td>
                                <td>{{ $detalle['nombre_equipo'] }}</td>
                                <td>{{ $detalle['observacion_detalle'] }}</td>
                                <td>{{ $detalle['estado_equipo']}}</td>
                                <td>{{ $detalle['ambiente_equipo'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Detalles de Devolución</h4>
                <ul class="list-group list-group-flush">
                    @if ($prestamo->fch_devolucion)
                        <li class="list-group-item"><strong>Fecha de Devolución:</strong> {{ \Carbon\Carbon::parse($prestamo->fch_devolucion)->format('d/m/Y') }}</li>
                        <li class="list-group-item"><strong>Hora:</strong> {{ $prestamo->hora_devolucion }}</li>
                        <li class="list-group-item"><strong>Descripción de Devolución:</strong> {{ $prestamo->descripcion_devolucion }}</li>
                        <li class="list-group-item"><strong>Encargado de Recibir:</strong> {{ $prestamo->nombre_encargados }}</li>
                    @else
                        <h3 style="color: red">No hay detalles de devolución disponibles.</h3>
                    @endif
                </ul>
                
            </div>
        </div>
    </div>
</main>
@endsection
