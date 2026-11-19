@extends('layouts.coordinator')

@section('title', 'Ambientes del Piso')

@section('content')
<main class="content px-3 py-3">
    <div class="container-fluid">
        <!-- Titulo de pagina institucional con boton volver -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
            <h4 class="mb-0 fw-bold" style="color: #0E0D39;">
                <i class="bi bi-door-open me-2 text-primary"></i>Ambientes {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }} (Piso {{ $piso->numero_piso ?? ($pisos->numero_piso ?? '') }})
            </h4>
            <a href="{{ route('coordinator.clinica.ambientes') }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
        
        <div class="card border-0 shadow-sm rounded-3">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-ulat align-middle mb-0">
                    <thead class="table-ulat-header">
                        <tr>
                            <th>Nro.</th>
                            <th>Nombre del Ambiente</th>
                            <th>Tipo de Ambiente</th>
                            <th>Descripcion</th>
                            <th>Contenido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $contador = 1;
                        @endphp
                        @forelse($ambientes as $ambiente)
                            <tr>
                                <td class="text-center">{{ $contador++ }}</td>
                                <td class="fw-bold text-dark">{{ $ambiente->nombre_ambiente }}</td>
                                <td>{{ $ambiente->tipo_ambiente }}</td>
                                <td class="small text-muted">{{ $ambiente->descripcion_ambiente ?: 'Sin descripcion' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('coordinator.clinica.contenido', ['id_ambiente' => Crypt::encryptString($ambiente->id_ambiente)]) }}" class="btn btn-outline-primary btn-sm" title="Ver contenido del ambiente">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Ver Contenido
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No se encontraron ambientes registrados en este piso.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
@endsection
