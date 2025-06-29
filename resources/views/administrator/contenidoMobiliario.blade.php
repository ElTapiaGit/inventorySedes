@extends('layouts.admin')

@section('title', 'Mobiliarios Ambiente')

@section('content')
<main class="content px-3 pagAmbMovi">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center titulo fw-bold">Contenido del {{ $ambiente->tipoAmbiente->nombre_amb }} - {{ $ambiente->nombre }}</h3>
        </div>
        <div class="btnambiente d-flex justify-content-center mb-3">
            <!--BTN Equipos-->
            <a href="{{ route('contenidoequipos.index', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">EQUIPOS</a>

            <!--BTN Material-->
            <a href="{{ route('materialesAdmin.show', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">MATERIALES</a>
            
            <!-- BTN Mobiliarios -->
            <a href="{{ route('contenidomobiliario.index', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">MOBILIARIO</a>

        </div>
        <div class="mb-3">
            <h4>Lista de Mobiliarios:</h4>
        </div>
        
        @if($mobiliarios->isEmpty())
        <script>
            Swal.fire({
                icon: 'info',
                title: 'No existen mobiliario en el laboratorio {{ $ambiente->nombre }}',
                showConfirmButton: false,
                timer: 3000
            });
        </script>
        @else
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nro.</th>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                        <th>Vida Útil</th>
                        <th>Fecha Registrada</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $contador = 1;
                    @endphp
                    @foreach($mobiliarios as $mobiliario)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>{{ $mobiliario->cod_mueble }}</td>
                            <td>{{ $mobiliario->tipoMobiliario->tipo_mueble}}</td> <!-- Asumiendo que tienes una relación tipoMobiliario definida en tu modelo -->
                            <td>{{ $mobiliario->estado_mueb }}</td>
                            <td>{{ $mobiliario->observacion }}</td>
                            <td>{{ $mobiliario->vida_util }}</td>
                            <td>{{ \Carbon\Carbon::parse($mobiliario->fch_registro)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('mobiliarioAdmin.detalles', ['cod_mueble' => Crypt::encryptString($mobiliario->cod_mueble)]) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</main>
@endsection
