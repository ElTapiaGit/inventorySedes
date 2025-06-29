@extends('layouts.admin')

@section('title', 'Lista de usuarios registrados')

@section('content')
    <main class="content p-3 pagUsuario">  
        <div class="container-fluid">
            <h2 class="text-center fw-bold">Lista de usuarios registrados</h2>
            
            <!-- Formulario de búsqueda por tipo de usuario -->
            <div class="row mt-3">
                <div class="col-md-6 justify-content-center">
                    <form action="{{ route('usuario.index') }}" method="GET" class="d-flex justify-content-center">
                        <div class="input-group mb-2" style="width: 400px">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" name="tipo_usuario" placeholder="Buscar tipo de usuario">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-6">
                    <form action="{{ route('usuario.index') }}" method="GET" class="d-flex justify-content-center">
                        <div class="input-group" style="width: 400px">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" name="nombre_usuario" placeholder="Nombre o apellidos de usuario">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Tabla de usuarios registrados -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nros.</th>
                                <th>Tipo de Usuario</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Número de Celular</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @foreach ($usuarios as $usuario)
                                <tr>
                                    <td>{{$contador++}}</td>
                                    <td>{{ optional($usuario->tipoUsuario)->tipo }}</td>
                                    <td>{{ $usuario->nombre }}</td>
                                    <td>{{ $usuario->apellidos }}</td>
                                    <td>{{ $usuario->celular }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <!--mensaje para cuando no encuentra la busqueda-->
    @if(session('warning'))
        <script>
                Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: '{{ session('warning') }}'
            }).then(() => {
                location.reload();
            });
        </script>
    @endif

    <!--mensaje para cuando no se pueda mostrar la consulta en la tabla-->
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}'
            });
        </script>
    @endif
@endsection
