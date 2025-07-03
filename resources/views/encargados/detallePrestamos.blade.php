@extends('layouts.encargado')

@section('title', 'Detalle del Préstamo')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4 text-center fw-bolder">Detalle del Préstamo</h4>

    <!-- Información General del Préstamo -->
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            Información del Préstamo
        </div>
        <div class="card-body">
            <p><strong>Solicitante:</strong> {{ $prestamo->nombre_solicitante }}</p>
            <p><strong>Responsable:</strong> {{ $prestamo->personal->nombre }} {{ $prestamo->personal->ap_paterno }}</p>
            <p><strong>Descripción:</strong> {{ $prestamo->descripcion_prestamo }}</p>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}</p>
            <p><strong>Hora:</strong> {{ $prestamo->hora_prestamo }}</p>
        </div>
    </div>

    <!-- Artículos Prestados -->
    <div class="card shadow-sm mb-4 border-info">
        <div class="card-header bg-info text-white">
            Artículos Prestados
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Nombre del Artículo</th>
                        <th>Ambiente</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prestamo->detallePrestamos as $index => $detalle)
                        @php
                            $equipo = \App\Models\Equipo::where('Cod_equipo', $detalle->cod_articulo)->first();
                            $material = \App\Models\Material::where('cod_mate', $detalle->cod_articulo)->first();
                            $mobiliario = \App\Models\Mobiliario::where('cod_mueble', $detalle->cod_articulo)->first();

                            if ($equipo) {
                                $nombre = $equipo->nombre_equi;
                                $ambiente = $equipo->ambiente->nombre ?? '—';
                            } elseif ($material) {
                                $nombre = $material->descripcion_mate;
                                $ambiente = $material->ambiente->nombre ?? '—';
                            } elseif ($mobiliario) {
                                $nombre = $mobiliario->descripticion_mueb;
                                $ambiente = $mobiliario->ambiente->nombre ?? '—';
                            } else {
                                $nombre = 'Desconocido';
                                $ambiente = '—';
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detalle->cod_articulo }}</td>
                            <td>{{ $nombre }}</td>
                            <td>{{ $ambiente }}</td>
                            <td>{{ $detalle->observacion_detalle ?? 'Sin observación' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No hay artículos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Información de Devolución -->
    <div class="card shadow-sm border-success">
        <div class="card-header bg-success text-white">
            Información de Devolución
        </div>
        <div class="card-body">
            @if($prestamo->devolucion)
                <p><strong>Fecha de Devolución:</strong> {{ \Carbon\Carbon::parse($prestamo->devolucion->fch_devolucion)->format('d/m/Y') }}</p>
                <p><strong>Hora:</strong> {{ $prestamo->devolucion->hora_devolucion }}</p>
                <p><strong>Descripción:</strong> {{ $prestamo->devolucion->descripcion_devolucion }}</p>
                <p><strong>Registrada por:</strong> {{ $prestamo->devolucion->personal->nombre }} {{ $prestamo->devolucion->personal->ap_paterno }}</p>
                <span class="badge bg-success">Devolución Registrada</span>
            @else
                <p class="text-danger"><strong>⚠️ Este préstamo aún no ha sido devuelto.</strong></p>
            @endif
        </div>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('encargado.prestamo') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
@endsection
