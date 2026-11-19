@extends('layouts.coordinator')

@section('title', 'Movimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/movimientos.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 pagMantenimiento">
    <div class="container-fluid">
        <!-- Título de la página -->
        <div class="mb-4">
            <h3 class="text-center fw-bold">USO DE AMBIENTES {{ isset($edificioActivo) ? '- ' . strtoupper($edificioActivo->nombre_edi) : '' }}</h3>
        </div>
        
        <!-- Formulario de búsqueda -->
        <form method="POST" action="{{ route($isClinica ? 'clinica.movimientos.buscar' : 'movimientos.buscar') }}">
            @csrf
            <div class="row justify-content-center">
                <div class="col-md-4 mb-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <label for="usuario" class="visually-hidden">Nombre de Docente</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" list="usuarios" placeholder="Nombre de Docente" aria-label="Nombre de Docente">
                        <datalist id="usuarios">
                            @foreach($nombresUsuarios as $usuario)
                                <option value="{{ $usuario->nombre }}">{{ $usuario->nombre }}</option>
                            @endforeach
                        </datalist>
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <label for="fecha" class="visually-hidden">Fecha (dd/mm/yyyy)</label>
                        <input type="text" class="form-control" id="fecha" name="fecha" placeholder="Fecha (dd/mm/yyyy)" aria-label="Fecha (dd/mm/yyyy)">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="mb-3">
            <h4>Lista de Uso de Ambientes</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="tablaMantenimiento">
                    <tr>
                        <th>Ambiente</th>
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
                            <a href="{{ route($isClinica ? 'clinica.movimientos.detalles' : 'movimientos.detalles', encrypt($movimiento->id_uso_ambiente)) }}" title="Detalles">
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

@push('scripts')
    <!-- Mensaje cuando no hay usuarios -->
    @if(session('error_usuario'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Oops...',
                text: '{{ session('error_usuario') }}'
            });
        </script>
    @endif

    <!-- Mensaje cuando no hay fecha -->
    @if(session('error_fecha'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Oops...',
                text: '{{ session('error_fecha') }}'
            });
        </script>
    @endif
@endpush
