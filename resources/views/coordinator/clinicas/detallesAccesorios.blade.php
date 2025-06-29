@extends('layouts.pagClinica')

@section('title', 'Detalles del Accesorio')

@section('content')
<main class="content px-3">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                <h3>Detalles del Accesorio</h3>
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Código:</th>
                        <td>{{ $accesorio->cod_accesorio }}</td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ $accesorio->nombre_acce }}</td>
                    </tr>
                    <tr>
                        <th>Descripción:</th>
                        <td>{{ $accesorio->descripcion_acce }}</td>
                    </tr>
                    <tr>
                        <th>Observaciones:</th>
                        <td>{{ $accesorio->observacion_ace }}</td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>{{ $accesorio->estado_acce }}</td>
                    </tr>
                    <tr>
                        <th>Vida Útil:</th>
                        <td>{{ $accesorio->vida_util }}</td>
                    </tr>
                    <tr>
                        <th>Ubicación:</th>
                        <td>{{ $accesorio->ubicacion }}</td>
                    </tr>
                    <tr>
                        <th>Fecha de Registro:</th>
                        <td>{{ \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') }}</td>
                    </tr>
                </table>
                </div>
            </div>
            <div class="col-md-6">
                <h3>Foto del Accesorio</h3>
                @if ($accesorio->foto)
                    <img src="{{ asset($accesorio->foto->ruta_foto) }}" class="img-thumbnail" alt="Foto del accesorio">
                @else
                    <p>No hay foto disponible</p>
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <a href="{{ route('clinica.accesorios.index') }}" class="btn btn-secondary">Volver</a>
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