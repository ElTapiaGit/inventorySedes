@extends('layouts.coodinacion')

@section('title', 'Equipos del Laboratorio')

@section('content')
<main class="content px-3 ambiente-page">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center titulo fw-bold">{{ $ambiente->nombre }}</h3>
        </div>
        <div class="btnambiente d-flex justify-content-center mb-3">
            <a href="{{ route('equipos.show', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">EQUIPOS</a>
            <a href="{{ route('materiales.show', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">MATERIALES</a>
            <a href="{{ route('mobiliarios.show', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">MOBILIARIO</a>
        </div>
        <div class="mb-3">
            <h4>Equipos del Laboratorio:</h4>
        </div>
        
        @if($equipos->isEmpty())
        <script>
            Swal.fire({
                icon: 'info',
                title: 'No existen equipos en el {{ $ambiente->nombre }}',
                showConfirmButton: false,
                timer: 3000
            });
        </script>

        @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nro.</th>
                        <th>Código</th>
                        <th>Nombre</th>
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
                    @foreach($equipos as $equipo)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>{{ $equipo->cod_equipo }}</td>
                            <td>{{ $equipo->nombre_equi }}</td>
                            <td>{{ $equipo->estado_equi }}</td>
                            <td>{{ $equipo->observaciones_equi }}</td>
                            <td>{{ $equipo->vida_util }}</td>
                            <td>{{ \Carbon\Carbon::parse($equipo->fch_registro)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('equipo.detalles', ['token' => Crypt::encrypt($equipo->cod_equipo)]) }}" title="Detalles">
                                    <i class="fas fa-info-circle"></i>
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