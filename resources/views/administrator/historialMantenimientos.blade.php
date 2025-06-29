@extends('layouts.admin')

@section('title', 'Historial Mantenimientos')

@section('content')
<main class="content px-3 pagHistoAdmin">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center my-3 fw-bold">HISTORIAL DE MANTENIMIENTOS</h3>
        </div>

        <div class="mb-4 justify-content-center">
            <form action="{{ route('historialAdmin.index') }}" method="GET" class="d-flex justify-content-center">
                <div class="input-group px-4">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="cod_equipo" id="cod_equipo" class="form-control" placeholder="Codigo de Artículo" value="{{ request('cod_articulo') }}">
                </div>
                <div class="input-group px-4">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="date" name="fch_inicio" id="fch_inicio" class="form-control" value="{{ request('fch_inicio') }}">
                </div>
                <div class="input-group px-4">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="date" name="fch_fin" id="fch_fin" class="form-control" value="{{ request('fch_fin') }}">
                </div>
                <div class="input-group align-self-end ">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>

        <div>
            <h4>Historial de Mantenimientos:</h4>
        </div>
        <!--mensaje si hay resultado de busqueda -->
        @if($mantenimientos->isEmpty())
            <script>
                Swal.fire({
                    icon: 'info',
                    title: 'Articulo No Registra en mantenimientos',
                    text: 'No hay historial de mantenimientos registrados para el Articulo.',
                    confirmButtonText: 'Entendido'
                });
            </script>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="tablaHistorial">
                        <tr>
                            <th>#</th>
                            <th>Codigo</th>
                            <th>Nombre Artículo</th>
                            <th>Tecnico</th>
                            <th>Fecha Inicio</th>
                            <th>Ambiente</th>
                            <th>Edificio</th>
                            <th>Sede</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mantenimientos as $index => $mantenimiento)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $mantenimiento->cod_articulo }}</td>
                                <td>
                                    @if($mantenimiento->nombre_articulo_equipo)
                                        {{ $mantenimiento->nombre_articulo_equipo }}
                                    @elseif($mantenimiento->nombre_articulo_mobiliario)
                                        {{ $mantenimiento->nombre_articulo_mobiliario }}
                                    @elseif($mantenimiento->nombre_articulo_material)
                                        {{ $mantenimiento->nombre_articulo_material }}
                                    @endif
                                </td>
                                <td>{{ $mantenimiento->nombre_tecnico }} {{ $mantenimiento->ap_paterno }} {{ $mantenimiento->ap_materno }}</td>
                                <td>{{ \Carbon\Carbon::parse($mantenimiento->fch_inicio)->format('d/m/Y') }}</td>
                                <td>{{ $mantenimiento->nombre_ambiente }}</td>
                                <td>{{ $mantenimiento->nombre_edificio }}</td>
                                <td>{{ $mantenimiento->nombre_sede }}</td>
                                <td>
                                    <a href="{{ route('detallesManteAdmin.index', ['id' => encrypt($mantenimiento->id_mantenimiento_ini)]) }}" class="btn btn-sm btn-info" title="Detalles">
                                        <i class="fas bi-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $mantenimientos->links('pagination::bootstrap-5') }}
                </div>
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
