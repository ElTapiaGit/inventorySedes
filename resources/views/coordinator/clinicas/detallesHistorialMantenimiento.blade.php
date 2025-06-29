@extends('layouts.pagClinica')

@section('title', 'Detalles Historial')

@section('content')
<main class="content px-3 detallesMantenimiento">
    <div class="container-fluid">
        <div class="mb-3">
            <h2 class="text-center fw-bold">DETALLES DEL MANTENIMIENTO</h2>
        </div>
        <div class="row">
            <div class="col-md-8">
                <!-- Datos del mantenimiento -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4>Datos del Mantenimiento</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Informe Inicial:</strong> <br> &nbsp; &nbsp;{{ $mantenimiento->informe_inicial }}</p>
                        <p><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($mantenimiento->fch_inicio)->format('d/m/Y') }}</p>
                        
                        @if ($mantenimiento->informe_final !== null && $mantenimiento->fch_final !== null)
                            <p><strong>Informe Final:</strong><br>&nbsp; &nbsp;{{ \Carbon\Carbon::parse($mantenimiento->informe_final)->format('d/m/Y') }}</p>
                            <p><strong>Fecha de Finalización:</strong> {{ $mantenimiento->fch_final }}</p>
                        @else
                            <h3 style="color: red"><strong>Aún no se ha finalizado el mantenimiento.</strong></h3>
                        @endif
                    </div>
                </div>

                <!-- Datos del artículo -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4>Datos del Artículo</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Código:</strong> {{ $articulo->codigo }}</p>
                        <p><strong>Nombre:</strong> {{ $articulo->nombre }}</p>
                    </div>
                </div>

                <!-- Datos del técnico -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4>Datos del Técnico</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $mantenimiento->nombre }}</p>
                        <p><strong>Celular:</strong> {{ $mantenimiento->celular }}</p>
                        <p><strong>Dirección:</strong> {{ $mantenimiento->direccion }}</p>
                    </div>
                </div>

                <!-- Datos del personal encargado -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4>Datos del Personal Encargado</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $personal->nombre_completo }}</p>
                        <p><strong>Celular:</strong> {{ $personal->celular }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Foto del artículo -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4>Foto del Artículo</h4>
                    </div>
                    <div class="card-body text-center">
                        @if($mantenimiento->ruta_foto)
                            <img src="{{ asset($mantenimiento->ruta_foto) }}" alt="Foto del artículo" class="img-fluid">
                        @else
                            <p>No hay foto disponible.</p>
                        @endif
                    </div>
                </div>
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