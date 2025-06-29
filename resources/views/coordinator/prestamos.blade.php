@extends('layouts.coodinacion')

@section('title', 'Prestamos')

@section('content')
<main class="content px-3 pagPrestamo">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">PRESTAMOS REALIZADOS DE LA SEDE CENTRAL</h3>
        </div>
    
        <div class="row mb-3 g-2">
            <!-- Formulario de búsqueda por nombre de usuario -->
            <div class="col-md-6 d-flex justify-content-center">
                <form method="GET" action="{{ route('prestamos.buscar') }}" class="d-flex align-items-center">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Nombre Prestatario">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>

            <!-- Formulario de búsqueda por fecha -->
            <div class="col-md-6 d-flex justify-content-center">
                <form method="GET" action="{{ route('prestamos.buscar') }}" class="d-flex align-items-center">
                    @csrf
                    <input type="date" class="form-control me-2 w-50" id="fecha_inicio" name="fecha_inicio" placeholder="Fecha de inicio">
                    <input type="date" class="form-control me-2 w-50" id="fecha_fin" name="fecha_fin" placeholder="Fecha de fin">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </form>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="tablaPrestamo">
                    <th>Prestatarios</th>
                    <th>Descripcion</th>
                    <th>Fecha Prestada</th>
                    <th>Encargado</th>
                    <th>Fecha Devolucion</th>
                    <th>Detalles</th>
                </thead>
                <tbody>
                    @foreach($prestamos as $prestamo)
                        <tr>
                            <td>{{ $prestamo->nombre_solicitante }}</td>
                            <td>{{ $prestamo->descripcion_prestamo }}</td>
                            <td>{{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}</td>
                            <td>{{ $prestamo->encargado }}</td>
                            <td>{{ \Carbon\Carbon::parse($prestamo->fch_devolucion)->format('d/m/Y') ?? 'No devuelto' }}</td>
                            <td class="text-center">
                                <a href="{{ route('prestamos.detalles', ['token' => Crypt::encrypt($prestamo->id_prestamo)])  }}" title="Detalles">
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

<!--mensaje cuando no hay resultados en la busqueda-->
@if(session('error_usuario'))
    <script>
        Swal.fire({
            icon: 'info',
            title: 'Oops...',
            text: '{{ session('error_usuario') }}'
        }).then(() => {
            window.location.href = "{{ route('prestamos.index') }}";
        });
    </script>
@endif

@if(session('error_fecha'))
    <script>
        Swal.fire({
            icon: 'info',
            title: 'Oops...',
            text: '{{ session('error_fecha') }}'
        }).then(() => {
            window.location.href = "{{ route('prestamos.index') }}";
        });
    </script>
@endif

<!--mensaje de error de comunicacion a la BD-->
@if(session('errordata'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('errordata') }}',
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