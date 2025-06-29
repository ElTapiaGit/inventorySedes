@extends('layouts.admin')

@section('title', 'Accesorios')


@section('content')
<main class="content p-3 pagAccesAdmin">
    <div class="container-fluid">
        <h1 class="titulo text-center">Accesorios</h1>
        <div class="mb-6 text-center">
            <a href="{{ route('accesoriosadmin.unicos') }}" class="btn btn-primary">Accesorios Únicos</a>
            <a href="{{ route('accesoriosadmin.conEquipo') }}" class="btn btn-secondary">Accesorios con Equipos</a>
        </div>


        @if($accesorios->isEmpty())
            <script>
                Swal.fire({
                    icon: 'info',
                    title: 'No hay novedad',
                    text: "No hay Accesorios para reponer o cambiar",
                });
            </script>
        @else
            <h2 class="titulo text-center">Lista de Accesorios para Reponer:</h2>
            <div class="table-responsive">
                <table class="table  table-striped">
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
                            <th>Ultimo Cambio</th>
                            <th>Equipo</th>
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
                                <td>{{ \Carbon\Carbon::parse($accesorio->fecha_ultimo_cambio)->format('d/m/Y') }}</td>
                                <td>{{ $accesorio->equipo_codigo }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $accesorios->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <!--mensaje de error de conexion de la BD-->    
    
</main>
@endsection
