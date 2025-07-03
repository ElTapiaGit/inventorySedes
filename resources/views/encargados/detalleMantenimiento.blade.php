@extends('layouts.encargado')

@section('title', 'Detalle del Mantenimiento')

@section('content')
<div class="container mt-4">
    <h4 class="text-center mb-4 fw-bolder">Detalle del Mantenimiento</h4>

    <!-- Información General del Mantenimiento -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            Información Inicial
        </div>
        <div class="card-body">
            <p><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($mantenimiento->fch_inicio)->format('d/m/Y') }}</p>
            <p><strong>Informe Inicial:</strong> {{ $mantenimiento->informe_inicial }}</p>
            <p><strong>Técnico Responsable:</strong> {{ $mantenimiento->tecnico->nombre }} {{ $mantenimiento->tecnico->ap_paterno }} {{ $mantenimiento->tecnico->ap_materno }}</p>
            <p><strong>Registrado por:</strong> {{ $mantenimiento->personal->nombre }} {{ $mantenimiento->personal->ap_paterno }}</p>
        </div>
    </div>

    <!-- Artículos Involucrados -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            Artículos en Mantenimiento
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Ambiente</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mantenimiento->detalleMantenimiento as $index => $detalle)
                        @php
                            $equipo = \App\Models\Equipo::where('Cod_equipo', $detalle->cod_articulo)->first();
                            $material = \App\Models\Material::where('cod_mate', $detalle->cod_articulo)->first();
                            $mobiliario = \App\Models\Mobiliario::where('cod_mueble', $detalle->cod_articulo)->first();

                            if ($equipo) {
                                $nombre = $equipo->nombre_equi;
                                $tipo = 'Equipo';
                                $ambiente = $equipo->ambiente->nombre ?? '—';
                            } elseif ($material) {
                                $nombre = $material->descripcion_mate;
                                $tipo = 'Material';
                                $ambiente = $material->ambiente->nombre ?? '—';
                            } elseif ($mobiliario) {
                                $nombre = $mobiliario->descripticion_mueb;
                                $tipo = 'Mobiliario';
                                $ambiente = $mobiliario->ambiente->nombre ?? '—';
                            } else {
                                $nombre = 'Desconocido';
                                $tipo = '—';
                                $ambiente = '—';
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detalle->cod_articulo }}</td>
                            <td>{{ $nombre }}</td>
                            <td>{{ $tipo }}</td>
                            <td>{{ $ambiente }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No hay artículos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Información Final -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            Información de Finalización
        </div>
        <div class="card-body">
            @if($mantenimiento->finalMantenimiento)
                <p><strong>Fecha de Finalización:</strong> {{ \Carbon\Carbon::parse($mantenimiento->finalMantenimiento->fch_final)->format('d/m/Y') }}</p>
                <p><strong>Informe Final:</strong> {{ $mantenimiento->finalMantenimiento->informe_final }}</p>
            @else
                <p class="text-danger"><strong>⚠️ Este mantenimiento aún no ha sido finalizado.</strong></p>
            @endif
        </div>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('encargado.mantenimiento') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
@endsection
