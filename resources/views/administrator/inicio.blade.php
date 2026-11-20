@extends('layouts.admin')

@section('title', 'Inicio - Panel Administrador')

@section('content')
<main class="contentini p-3 d-flex flex-column justify-content-between">  
    <div class="container-fluid">
        <!-- Panel de bienvenida institucional -->
    </div>
</main>
@endsection

@push('scripts')
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
