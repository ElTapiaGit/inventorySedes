@extends('layouts.coodinacion')

@section('title', 'Accesorios')

@section('content')
<main class="content px-3 pagacces">
    <div class="container-fluid">
        <!-- Título de la página -->
        <div class="mb-4">
            <h3 class="text-center fw-bold">LISTA DE ACCESORIOS</h3>
        </div>
        <!-- Botones de navegación -->
        <div class="d-flex justify-content-center mb-4">
            <a href="{{ route('accesorios.equipo') }}" class="btn btn-primary mx-2">Equipo con Accesorios</a>
            <a href="{{ route('accesorios.unicos') }}" class="btn btn-secondary mx-2">Accesorios Únicos</a>
        </div>
        <!-- Formulario de búsqueda por código -->
        <div class="row mb-3">
            <div class="col-md-6 d-flex justify-content-center align-items-center my-3">
                <form method="GET" action="{{ route('accesorios.buscarCodigo') }}" class="personaliacces">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="codigo" placeholder="Buscar por código">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 d-flex justify-content-center align-items-center " >
                <form method="GET" action="{{ route('accesorios.buscarNombre') }}" class="personaliacces">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="nombre" placeholder="Buscar por nombre">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Tabla de accesorios -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="tablaAcces">
                    <tr>
                        <th >Codigo</th>
                        <th>Nombre</th>
                        <th>Observaciones</th>
                        <th>Estado</th>
                        <th>Ubicacion</th>
                        <th>Resgistrada</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accesorios as $accesorio)
                        <tr>
                            <td>{{ $accesorio->cod_accesorio }}</td>
                            <td>{{ $accesorio->nombre_acce }}</td>
                            <td>{{ $accesorio->observacion_ace }}</td>
                            <td>{{ $accesorio->estado_acce }}</td>
                            <td>{{ $accesorio->ubicacion }}</td>
                            <td>{{ \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('accesorios.show', $accesorio->cod_accesorio) }}" title="Mas Informacion">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $accesorios->links('pagination::bootstrap-5') }}
        </div>

        <!--mensaje para cuando no se encuentra resultados de busqueda-->
        @if (session('errorbuscar'))
            <script>
                Swal.fire({
                    icon: 'info',
                    title: 'Busqueda NO Encontrada',
                    text: '{{ session('errorbuscar') }}'
                });
            </script>
        @endif
    </div>
</main>
<!--mensaje de error en la comunicacion a BD-->
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
