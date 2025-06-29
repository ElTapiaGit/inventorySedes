@extends('layouts.pagClinica')

@section('title', 'Detalles de Mobiliario')

@section('content')
<main class="content px-3 py-2">
    <div class="container-fluid">
        <h1 class="text-center fw-bold">DETALLES DE MUEBLE</h1>

        <!-- Información del mueble -->
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card ml-auto d-flex">
                    <div class="card-header bg-info text-dark">
                        <h5>Información del mueble</h5>
                    </div>
                    <div class="card-body d-flex flex-column flex-md-row">
                        <!-- Aquí mostrar la información del mueble -->
                        <div class="flex-grow-1 mb-3 mb-md-0" style="max-width: 500px">
                            <p><strong>Código:</strong> {{ $mobiliario->cod_mueble }}</p>
                            <p><strong>Tipo de Mueble:</strong> {{ $mobiliario->tipo_mueble }}</p>
                            <p style="text-align: justify;"><strong>Descripción:</strong><br> {{ $mobiliario->descripticion_mueb }} </p>
                            <p style="text-align: justify;"><strong>Observaciones:</strong> <br> {{ $mobiliario->observacion }}</p>
                            <p><strong>Estado:</strong> {{ $mobiliario->estado_mueb }}</p>
                            <p><strong>Vida Útil:</strong> {{ $mobiliario->vida_util }}</p>
                            <p><strong>Fecha Registrado:</strong> {{ \Carbon\Carbon::parse($mobiliario->fch_registro)->format('d/m/Y') }}</p>
                        </div>
                    
                        <!-- Imagen del mueble en el lado derecho -->
                        <div class="ml-md-3 px-2">
                            @if($mobiliario->ruta_foto)
                                <img src="{{ asset($mobiliario->ruta_foto) }}" alt="Foto del mueble" class="img-fluid img-thumbnail">
                            @else
                                <p>No hay foto disponible</p>
                            @endif
                        </div>
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