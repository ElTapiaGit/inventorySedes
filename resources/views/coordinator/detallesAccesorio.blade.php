@extends('layouts.coordinator')

@section('title', 'Detalles del Accesorio')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/accesorio.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
@endphp

@section('content')
<main class="content px-3 pagAccesorio">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Detalles del Accesorio</h3>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Código:</th>
                            <td>{{ $accesorio->cod_accesorio }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $accesorio->nombre_acce }}</td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td>{{ $accesorio->descripcion_acce }}</td>
                        </tr>
                        <tr>
                            <th>Observaciones:</th>
                            <td>{{ $accesorio->observacion_ace }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>{{ $accesorio->estado_acce }}</td>
                        </tr>
                        <tr>
                            <th>Vida Útil:</th>
                            <td>{{ $accesorio->vida_util }}</td>
                        </tr>
                        <tr>
                            <th>Ubicación:</th>
                            <td>{{ $accesorio->ubicacion }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td>{{ \Carbon\Carbon::parse($accesorio->fch_registro_acce)->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <h3>Foto del Accesorio</h3>
                @if ($accesorio->foto)
                    <img src="{{ inventario_img_url($accesorio->foto->ruta_foto ?? null) }}" class="img-thumbnail" alt="Foto del accesorio">
                @else
                    <p>No hay foto disponible</p>
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <a href="{{ route($isClinica ? 'clinica.accesorios.index' : 'accesorios.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</main>
@endsection
