@extends('layouts.admin')

@section('title', 'Ambientes')

    <!--contenido para el menu desplegable de la cabecera-->
    @section('sede-dropdown')
        <li class="nav-item dropdown px-1">
            <a class="nav-link dropdown-toggle white-text" href="#" id="sedeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Sedes
            </a>
            <ul class="dropdown-menu" aria-labelledby="sedeDropdown">
                <li class="dropdown-item">
                    <form method="GET" action="{{ route('ambiente.index') }}">
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
    @section('edificio-dropdown')
        <li class="nav-item dropdown px-4">
            <a class="nav-link dropdown-toggle white-text" href="#" id="edificioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Edificios
            </a>
            <ul class="dropdown-menu" aria-labelledby="edificioDropdown">
                <li class="dropdown-item">
                    <form method="GET" action="{{ route('ambiente.index') }}">
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

@section('content')
    <main class="content p-3 pagAmbienteAdmin">  
        <div class="container-fluid">
            <div class="mb-3">
                <h2 class="text-center fw-bold">Tipos de Ambientes y Ambientes</h2>
                <div class="d-flex mt-3 mb-3 justify-content-center">
                    <a href="{{ route('tipoambiente.index') }}" class="btn btn-primary me-3">Tipos de Ambientes</a>
                    <a href="{{ route('ambientes.index')}}" class="btn btn-primary">Ambientes</a>
                </div>
            </div>

            <h3 class="mb-4 text-center">Lista de Ambientes de la {{ $nombreSedeSeleccionada }}: {{ $nombreEdificioSeleccionada}}</h3>

            <!-- Formulario de Búsqueda -->
            <div class="row mt-3">
                <div class="col-md-6 justify-content-center">
                    <form method="GET" action="{{ route('ambiente.index') }}" class="d-flex justify-content-center">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" id="search" class="form-control w-50" placeholder="Nombre del Ambiente" value="{{ request('search') }}">

                                <button type="submit" class="btn btn-primary">Buscar</button>
                            </div>
                        </div>
                        <input type="hidden" name="sede" value="{{ Crypt::encryptString($sedeSeleccionada)  }}">
                        <input type="hidden" name="edificio" value="{{ Crypt::encryptString($edificioSeleccionado) }}">
                    </form>
                </div>
                <!-- Formulario de Búsqueda por Número de Piso -->
                <div class="col-md-6">
                    <form method="GET" action="{{ route('ambiente.index') }}" class="d-flex justify-content-center">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                <select name="piso" id="piso" class="form-select w-50">
                                    <option value="">Seleccionar Piso</option>
                                    @foreach($pisos as $piso)
                                        <option value="{{ $piso->numero_piso }}" {{ $pisoSeleccionado == $piso->numero_piso ? 'selected' : '' }}>
                                            {{ $piso->numero_piso }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">Buscar</button>
                            </div>
                        </div>
                        <input type="hidden" name="sede" value="{{ Crypt::encryptString($sedeSeleccionada) }}">
                        <input type="hidden" name="edificio" value="{{ Crypt::encryptString($edificioSeleccionado) }}">
                    </form>
                </div>
            </div>

            
            <div class="row">
                <div class="col-md-12">
                    <!--Mensaje inf. cuando no hay ambientes-->
                    @if ($noHayAmbientes)
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Sin Ambientes',
                                    text: 'No hay ambientes registrados para el edificio seleccionado.'
                                });
                            });
                        </script>
                    @endif
                    <!-- Mensaje de no encontrado -->
                    @if ($search && $noAmbientesEncontrados)
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'No Encontrado',
                                    text: 'No se encontraron ambientes con el nombre especificado.'
                                });
                            });
                        </script>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nros.</th>
                                    <th>Piso</th>
                                    <th>Tipo de Ambiente</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach($ambientes as $ambiente)
                                    <tr>
                                        <td>{{ $contador++ }}</td>
                                        <td>{{ $ambiente->numero_piso }}</td>
                                        <td>{{ $ambiente->nombre_amb }}</td>
                                        <td>{{ $ambiente->nombre }}</td>
                                        <td>{{ $ambiente->descripcion_amb }}</td>
                                        <td> 
                                            <a href="{{ route('ambienteAdmin.contenido', ['id_ambiente' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-sm btn-info" title="Detalles">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
<!--mensaje de error de conexion de la BD-->
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
