@extends('layouts.pagClinica')

@section('title', 'Detalles-Prestamo-clinica')

@section('content')
<main class="content px-3 pagDetallesprestamo">
    <div class="container-fluid">

        <div class="mb-3">
            <h2 class="text-center my-4 fw-bold">Detalles del Préstamo</h2>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Información del Préstamo</h4>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Prestatario:</strong> {{ $prestamo->nombre_solicitante }}</li>
                    <li class="list-group-item"><strong>Fecha Prestada:</strong> {{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}</li>
                    <li class="list-group-item"><strong>Hora:</strong> {{ $prestamo->hora_prestamo }}</li>
                    <li class="list-group-item"><strong>Descripcion del Prestamo:</strong> {{ $prestamo->descripcion_prestamo }}</li>
                    <li class="list-group-item"><strong>Encargado Responsable:</strong> {{ $prestamo->nombre_encargado }}</li>
                </ul>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Detalles de Articulos Prestados</h4>
                @if($detalle_prestamos)
                <table class="table table-bordered">
                    <thead class="tablaCabecera">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                            <th>Ambiente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detalle_prestamos as $detalle)
                            <tr>
                                <td>{{ $detalle->cod_equipo }}</td>
                                <td>{{ $detalle->nombre_equipo }}</td>
                                <td>{{ $detalle->observacion_detalle }}</td>
                                <td>{{ $detalle->estado_equipo }}</td>
                                <td>{{ $detalle->ambiente_equipo }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
                @if(!$detalle_prestamos)
                    <p style="color: red">No hay detalles de los articulo de prestamo.</p>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Detalles de Devolución</h4>
                @if ($prestamo->fch_devolucion)
                    <p><strong>Fecha de Devolución:</strong> {{ \Carbon\Carbon::parse($prestamo->fch_devolucion)->format('d/m/Y') }}</p>
                    <p><strong>Hora:</strong> {{ $prestamo->hora_devolucion }}</p>
                    <p><strong>Descripción de Devolución:</strong> {{ $prestamo->devolucion_descripcion }}</p>
                    <p><strong>Encargado de Recibir:</strong> {{ $prestamo->nombre_encargado }}</p>
                @else
                    <h3 style="color: red">No hay detalles de devolución disponibles.</h3>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection

<!--Para mostrar el desplegable de edificios de la sede central-->
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