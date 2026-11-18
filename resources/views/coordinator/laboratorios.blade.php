@extends('layouts.coodinacion')

@section('title', 'Laboratorios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/laboratorios.css') }}">
@endpush

@section('content')
<main class="content px-3">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">LABORATORIOS DE ODONTOLOGIA</h3>
        </div>
        <form class="form-container" action="{{ route('laboratorios.buscar') }}" method="POST">
            @csrf
            <input list="laboratorios" name="Laboratorio" id="Laboratorio" class="form-control" required placeholder="Escriba su búsqueda" style="width: 300px;">
            <datalist id="laboratorios">
                @foreach($laboratorios as $laboratorio)
                    <option value="{{ $laboratorio->nombre }}"></option>
                @endforeach
            </datalist>
            <button type="submit" class="btn btn-success">Buscar</button>
        </form>
    </div>
</main>
@endsection

@push('scripts')
    <!-- Mensaje sin resultados de busqueda -->
    @if(session('errorbuscar'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin resultados de busqueda',
                text: '{{ session('errorbuscar') }}',
            });
        </script>
    @endif
@endpush

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