@extends('layouts.coordinator')

@section('title', 'Equipos del Ambiente')

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
            <h3 class="text-center titulo fw-bold">Equipos del Ambiente: {{ $ambiente->nombre_completo_ambiente ?? $ambiente->nombre }}</h3>
        </div>
        <div class="btnambiente d-flex justify-content-center mb-3">
            <a href="{{ route($isClinica ? 'coordinator.clinica.equipos' : 'equipos.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-primary mx-2">EQUIPOS</a>
            <a href="{{ route($isClinica ? 'coordinator.clinica.materiales' : 'materiales.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-primary mx-2">MATERIALES</a>
            <a href="{{ route($isClinica ? 'coordinator.clinica.mobiliarios' : 'mobiliarios.show', $isClinica ? ['id_ambiente' => $tokenAmb] : ['token' => $tokenAmb]) }}" class="btn btn-primary mx-2">MOBILIARIO</a>
        </div>
        <div class="mb-3">
            <h4>Lista de Equipos:</h4>
        </div>
        
        @if($equipos->isEmpty())
            <div class="alert alert-info text-center">
                No existen equipos en el ambiente {{ $ambiente->nombre_completo_ambiente ?? $ambiente->nombre }}.
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
                        <th>Vida Útil</th>
                        <th>Fecha Registrada</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $contador = 1;
                    @endphp
                    @foreach($equipos as $equipo)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>{{ $equipo->cod_equipo }}</td>
                            <td>{{ $equipo->nombre_equi }}</td>
                            <td>{{ $equipo->estado_equi }}</td>
                            <td>{{ $equipo->observaciones_equi }}</td>
                            <td>{{ $equipo->vida_util }}</td>
                            <td>{{ \Carbon\Carbon::parse($equipo->fch_registro)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route($isClinica ? 'clinica.equipo.detalles' : 'equipo.detalles', ['token' => Crypt::encrypt($equipo->cod_equipo)]) }}" title="Detalles">
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
