@extends('layouts.coordinator')

@section('title', 'Inicio')

@section('content')
    <main class="contentini p-3" @if(!empty($coordFondoUrl)) style="background-image: url('{{ $coordFondoUrl }}'); background-size: cover; background-position: center; background-repeat: no-repeat;" @endif>  
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
