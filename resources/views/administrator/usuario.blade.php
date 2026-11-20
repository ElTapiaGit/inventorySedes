@extends('layouts.admin')

@section('title', 'Usuarios Registrados')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/administrador/usuario.css') }}">
@endpush

@section('content')
<main class="content px-3 py-4 pagUsuario">  
    <div class="container-fluid">
        <!-- Cabecera de la seccion -->
        <div class="mb-4">
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-people me-2 text-primary"></i>Usuarios Registrados
            </h2>
            <p class="text-muted small mb-0">Listado general de usuarios habilitados para solicitud y uso de ambientes.</p>
        </div>
        
        <!-- Formularios de busqueda responsivos -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
            <div class="row g-3">
                <div class="col-12 col-md-6 d-flex justify-content-center">
                    <form action="{{ route('usuario.index') }}" method="GET" class="w-100" style="max-width: 450px;">
                        <label for="buscar_tipo_usuario" class="form-label small fw-semibold text-muted mb-1">Buscar por tipo de usuario:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-person-badge"></i></span>
                            <input type="text" id="buscar_tipo_usuario" class="form-control" name="tipo_usuario" placeholder="Ej: Docente, Estudiante" value="{{ request('tipo_usuario') }}">
                            <button class="btn btn-primary d-inline-flex align-items-center gap-1" type="submit">
                                <i class="bi bi-search"></i>
                                <span>Buscar</span>
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="col-12 col-md-6 d-flex justify-content-center">
                    <form action="{{ route('usuario.index') }}" method="GET" class="w-100" style="max-width: 450px;">
                        <label for="buscar_nombre_usuario" class="form-label small fw-semibold text-muted mb-1">Buscar por nombre o apellidos:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscar_nombre_usuario" class="form-control" name="nombre_usuario" placeholder="Nombre o apellidos" value="{{ request('nombre_usuario') }}">
                            <button class="btn btn-primary d-inline-flex align-items-center gap-1" type="submit">
                                <i class="bi bi-search"></i>
                                <span>Buscar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de usuarios registrados -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                    <i class="bi bi-list-ul me-2 text-primary"></i>Listado de Usuarios
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-ulat align-middle mb-0">
                        <thead class="table-ulat-header">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Tipo de Usuario</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Numero de Celular</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @forelse ($usuarios as $usuario)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $contador++ }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ optional($usuario->tipoUsuario)->tipo ?? 'No definido' }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $usuario->nombre }}</td>
                                    <td>{{ $usuario->apellidos }}</td>
                                    <td>{{ $usuario->celular ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>No hay usuarios registrados que coincidan con la busqueda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    @if(session('warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: '{{ session('warning') }}'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}'
            });
        </script>
    @endif
@endpush
