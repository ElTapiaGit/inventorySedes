@extends('layouts.pagClinica')

@section('title', 'Detalles de Equipo')

@section('content')
<main class="content px-3 detallesEquipo">
    <div class="container-fluid">
        <h1 class="text-center fw-bold">DETALLES DE EQUIPO</h1>

        <!-- Información del equipo -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5>Información del equipo</h5>
                    </div>
                    <div class="card-body d-flex flex-column flex-md-row">
                        <!-- Información del equipo -->
                        <div class="flex-grow-1 mb-3 mb-md-0" style="max-width: 500px">
                            <p><strong>Codigo:</strong> {{ $equipo->cod_equipo }}</p>
                            <p><strong>Nombre:</strong> {{ $equipo->nombre_equi }}</p>
                            <p><strong>Marca:</strong> {{ $equipo->marca }}</p>
                            <p><strong>Modelo:</strong> {{ $equipo->modelo }}</p>
                            <p style="text-align: justify;"><strong>Descripción:</strong> <br> {{ $equipo->descripcion_equi }} </p>
                            <p><strong>Empotrado:</strong> {{ $equipo->empotrado ? 'Sí' : 'No' }}</p>
                            <p><strong>Estado:</strong> {{ $equipo->estado_equi }}</p>
                            <p style="text-align: justify;"><strong>Observaciones:</strong><br> {{ $equipo->observaciones_equi }}</p>
                            <p><strong>Vida Util:</strong> {{ $equipo->vida_util }}</p>
                        </div>

                        <!-- Imagen del equipo -->
                        <div class="ml-md-3 px-2">
                            @if($equipo->ruta_foto)
                                <img src="{{ asset($equipo->ruta_foto) }}" alt="Foto del equipo" class="img-fluid img-thumbnail">
                            @else
                                <p>No hay foto disponible</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Componentes del equipo -->
        <br>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5>Componentes del Equipo</h5>
                    </div>
                     <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="headerComp">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($componentes as $componente)
                                <tr>
                                    <td>{{ $componente->nombre_compo }}</td>
                                    <td>{{ $componente->descripcion_compo }}</td>
                                    <td>{{ $componente->estado_compo }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay componentes para este equipo.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesorios del equipo -->
        <h2 class="mt-3">Accesorios del equipo</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="headerAcces">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Observaciones</th>
                        <th>Estado</th>
                        <th>Vida Útil</th>
                        <th>Ubicación</th>
                        <th>Fecha Registrada</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accesorios as $accesorio)
                    <tr>
                        <td>{{ $accesorio->cod_accesorio }}</td>
                        <td>{{ $accesorio->nombre_acce }}</td>
                        <td>{{ $accesorio->descripcion_acce }}</td>
                        <td>{{ $accesorio->observacion_ace }}</td>
                        <td>{{ $accesorio->estado_acce }}</td>
                        <td>{{ $accesorio->vida_util }}</td>
                        <td>{{ $accesorio->ubicacion }}</td>
                        <td>{{ \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($accesorio->FOTO_id_foto)
                                <form action="{{ route('clinica.equiposAccefoto')}}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="cod_accesorio" value="{{ $accesorio->FOTO_id_foto }}">
                                    <button type="submit" class="btn btn-link">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </form>
                            @else
                                No disponible
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No hay accesorios para este equipo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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