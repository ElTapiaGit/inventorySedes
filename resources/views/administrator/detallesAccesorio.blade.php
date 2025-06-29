@extends('layouts.admin')

@section('title', 'Detalles Accesorios')

@section('content')
<main class="content p-3 pagAccesorio">
    <div class="container-fluid">

        <h2 class="titulo text-center">Detalles del Accesorio</h2>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Información del Accesorio</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <!-- Mostrar foto del accesorio si existe -->
                        @if($accesorio->foto)
                            <img src="{{ asset($accesorio->foto->ruta_foto) }}" class="img-fluid" alt="Foto del accesorio">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="img-fluid" alt="No hay imagen">
                        @endif
                    </div>
                    <div class="col-md-7">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Código:</strong> {{ $accesorio->cod_accesorio }}</li>
                            <li class="list-group-item"><strong>Nombre:</strong> {{ $accesorio->nombre_acce }}</li>
                            <li class="list-group-item"><strong>Descripción:</strong> <br>{{ $accesorio->descripcion_acce }}</li>
                            <li class="list-group-item"><strong>Observación:</strong> {{ $accesorio->observacion_ace }}</li>
                            <li class="list-group-item"><strong>Estado:</strong> {{ $accesorio->estado_acce }}</li>
                            <li class="list-group-item"><strong>Vida Útil:</strong> {{ $accesorio->vida_util }}</li>
                            <li class="list-group-item"><strong>Ubicación:</strong> {{ $accesorio->ubicacion }}</li>
                            <li class="list-group-item"><strong>Fecha de Registro:</strong> {{ \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Equipo -->
        @if($equipo)
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Información del Equipo</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">{{ $equipo->nombre_equi }}</h5>
                            <p class="card-text"><strong>Codigo:</strong> {{ $equipo->cod_equipo }}</p>
                            <p class="card-text"><strong>Marca:</strong> {{ $equipo->marca }}</p>
                            <p class="card-text"><strong>Modelo:</strong> {{ $equipo->modelo }}</p>
                            <p class="card-text"><strong>Descripción:</strong> {{ $equipo->descripcion_equi }}</p>
                            <p class="card-text"><strong>Estado:</strong> {{ $equipo->estado_equi }}</p>
                            <p class="card-text"><strong>Vida Útil:</strong> {{ $equipo->vida_util }}</p>
                            <p class="card-text"><strong>Fecha de Registro:</strong> {{ \Carbon\Carbon::parse($equipo->fch_registro)->format('d/m/Y') }}</p>
                        </div>

                        <div class="col-md-4">
                            <img src="{{ $equipo->foto ? asset($equipo->foto->ruta_foto) : 'https://via.placeholder.com/150' }}" class="img-fluid" alt="{{ $equipo->nombre_equi }}">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <h2 class="titulo text-center mt-4">Historial del Accesorio</h2>
        @if($historial->isEmpty())
            <div class="alert alert-info">No hay registros de historial para este accesorio.</div>
        @else
            @foreach($historial as $registro)
                <div class="card mb-3">
                    <div class="card-header">
                        <p><strong>Fecha: </strong>{{ \Carbon\Carbon::parse($registro->fch_cambio)->format('d/m/Y') }}</p>
                    </div>
                    <div class="card-body">
                        <p><strong>Motivo del Cambio:</strong> {{ $registro->motivo_cambio }}</p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</main>
@endsection
