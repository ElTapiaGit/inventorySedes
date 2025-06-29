@extends('layouts.admin')

@section('title', 'Detalles Equipo')

@section('content')
<main class="content px-3 detallesEquipo">
    <div class="container-fluid">
        <h1 class="text-center fw-bold">DETALLES DE EQUIPO</h1>
        <!-- Información del equipo -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Información del equipo</h5>
                    </div>
                    <div class="card-body d-flex mx-3">
                        <!-- Información del equipo -->
                        <div class="flex-grow-1 ml-8">
                            <p><strong>Código:</strong> {{ $equipo->cod_equipo }}</p>
                            <p><strong>Nombre:</strong> {{ $equipo->nombre_equi }}</p>
                            <p><strong>Marca:</strong> {{ $equipo->marca }}</p>
                            <p><strong>Modelo:</strong> {{ $equipo->modelo }}</p>
                            <p><strong>Descripción:</strong> <br>{{ $equipo->descripcion_equi }}</p>
                            <p><strong>Empotrado:</strong> {{ $equipo->empotrado ? 'Sí' : 'No' }}</p>
                            <p><strong>Estado:</strong> {{ $equipo->estado_equi }}</p>
                            <p><strong>Observaciones:</strong> {{ $equipo->observaciones_equi }}</p>
                            <p><strong>Vida Util:</strong> {{ $equipo->vida_util }}</p>
                        </div>

                        <!-- Imagen del equipo -->
                        <div class="mueble-foto mt-3 text-center">
                            @if($equipo->foto)
                                <img src="{{ asset($equipo->foto->ruta_foto) }}" alt="Foto del equipo" class="img-fluid img-thumbnail">
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
                    <div class="card-header">
                        <h5>Componentes del Equipo</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
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
        <div class="row justify-content-center mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Accesorios del Equipo</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="headerAcces">
                                <tr>
                                    <th>Codigo</th>
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
                                        @if($accesorio->foto)
                                            <form action="{{ route('foto-accesorio') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="cod_accesorio" value="{{ $accesorio->cod_accesorio }}">
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
            </div>
        </div>
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
