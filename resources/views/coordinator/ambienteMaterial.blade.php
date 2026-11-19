@extends('layouts.coordinator')

@section('title', 'Materiales del Ambiente')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/ambiente.css') }}">
@endpush

@php
    $isClinica = request()->is('*clinica*');
    $tokenAmb = Crypt::encryptString($ambiente->id_ambiente);
@endphp

@section('content')
<main class="content px-3 ambiente-page">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center titulo fw-bold">Materiales del Ambiente: {{ $ambiente->nombre_completo_ambiente ?? $ambiente->nombre }}</h3>
        </div>
        <div class="btnambiente d-flex justify-content-center mb-3">
            <a href="{{ route($isClinica ? 'coordinator.clinica.equipos' : 'equipos.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-primary mx-2">EQUIPOS</a>
            <a href="{{ route($isClinica ? 'coordinator.clinica.materiales' : 'materiales.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-primary mx-2">MATERIALES</a>
            <a href="{{ route($isClinica ? 'coordinator.clinica.mobiliarios' : 'mobiliarios.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-primary mx-2">MOBILIARIO</a>
        </div>
        <div class="mb-3">
            <h4>Lista de Materiales:</h4>
        </div>
        
        @if($materiales->isEmpty())
            <div class="alert alert-info text-center">
                No existen materiales en el ambiente {{ $ambiente->nombre_completo_ambiente ?? $ambiente->nombre }}.
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nro.</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                        <th>Fecha Registrada</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $contador = 1;
                    @endphp
                    @foreach($materiales as $material)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>{{ $material->cod_mate }}</td>
                            <td>{{ $material->tipo_mate }}</td>
                            <td>{{ $material->estado_mate }}</td>
                            <td>{{ $material->observacion_mate }}</td>
                            <td>{{ \Carbon\Carbon::parse($material->fch_registrada)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route($isClinica ? 'clinica.material.detalles' : 'material.detalles', ['token' => Crypt::encrypt($material->cod_mate)]) }}" title="Detalles">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</main>
@endsection
