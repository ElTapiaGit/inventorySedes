@extends('layouts.pagClinica')

@section('title', 'Contenido-Clinica')

@section('content')
<main class="content px-3 ambiente-clinica">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center titulo fw-bold">Contenido del &nbsp;{{ $ambiente->nombre_completo_ambiente }}</h3>
        </div>

        <div class="btnambiente d-flex justify-content-center mb-3">
            <a href="{{ route('coordinator.clinica.equipos', ['id_ambiente' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">EQUIPOS</a>
            <a href="{{ route('coordinator.clinica.materiales', ['id_ambiente' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">MATERIALES</a>
            <a href="{{ route('coordinator.clinica.mobiliarios', ['id_ambiente' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">MOBILIARIA</a>
        </div>

        @if($equipos->isEmpty() && $materiales->isEmpty() && $mobiliarios->isEmpty())
        <script>
            Swal.fire({
                icon: 'info',
                title: 'El ambiente {{ $ambiente->nombre_completo_ambiente }} no tiene registros de equipos, materiales ni mobiliarios.',
                showConfirmButton: true,
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
                                    <a href="{{ route('clinica.equipo.detalles', ['token' => Crypt::encrypt($equipo->cod_equipo)]) }}" title="Detalles">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @foreach($materiales as $material)
                            <tr>
                                <td>{{ $contador++ }}</td>
                                <td>{{ $material->cod_mate }}</td>
                                <td>{{ $material->tipo_mate }}</td>
                                <td>{{ $material->estado_mate }}</td>
                                <td>{{ $material->observacion_mate }}</td>
                                <td>null</td>
                                <td>{{ \Carbon\Carbon::parse($material->fch_registrada)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('clinica.material.detalles', ['token' => Crypt::encrypt($material->cod_mate)]) }}">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @foreach($mobiliarios as $mobiliario)
                            <tr>
                                <td>{{ $contador++ }}</td>
                                <td>{{ $mobiliario->cod_mueble }}</td>
                                <td>{{ $mobiliario->tipo_mueble }}</td>
                                <td>{{ $mobiliario->estado_mueb }}</td>
                                <td>{{ $mobiliario->observacion }}</td>
                                <td>{{ $mobiliario->vida_util }}</td>
                                <td>{{ \Carbon\Carbon::parse($mobiliario->fch_registro)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('clinica.mobiliario.detalles', ['token' => Crypt::encrypt($mobiliario->cod_mueble)]) }}">
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