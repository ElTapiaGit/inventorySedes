@extends('layouts.admin')

@section('title', 'Materiales Ambiente')

@section('content')
<main class="content px-3 pagMateAdmin">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center titulo fw-bold">Contenido <br> {{ $ambiente->tipoAmbiente->nombre_amb }} - {{ $ambiente->nombre }}</h3>
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
            <h4>Materiales del Laboratorio:</h4>
        </div>
        
        @if($materiales->isEmpty())
        <script>
            Swal.fire({
                icon: 'info',
                title: 'No existen materiales en el {{ $ambiente->nombre }}',
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
                        <th>Fecha Registrada</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $contador = 1;
                    @endphp
                    @foreach($materiales as $material)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>{{ $material->cod_mate }}</td>
                            <td>{{ $material->tipo_mate }}</td>
                            <td>{{ $material->estado_mate }}</td>
                            <td>{{ $material->observacion_mate }}</td>
                            <td>{{ \Carbon\Carbon::parse($material->fch_registrada)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('maeterialAdmin.detalles', ['token' => Crypt::encryptString($material->cod_mate)]) }}" class="btn btn-sm btn-info">
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
