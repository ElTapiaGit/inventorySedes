@extends('layouts.admin')

@section('title', 'Detalles Uso Ambiente')

@section('content')
<main class="content px-3 pagdetalles">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mt-4">
                    <div class="data-section">
                        <div class="card-header mb-3">
                            <h3 class="text-center fw-bold">Detalles del Movimiento</h3>
                        </div>

                        <div class="mb-3 ">
                            <h4>Datos del Movimiento</h4>
                            <p><strong>Ambiente:</strong> {{ $movimiento->ambiente->nombre }}</p>
                            <p><strong>Usuario:</strong> {{ $movimiento->usuario->nombreCompleto}}</p>
                            <p><strong>Descripción:</strong> {{ $movimiento->descripcion }}</p>
                            <p><strong>Semestre:</strong> {{ $movimiento->semestre }}</p>
                            <p><strong>Fecha de Uso:</strong> {{ \Carbon\Carbon::parse($movimiento->fch_uso)->format('d/m/Y') }}</p>
                            <p><strong>Hora de Uso:</strong> {{ $movimiento->hora_uso }}</p>
                            <p><strong>Encargado de Turno:</strong> {{ $personal_inicio->nombre }} {{ $personal_inicio->ap_paterno }} {{ $personal_inicio->ap_materno }}</p>
                            <p><strong>Celular:</strong> {{ $personal_inicio->celular }}</p>
                        </div>

                        @if($finalUso)
                            <div class="mb-3">
                                <h4>Finalización del Movimiento</h4>
                                <p><strong>Fecha de Fin:</strong> {{ \Carbon\Carbon::parse($finalUso->fch_fin)->format('d/m/Y') }}</p>
                                <p><strong>Hora de Fin:</strong> {{ $finalUso->hora_fin }}</p>
                                <p><strong>Encargado de Turno:</strong> {{ $finalUso->personal->nombre }} {{ $finalUso->personal->ap_paterno }} {{ $finalUso->personal->ap_materno }}</p>
                                <p><strong>Celular:</strong> {{ $finalUso->personal->celular }}</p>
                            </div>
                        @else
                            <div class="mb-3">
                                <h4 style="color: red">Aun no finaliza el Uso del Ambiente</h4>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
