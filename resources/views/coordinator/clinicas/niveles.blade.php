@extends('layouts.pagClinica')
@section('title', 'Niveles-Clinica')
<!--aperturamos la seccion para saber en que parte se debe mostrar-->
@section('content')

<main class="content px-3">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">BUSQUEDAS RAPIDAS</h3>
        </div>

        <!-- Formulario para buscar código de equipo -->
        <form action="{{ route('niveles.buscarEquipo') }}" method="POST" class="mb-3">
            @csrf
            <div class="input-group mx-auto" style="max-width: 400px;">
                <input type="text" name="codigo" class="form-control" placeholder="Código de Equipo" required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>

        <!-- Formulario para buscar código de mobiliario -->
        <form action="{{ route('niveles.buscarMobiliario')}}" method="POST" class="mb-3">
            @csrf
            <div class="input-group mx-auto" style="max-width: 400px;">
                <input type="text" name="codigo" class="form-control" placeholder="Código de Mobiliario">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>

        <!-- Formulario para buscar código de material -->
        <form action="{{ route('niveles.buscarMaterial')}}" method="POST" class="mb-3">
            @csrf
            <div class="input-group mx-auto" style="max-width: 400px;">
                <input type="text" name="codigo" class="form-control" placeholder="Código de Material">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>

        <!-- Formulario para buscar código de accesorio -->
        <form action="{{ route('niveles.buscarAccesorio')}}" method="POST" class="mb-3">
            @csrf
            <div class="input-group mx-auto" style="max-width: 400px;">
                <input type="text" name="codigo" class="form-control" placeholder="Código de Accesorio">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>

        
        <!-- Mostrar mensaje de error si el código no existe -->
        @if(session('errorbuscar'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin resultados de búsqueda',
                text: '{{ session('errorbuscar') }}',
            });
        </script>
        @endif
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