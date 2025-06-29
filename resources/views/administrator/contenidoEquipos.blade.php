@extends('layouts.admin')

@section('title', 'Equipos del Ambiente')

@section('content')
<main class="content px-3 pagAmbEquip">
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
            <h4>Lista de Equipos:</h4>
        </div>
        
        @if(isset($error_message))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ $error_message }}',
                showConfirmButton: true
            });
        </script>
        @endif

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
            <table class="table table-bordered">
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
                </tbody>
            </table>
        </div>
        @endif
    </div>
</main>


<!-- mensajes de error al obtener los datos -->
@if($errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ $errors->first() }}',
        });
    </script>
@endif
@endsection
