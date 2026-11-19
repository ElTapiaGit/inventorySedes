@extends('layouts.coordinator')

@section('title', 'Inicio')

@section('content')
    <main class="contentini p-3">  
        <div class="container-fluid">
            <div class="mb-3">
                <!-- Contenido principal -->
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <!-- Mensaje de error general -->    
    @if(session('errordata'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errordata') }}',
            });
        </script>
    @endif
@endpush

<!-- Para mostrar el desplegable de edificios del coordinador -->
@section('dropdown-items')
    @forelse($edificios as $edificio)
        @if($edificio->nombre_edi == 'Edificio Central')
            <li><a class="dropdown-item" href="{{ route('coordinator.inicio') }}"><i class="bi bi-building pe-1"></i> {{ $edificio->nombre_edi }}</a></li>
        @elseif($edificio->nombre_edi == 'Clinica Odontologia')
            <li><a class="dropdown-item" href="{{ route('coordinator.clinica.inicio') }}"><i class="bi bi-hospital pe-1"></i> {{ $edificio->nombre_edi }}</a></li>
        @else
            <li><a class="dropdown-item text-secondary" href="{{ route('coordinator.inicio') }}?edificio={{ $edificio->id_edificio }}" title="Disponible en Bloque 3"><i class="bi bi-building-check pe-1"></i> {{ $edificio->nombre_edi }}</a></li>
        @endif
    @empty
        <li><span class="dropdown-item text-muted">Sin edificios asignados</span></li>
    @endforelse
@endsection
