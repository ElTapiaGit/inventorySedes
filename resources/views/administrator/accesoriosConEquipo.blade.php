@extends('layouts.admin')

@section('title', 'Accesorios Con Equipos')

@section('content')
<main class="content p-3 pagAccesEquip">
    <h1 class="text-center">Accesorios con Equipos</h1>

    <div class="mb-3 text-center">
        <a href="{{ route('accesoriosadmin.unicos') }}" class="btn btn-primary">Accesorios Únicos</a>
        <a href="{{ route('accesoriosadmin.conEquipo')}}" class="btn btn-secondary">Accesorios con Equipos</a>
    </div>

    <!-- Formulario de búsqueda -->
    <form action="{{ route('accesoriosadmin.conEquipo') }}" method="GET" class="form-inline mb-4">
        <div class="row justify-content-center">
            <div class="col-md-3 mb-2">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="codigo" id="codigo" placeholder="Buscar por Código" class="form-control" value="{{ request('codigo') }}">
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search me-2"></i></span>
                    <input type="text" name="nombre" id="nombre" placeholder="Buscar por Nombre" class="form-control" value="{{ request('nombre') }}">
                </div>
            </div>
            
            <div class="col-md-2 d-flex align-items-center mb-2">
                <button type="submit" class="btn btn-primary w-100">Buscar</button>
            </div>
        </div>
    </form>

    <!-- Tabla de accesorios -->
    @if($accesorios->count() > 0)
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Observación</th>
                    <th>Estado</th>
                    <th>Vida Útil</th>
                    <th>Ubicación</th>
                    <th>Fecha de Registro</th>
                    <th>Equipo</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accesorios as $accesorio)
                    <tr>
                        <td>{{ $accesorio->cod_accesorio }}</td>
                        <td>{{ $accesorio->nombre_acce }}</td>
                        <td style="text-align: justify;">{{ $accesorio->descripcion_acce }}</td>
                        <td>{{ $accesorio->observacion_ace }}</td>
                        <td>{{ $accesorio->estado_acce }}</td>
                        <td>{{ $accesorio->vida_util }}</td>
                        <td>{{ $accesorio->ubicacion }}</td>
                        <td>{{ \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') }}</td>
                        <td>
                            @foreach($accesorio->equipos as $equipo)
                                <div>{{ $equipo->cod_equipo }}</div>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('accesoriosadmin.detalles', ['codigo' => Crypt::encrypt($accesorio->cod_accesorio)]) }}" class="btn btn-sm btn-info" title="Mas Informacion">
                                <i class="fas bi-info-circle"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="d-flex justify-content-center">
            {{ $accesorios->links('pagination::bootstrap-5') }}
        </div>
    @else
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin resultados',
                text: 'No se encontraron accesorios con equipos que coincidan con los criterios de búsqueda.',
            }).then(function() {
                window.location = '{{ route("accesoriosadmin.conEquipo") }}';
            });
        </script>
    @endif
</main>
@endsection
