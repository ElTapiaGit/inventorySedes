@extends('layouts.coordinator')

@section('title', 'Ambientes')

@section('content')
<main class="content px-3 ambiente-clinica">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center titulo fw-bold">Ambientes {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }}</h3>
        </div>

        <div class="row mt-3">
            <!-- Buscador de ambientes por nombre -->
            <div class="col-md-6 d-flex justify-content-center align-items-center my-3">
                <form method="GET" action="{{ route('coordinator.clinica.ambientes') }}">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <label for="search_ambiente" class="visually-hidden">Buscar por nombre</label>
                        <input type="text" id="search_ambiente" name="search" class="form-control" placeholder="Buscar por nombre" value="{{ request('search') }}" aria-label="Buscar por nombre">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </form>
            </div>

            <!-- Formulario de Búsqueda por Número de Piso -->
            <div class="col-md-6 d-flex justify-content-center align-items-center my-3">
                <form method="GET" action="{{ route('coordinator.clinica.ambientes') }}" class="d-flex justify-content-center">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                            <label for="piso" class="visually-hidden">Seleccionar Piso</label>
                            <select name="piso" id="piso" class="form-select w-50" aria-label="Seleccionar Piso">
                                <option value="">Seleccionar Piso</option>
                                @foreach($pisos as $piso)
                                    <option value="{{ $piso->numero_piso }}" {{ $pisoSeleccionado == $piso->numero_piso ? 'selected' : '' }}>
                                        {{ $piso->numero_piso }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nro.</th>
                        <th>Piso</th>
                        <th>Nombre del Ambiente</th>
                        <th>Tipo de Ambiente</th>
                        <th>Descripción</th>
                        <th>Contenido</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $contador = 1;
                    @endphp
                    @foreach($ambientes as $ambiente)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>{{ $ambiente->numero_piso }}</td>
                            <td>{{ $ambiente->nombre_ambiente }}</td>
                            <td>{{ $ambiente->tipo_ambiente }}</td>
                            <td>{{ $ambiente->descripcion_ambiente }}</td>
                            <td>
                                <a href="{{ route('coordinator.clinica.contenido', ['id_ambiente' => Crypt::encryptString($ambiente->id_ambiente)]) }}" title="Detalles">
                                    <i class="bi bi-plus-circle-fill"></i>
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
    @if(session('info'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Información',
                text: '{{ session('info') }}'
            });
        </script>
    @endif
@endpush
