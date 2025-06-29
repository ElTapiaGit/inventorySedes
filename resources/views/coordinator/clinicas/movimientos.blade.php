@extends('layouts.pagClinica')

@section('title', 'Movimientos')

@section('content')
<main class="content px-3 pagMantenimiento">
    <div class="container-fluid">
        <!-- Título de la página -->
        <div class="mb-4">
            <h3 class="text-center fw-bold">MOVIMIENTOS EN LOS LABORATORIOS</h3>
        </div>
        <!-- Formulario de búsqueda por código -->
        
        <form method="POST" action="{{ route('clinica.movimientos.buscar') }}">
            @csrf
            <div class="row justify-content-center">
                <div class="col-md-4 mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control" id="usuario" name="usuario" list="usuarios" placeholder="Nombre de Docente">
                        <datalist id="usuarios">
                            @foreach($nombresUsuarios as $usuario)
                                <option value="{{ $usuario->nombre }}">{{ $usuario->nombre }}</option>
                            @endforeach
                        </datalist>
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>

                    @if(session('error_usuario'))
                        <script>
                            Swal.fire({
                                icon: 'info',
                                title: 'Oops...',
                                text: '{{ session('error_usuario') }}'
                            });
                        </script>
                    @endif
                </div>

                <div class="col-md-4 mb-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="fecha" name="fecha" placeholder="Fecha (dd/mm/yyyy)">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                    @if(session('error_fecha'))
                        <script>
                            Swal.fire({
                                icon: 'info',
                                title: 'Oops...',
                                text: '{{ session('error_fecha') }}'
                            });
                        </script>
                    @endif

                </div>
            </div>
        </form>
        
        <div class="mb-3">
            <h4> Lista de Uso de Ambinetes</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="tablaMantenimiento">
                    <tr>
                        <th>Laboratorio</th>
                        <th>Usuario</th>
                        <th>Actividad</th>
                        <th>Semestre</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movimientos as $movimiento)
                    <tr>
                        <td @if($movimiento->uso_finalizado == 0) style="background-color: #75f459;" @endif>
                            {{ $movimiento->nombre_ambiente }}
                        </td>
                        <td>{{ $movimiento->nombre_usuario }}</td>
                        <td>{{ $movimiento->descripcion }}</td>
                        <td>{{ $movimiento->semestre }}</td>
                        <td>{{ \Carbon\Carbon::parse($movimiento->fch_uso)->format('d/m/Y') }}</td>
                        <td>{{ $movimiento->hora_uso }}</td>
                        <td>
                            <a href="{{ route('clinica.movimientos.detalles', encrypt($movimiento->id_uso_ambiente)) }}" title="Detalles">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection

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
