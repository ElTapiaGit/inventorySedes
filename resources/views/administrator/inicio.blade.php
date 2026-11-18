
@extends('layouts.admin')

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
    <!-- Mensaje de error de conexion de la BD -->    
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

