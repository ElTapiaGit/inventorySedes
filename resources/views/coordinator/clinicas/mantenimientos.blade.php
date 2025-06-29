@extends('layouts.pagClinica')

@section('title', 'Mantenimientos-Clinica')

@section('content')
<main class="content px-3 pagMantenimiento">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">MANTENIMIENTOS DE LA CLINICA ODONTOLOGICA</h3>
        </div>
        <div class="d-flex justify-content-center mb-4">
            <a href="{{ route('clinica.historialmantenimientos.index')}}" class="btn btn-primary mx-2">Historial de mantenimiento</a>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4>Equipos Para Mantenimiento:</h4>
            <a href="{{ route('print.equipos')}}" target="_blank" class="btn btn-success">Imprimir</a>
        </div>

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
                <table class="table table-bordered table-striped">
                    <thead class="tablaMantenimiento">
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
                                <td>{{ $equipo->cod_equipo }}</td>
                                <td>{{ $equipo->nombre_equi }}</td>
                                <td>{{ $equipo->observaciones_equi }}</td>
                                <td>{{ $equipo->estado_equi }}</td>
                                <td>{{ $equipo->nombre_ambiente }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!--para paginacion-->
            <div class="d-flex justify-content-center">
                {{ $equipos->links('pagination::bootstrap-5') }}
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