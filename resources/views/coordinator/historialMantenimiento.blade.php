@extends('layouts.coodinacion')

@section('title', 'Historial de Mantenimientos')

@section('content')
<main class="content px-3 pagHistorial">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">HISTORIAL DE MANTENIMIENTOS DEL EDIFICIO CENTRAL</h3>
        </div>


            <form action="{{ route('historialmantenimientos.index') }}" method="GET" class="d-flex justify-content-center" >
                <div class="row justify-content-center">
                    <div class="col-md-3 mb-2">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="cod_equipo" id="cod_equipo" class="form-control" placeholder="Codigo Artículo" value="{{ request('cod_articulo') }}">
                        </div>
                    </div>

                    <div class="col-md-3 mb-2">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="date" name="fch_inicio" id="fch_inicio" class="form-control" placeholder="Código de Artículo" value="{{ request('fch_inicio') }}">
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="date" name="fch_fin" id="fch_fin" class="form-control" value="{{ request('fch_fin') }}">
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </div>
            </form>

        <!--mensaje de inf sin resultado de buscar-->
        @if(session('errorbuscar'))
            <script>
                Swal.fire({
                    icon: 'info',
                    title: 'Sin Resultados',
                    text: '{{ session('errorbuscar') }}',
                });
            </script>
        @endif

        <div>
            <h4>Historial de Mantenimientos:</h4>
        </div>

        <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="tablaHistorial">
                        <tr>
                            <th>Codigo Articulo</th>
                            <th>Nombre Artículo</th>
                            <th>Tecnico Responsable</th>
                            <th>Fecha Inicio</th>
                            <th>Ambiente</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mantenimientos as $mantenimiento)
                            <tr>
                                <td>{{ $mantenimiento->cod_articulo }}</td>
                                <td>{{ $mantenimiento->nombre_articulo }}</td>
                                <td>{{ $mantenimiento->nombre_tecnico }}</td>
                                <td>{{ \Carbon\Carbon::parse($mantenimiento->fch_inicio)->format('d/m/Y') }}</td>
                                <td>{{ $mantenimiento->nombre_ambiente }}</td>
                                <td>
                                    <a href="{{ route('detallesmantenimientos.mostrar', ['id' => encrypt($mantenimiento->id_mantenimiento_ini)]) }}" title="Detalles">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $mantenimientos->links('pagination::bootstrap-5') }}
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