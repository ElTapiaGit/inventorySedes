@extends('layouts.coordinator')

@section('title', 'Detalles de Mantenimiento')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/mantenimiento.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center fw-bold">DETALLES DEL MANTENIMIENTO</h3>
        </div>
        <div class="row">
            <div class="col-md-8">
                <!-- Datos del mantenimiento -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4>Datos del Mantenimiento</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Informe Inicial:</strong> <br> &nbsp; &nbsp;{{ $mantenimiento->informe_inicial }}</p>
                        <p><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($mantenimiento->fch_inicio)->format('d/m/Y') }}</p>

                        @if ($mantenimiento->informe_final !== null && $mantenimiento->fch_final !== null)
                            <p><strong>Informe Final:</strong><br>&nbsp; &nbsp;{{ $mantenimiento->informe_final }}</p>
                            <p><strong>Fecha de Finalización:</strong> {{ \Carbon\Carbon::parse($mantenimiento->fch_final)->format('d/m/Y') }}</p>
                        @else
                            <h3 style="color: red"><strong>Aún no se ha finalizado el mantenimiento.</strong></h3>
                        @endif
                    </div>
                </div>

                <!-- Datos del artículo -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4>Datos del Artículo</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Código:</strong> {{ $articulo->codigo }}</p>
                        <p><strong>Nombre:</strong> {{ $articulo->nombre }}</p>
                    </div>
                </div>

                <!-- Datos del técnico -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4>Datos del Técnico</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $mantenimiento->nombre }}</p>
                        <p><strong>Celular:</strong> {{ $mantenimiento->celular }}</p>
                        <p><strong>Dirección:</strong> {{ $mantenimiento->direccion }}</p>
                    </div>
                </div>

                <!-- Datos del personal encargado -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4>Datos del Personal Encargado</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $personal->nombre_completo ?? ($personal->nombre . ' ' . $personal->ap_paterno) }}</p>
                        <p><strong>Celular:</strong> {{ $personal->celular }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Foto del artículo -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4>Foto del Artículo</h4>
                    </div>
                    <div class="card-body text-center">
                        @if($mantenimiento->ruta_foto)
                            <img src="{{ inventario_img_url($mantenimiento->ruta_foto ?? null) }}" alt="Foto del artículo" class="img-fluid">
                        @else
                            <p>No hay foto disponible.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mb-4">
            <a href="{{ route($isClinica ? 'clinica.historialmantenimientos.index' : 'historialmantenimientos.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</main>
@endsection
