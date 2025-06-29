@extends('layouts.admin')

@section('title', 'Mantenimientos')

@section('content')
<main class="content px-3 pagMantAdmin">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center mt-4 fw-bold">MANTENIMIENTOS DE LA SEDE CENTRAL</h3>
        </div>
        <div class="d-flex justify-content-center mb-4">
            <a href="{{route('historialAdmin.index')}}" class="btn btn-primary mx-2">Historial de mantenimiento</a>
        </div>
        <div>
            <h4>Equipos Para Mantenimiento:</h4>
        </div>
        <!--mensaje para cuando no hay equipos para mantenimiento-->
        @if($equipos->isEmpty() && $mobiliarios->isEmpty() && $materiales->isEmpty())
            <script>
                Swal.fire({
                    icon: 'info',
                    title: 'Sin equipos para mantenimiento',
                    text: 'No hay equipos registrados para mantenimiento.',
                    confirmButtonText: 'Entendido'
                });
            </script>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nros.</th>
                            <th>Codigo</th>
                            <th>Nombre</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                            <th>Ambiente</th>
                            <th>Edificio</th>
                            <th>Sede</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $contador = 1;
                        @endphp
                        @foreach($equipos as $equipo)
                            <tr>
                                <td>{{$contador++}}</td>
                                <td>{{ $equipo->cod_equipo }}</td>
                                <td>{{ $equipo->nombre_equi }}</td>
                                <td style="text-align: justify;">{{ $equipo->observaciones_equi }}</td>
                                <td>{{ $equipo->estado_equi }}</td>
                                <td>{{ $equipo->nombre }}</td>
                                <td>{{ $equipo->nombre_edi }}</td>
                                <td>{{ $equipo->nombre_sede }}</td>
                                <td>
                                    <a href="{{ route('equiposAdmin.detalles', ['token' => Crypt::encryptString($equipo->cod_equipo)]) }}" class="btn btn-sm btn-info" title="Detalles">
                                        <i class="bi bi-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @foreach($mobiliarios as $mobiliario)
                            <tr>
                                <td>{{$contador++}}</td>
                                <td>{{ $mobiliario->cod_mueble }}</td>
                                <td>{{ $mobiliario->tipo_mueble }}</td>
                                <td style="text-align: justify;">{{ $mobiliario->observacion }}</td>
                                <td>{{ $mobiliario->estado_mueb }}</td>
                                <td>{{ $mobiliario->nombre }}</td>
                                <td>{{ $mobiliario->nombre_edi }}</td>
                                <td>{{ $mobiliario->nombre_sede }}</td>
                                <td>
                                    <a href="{{ route('mobiliarioAdmin.detalles', ['cod_mueble' => Crypt::encryptString($mobiliario->cod_mueble)]) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @foreach($materiales as $material)
                            <tr>
                                <td>{{$contador++}}</td>
                                <td>{{ $material->cod_mate }}</td>
                                <td>{{ $material->tipo_mate }}</td>
                                <td style="text-align: justify;">{{ $material->observacion_mate }}</td>
                                <td>{{ $material->estado_mate }}</td>
                                <td>{{ $material->nombre }}</td>
                                <td>{{ $material->nombre_edi }}</td>
                                <td>{{ $material->nombre_sede }}</td>
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
            <!--para las paginaciones-->
            <div class="d-flex justify-content-center">
                {{ $equipos->links() }}
                {{ $mobiliarios->links() }}
                {{ $materiales->links() }}
            </div>
        @endif
    </div>
</main>
<!--mensaje de error todo-->    
@if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
        });
    </script>
@endif
@endsection

