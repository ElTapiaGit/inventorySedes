@extends('layouts.admin')

@section('title', 'Prestamos')

@section('content')
<main class="content px-3 pagPrestAdmin">
    <div class="container-fluid">
        <div class="mb-3">
            <h2 class="text-center mt-4 fw-bold">PRESTAMOS REALIZADOS <br> {{ $nombreSedeSeleccionada }} - {{ $nombreEdificioSeleccionada }}</h2>
        </div>

        <!-- Menús desplegables para Sede y Edificio -->
        <div class="row mb-3">
            <div class="col-md-6">
                @yield('sede-dropdown')
            </div>
            <div class="col-md-6">
                @yield('edificio-dropdown')
            </div>
        </div>
    
        <!-- Formulario de búsqueda por nombre de usuario -->
        <div class="row mb-3 d-flex justify-content-center">
            <div class="col-md-5 justify-content-center">
                <form method="GET" action="{{ route('prestamosAdmin.buscar') }}" class="d-flex justify-content-center">
                    @csrf
                    <div class="input-group ">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control me-2" id="usuario" name="usuario" list="usuarios" placeholder="Nombre Prestatario">

                    </div>
                    <button class="btn btn-primary  ms-2" type="submit">Buscar</button>
                </form>
            </div>

            <!-- Formulario de búsqueda por fecha -->
            <div class="col-md-5">
                <form method="GET" action="{{ route('prestamosAdmin.buscar') }}" class="d-flex justify-content-center">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control me-2" id="fecha" name="fecha" placeholder="Fecha de prestamo (yy-mm-dd)">
                        
                    </div>
                    <button class="btn btn-primary  ms-2" type="submit">Buscar</button>
                </form>
            </div>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
                <thead class="tablaPrestamo">
                    <th>Prestatarios</th>
                    <th>Descripcion</th>
                    <th>Prestada</th>
                    <th>Encargado</th>
                    <th>Devolucion</th>
                    <th>Detalles</th>
                </thead>
                <tbody>
                    @foreach($prestamos as $prestamo)
                        <tr>
                            <td>{{ $prestamo->nombre_solicitante }}</td>
                            <td>{{ $prestamo->descripcion_prestamo }}</td>
                            <td>{{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}</td>
                            <td>{{ $prestamo->personal->nombre_completo ?? 'No asignado' }}</td>
                            <td>{{ optional($prestamo->devolucion)->fch_devolucion ?? 'No devuelto' }}</td>
                            <td class="text-center">
                                <a href="{{ route('prestamosAdmin.detalles', ['id_prestamo' => Crypt::encrypt($prestamo->id_prestamo)]) }}" title="Mas Informacion">
                                    <i class="bi bi-plus-square-fill"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $prestamos->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!--para cambiar fondo de celda si hay devolucion-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Selecciona todas las celdas en la columna "Fecha Devolución" (quinta columna)
            const celdasFechaDevolucion = document.querySelectorAll('table tbody tr td:nth-child(5)');
    
            celdasFechaDevolucion.forEach(celda => {
                // Si el contenido de la celda es "Null" o vacío, cambia el color de fondo a rojo
                if (celda.textContent.trim() === 'No devuelto' || celda.textContent.trim() === '') {
                    celda.style.backgroundColor = '#f45963';
                    celda.style.color = 'white'; // Para asegurar que el texto sea visible sobre el fondo rojo
                }
            });
        });
    </script>
</main>
<!-- Mensajes de error para los edificios -->
@if(session('ifo'))
    <div class="alert alert-danger">
        {{ session('info') }}
    </div>
@endif


@if(session('errorbuscar'))
    <script>
        Swal.fire({
            icon: 'info',
            title: 'Sin Resultados',
            text: '{{ session('errorbuscar') }}',
        });
    </script>
@endif

<!-- Mensajes de error de conexion en BD -->
@if(session('errordata'))
    <script>
        Swal.fire({
            icon: 'info',
            text: '{{ session('errordata') }}',
        });
    </script>
@endif

<!-- Menú desplegable de Sedes -->
@section('sede-dropdown')
    <li class="nav-item dropdown px-1">
        <a class="nav-link dropdown-toggle white-text" href="#" id="sedeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Sedes
        </a>
        <ul class="dropdown-menu" aria-labelledby="sedeDropdown">
            <li class="dropdown-item">
                <form method="GET" action="{{ route('prestamosAdmin.index') }}">
                    <div class="mb-3">
                        <label for="sede" class="form-label">Sede</label>
                        <select name="sede" id="sede" class="form-control" onchange="this.form.submit()">
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id_sede }}" {{ $sedeSeleccionada == $sede->id_sede ? 'selected' : '' }}>
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

<!-- Menú desplegable de Edificiossdfewgwertgwert sadfvasdf-->
@section('edificio-dropdown')
    <li class="nav-item dropdown px-4">
        <a class="nav-link dropdown-toggle white-text" href="#" id="edificioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Edificios
        </a>
        <ul class="dropdown-menu" aria-labelledby="edificioDropdown">
            <li class="dropdown-item">
                <form method="GET" action="{{ route('prestamosAdmin.index') }}">
                    <div class="mb-3">
                        <label for="edificio" class="form-label">Edificio</label>
                        <select name="edificio" id="edificio" class="form-control" onchange="this.form.submit()">
                            @foreach($edificios as $edificio)
                                <option value="{{ $edificio->id_edificio }}" {{ $edificioSeleccionado == $edificio->id_edificio ? 'selected' : '' }}>
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

@endsection