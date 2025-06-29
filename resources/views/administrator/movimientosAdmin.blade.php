@extends('layouts.admin')

@section('title', 'Movimientos')

@section('content')
<main class="content px-3 pagMovAdmin">
    <div class="container-fluid">
         <!-- Menús desplegables para Sede y Edificio -->
         <div class="row mb-3">
            <div class="col-md-6">
                @yield('sede-dropdown')
            </div>
            <div class="col-md-6">
                @yield('edificio-dropdown')
            </div>
        </div>

        <!-- Título de la página -->
        <div class="mb-4">
            <h3 class="text-center mt-4 fw-bold">MOVIMIENTOS EN LOS LABORATORIOS <br> {{ $nombreSedeSeleccionada }} - {{ $nombreEdificioSeleccionada }}</h3>
        </div>

        <!-- Formulario de búsqueda por nombre de usuario -->
        <form method="POST" action="{{ route('movimientosAdmin.buscar') }}" class="d-flex justify-content-center">
            @csrf
            <div class="mb-3 row justify-items-center">
                <div class="col-md-6 d-flex align-items-center mt-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" placeholder="Nombre usuario" >
                        <!--mensaje de no encontrado usuario-->
                        @if(session('error_usuario'))
                        <script>
                            Swal.fire({
                                icon: 'info',
                                title: 'Oops...',
                                text: '{{ session('error_usuario') }}'
                            });
                        </script>
                        @endif

                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </div>

                <div class="col-md-6 d-flex align-items-center">
                    <div class="input-group mt-2">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="fecha_inicio" placeholder="Busacr fecha: dd-mm-aa" class="form-control">
                        <!--mensaje de no encontrado fecha-->
                        @if(session('error_fecha'))
                            <script>
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Oops...',
                                    text: '{{ session('error_fecha') }}'
                                });
                            </script>
                        @endif

                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Lista de Movimientos -->
        <div class="mb-3">
            <h4>Lista de Uso de Ambientes</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="tablaMantenimiento">
                    <tr>
                        <th>Ambientes</th>
                        <th>Usuario</th>
                        <th>Actividad</th>
                        <th>Semestre</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody class="tbody">
                    @foreach($movimientos as $movimiento)
                    <tr class="highlight-row">
                        
                        <td @if($movimiento->fch_fin == 0) style="background-color: #75f459;" @endif>
                            {{ $movimiento->nombre_ambiente }}
                        </td>
                        <td>{{ $movimiento->nombre_usuario }}</td>
                        <td>{{ $movimiento->descripcion }}</td>
                        <td>{{ $movimiento->semestre }}</td>
                        <td>{{ \Carbon\Carbon::parse($movimiento->fch_uso)->format('d/m/Y') }}</td>
                        <td>{{ $movimiento->hora_uso }}</td>
                        <td>
                            <a href="{{ route('movimientosAdmin.detalles', encrypt($movimiento->id_uso_ambiente)) }}" class="btn btn-sm btn-info" title="Detalles">
                                <i class="fas bi-info-circle"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Enlaces de paginación -->
        <div class="d-flex justify-content-center">
            {{ $movimientos->links() }}
        </div>
    </div>
</main>
<!--mensaje de error de todo-->
@if(session('errordata'))
<script>
    Swal.fire({
        icon: 'info',
        title: 'Oops...',
        text: '{{ session('errordata') }}'
    });
</script>
@endif
@endsection

<!-- Menú desplegable de Sedes -->
@section('sede-dropdown')
    <li class="nav-item dropdown px-1">
        <a class="nav-link dropdown-toggle white-text" href="#" id="sedeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Sedes
        </a>
        <ul class="dropdown-menu" aria-labelledby="sedeDropdown">
            <li class="dropdown-item">
                <form method="GET" action="{{ route('movimientosAdmin.index') }}">
                    <div class="mb-3">
                        <label for="sede" class="form-label">Sede</label>
                        <select name="sede" id="sede" class="form-control" onchange="this.form.submit()">
                            @foreach($sedes as $sede)
                                <option value="{{ Crypt::encryptString($sede->id_sede) }}" {{ $sedeSeleccionada == $sede->id_sede ? 'selected' : '' }}>
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </li>
        </ul>
    </li>
@endsection

<!-- Menú desplegable de Edificios -->
@section('edificio-dropdown')
    <li class="nav-item dropdown px-4">
        <a class="nav-link dropdown-toggle white-text" href="#" id="edificioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Edificios
        </a>
        <ul class="dropdown-menu" aria-labelledby="edificioDropdown">
            <li class="dropdown-item">
                <form method="GET" action="{{ route('movimientosAdmin.index') }}">
                    <div class="mb-3">
                        <label for="edificio" class="form-label">Edificio</label>
                        <select name="edificio" id="edificio" class="form-control" onchange="this.form.submit()">
                            @foreach($edificios as $edificio)
                                <option value="{{ Crypt::encryptString($edificio->id_edificio) }}" {{ $edificioSeleccionado == $edificio->id_edificio ? 'selected' : '' }}>
                                    {{ $edificio->nombre_edi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </li>
        </ul>
    </li>
@endsection
