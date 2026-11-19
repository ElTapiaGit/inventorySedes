
@extends('layouts.pagClinica')

@section('title', 'Inicio Clinica')

@section('content')
    <main class="contentini p-3">  
        <div class="container-fluid">
                <div class="mb-3">`
                    <!--<h3>HOLA BIENVENIDO AL CONTENIDO DE LA PAGINA</h3>-->
                </div>
        </div>
    </main>

    <!--mensaje de error en la connexion de la BD-->
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

<!-- Para mostrar el desplegable de edificios del coordinador -->
@section('dropdown-items')
    @forelse($edificios as $edificio)
        <li>
            <a class="dropdown-item {{ (isset($edificioActivoId) && $edificioActivoId == $edificio->id_edificio) ? 'active' : '' }}" 
               href="{{ route('coordinator.edificio.seleccionar', ['id' => $edificio->id_edificio]) }}">
                <i class="bi bi-building pe-1"></i> {{ $edificio->nombre_edi }}
            </a>
        </li>
    @empty
        <li><span class="dropdown-item text-muted">Sin edificios asignados</span></li>
    @endforelse
@endsection