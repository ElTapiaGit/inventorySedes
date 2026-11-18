@extends('layouts.encargado')

@section('title', 'Página Principal')

@section('content')
<div class="container text-center mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="p-5 bg-light rounded shadow-sm">
                <h1 class="display-4">Bienvenido al Sistema de Gestión</h1>
                <p class="lead">Esta es la página principal.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Mostrar mensajes de error -->
    @if($errors->any())
        <script>
            var errorMessages = [];
            @foreach($errors->all() as $error)
                errorMessages.push('{{ $error }}');
            @endforeach
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: '<ul>' + errorMessages.map(msg => `<li>${msg}</li>`).join('') + '</ul>',
                confirmButtonText: 'Aceptar'
            });
        </script>
    @endif
@endpush
