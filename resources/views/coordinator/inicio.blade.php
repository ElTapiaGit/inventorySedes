
@extends('layouts.coodinacion')

@section('title', 'Inicio')

@section('content')
    <main class="contentini p-3">  
        <div class="container-fluid">
                <div class="mb-3">`
                    <!--<h3>HOLA BIENVENIDO AL CONTENIDO DE LA PAGINA</h3>-->
                </div>
        </div>
    </main>

    <!--mensaje de error todo-->    
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