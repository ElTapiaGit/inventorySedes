@extends('layouts.admin')

@section('title', 'Contenido Ambiente')

@section('content')
<main class="content px-3 pagContAdmin">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center titulo fw-bold">Contenido del <br> {{ $ambiente->tipoAmbiente->nombre_amb }} - {{ $ambiente->nombre }}</h3>
        </div>

        <div class="btnambiente d-flex justify-content-center mb-3">
            <!--BTN Equipos-->
            <a href="{{ route('contenidoequipos.index', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">EQUIPOS</a>

            <!--BTN Material-->
            <a href="{{ route('materialesAdmin.show', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">MATERIALES</a>

            <!-- BTN Mobiliarios -->
            <a href="{{ route('contenidomobiliario.index', ['token' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-primary mx-2">MOBILIARIO</a>

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
                <table class="table">
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
                                    <a href="{{ route('equiposAdmin.detalles', ['token' => Crypt::encryptString($equipo->cod_equipo)]) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-info-circle"></i>
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
                                    <a href="{{ route('maeterialAdmin.detalles', ['token' => Crypt::encryptString($material->cod_mate)]) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @foreach($mobiliarios as $mobiliario)
                            <tr>
                                <td>{{ $contador++ }}</td>
                                <td>{{ $mobiliario->cod_mueble }}</td>
                                <td>{{ $mobiliario->tipoMobiliario->tipo_mueble }}</td>
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
