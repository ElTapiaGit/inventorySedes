@extends('layouts.coodinacion')

@section('title', 'Mantenimientos')

@section('content')
<main class="content px-3 pagMantenimiento">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">MANTENIMIENTOS DE LA SEDE CENTRAL</h3>
        </div>
        <div class="d-flex justify-content-center mb-4">
            <a href="{{ route('historialmantenimientos.index')}}" class="btn btn-primary mx-2">Historial de mantenimiento</a>
        </div>
        <div>
            <h4>Equipos Para Mantenimiento:</h4>
        </div>
        <!--mensaje para cuando no hay equipos para mantenimiento-->
        @if($equipos->isEmpty())
            <script>
                Swal.fire({
                    icon: 'info',
                    title: 'Sin equipos para mantenimiento',
                    text: 'No hay equipos registrados para mantenimiento en el Edificio central.',
                    confirmButtonText: 'Entendido'
                });
            </script>
        @else
            <div class="table-responsive">
                <table class="table table-mantenimiento table-borded table-striped">
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Nombre</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                            <th>Ambiente</th>
                        </tr>
                    </thead>
                    <tbody class="tbodyMante">
                        @foreach($equipos as $equipo)
                            <tr>
                                <td>{{ $equipo->Cod_equipo }}</td>
                                <td>{{ $equipo->nombre_equi }}</td>
                                <td>{{ $equipo->observaciones_equi }}</td>
                                <td>{{ $equipo->estado_equi }}</td>
                                <td>{{ $equipo->nombre_ambiente }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $equipos->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</main>
<!-- mensaje de error de comunicacion con la BD-->
@if(session('errordata'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
        });
    </script>
@endif
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