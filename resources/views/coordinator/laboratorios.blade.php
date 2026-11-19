@extends('layouts.coordinator')

@section('title', 'Laboratorios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/laboratorios.css') }}">
@endpush

@section('content')
<main class="content px-3">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">LABORATORIOS {{ isset($carreraCoordinada) ? 'DE ' . strtoupper($carreraCoordinada) : (isset($edificioActivo) ? '- ' . strtoupper($edificioActivo->nombre_edi) : '') }}</h3>
        </div>
        <form class="form-container" action="{{ route('laboratorios.buscar') }}" method="POST">
            @csrf
            <label for="Laboratorio" class="visually-hidden">Buscar laboratorio</label>
            <input list="laboratorios" name="Laboratorio" id="Laboratorio" class="form-control" required placeholder="Escriba su búsqueda" style="width: 300px;" aria-label="Buscar laboratorio">
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
