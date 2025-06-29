@extends('layouts.coodinacion')

@section('title', 'Detalles de Movimientos')

@section('content')
<main class="content px-3 pagdetalles">
    <div class="container-fluid">
        <div class="card">
            <div class="data-section">
                <div class="mb-3">
                    <h3 class="text-center fw-bold">Detalles del Movimiento</h3>
                </div>
        
                <div class="mb-3">
                    <h4>Datos del Movimiento</h4>
                    <p><strong>Laboratorio:</strong> {{ $movimiento->nombre_ambiente }}</p>
                    <p><strong>Usuario:</strong> {{ $movimiento->nombre_usuario }}</p>
                    <p><strong>Descripción:</strong> {{ $movimiento->descripcion }}</p>
                    <p><strong>Semestre:</strong> {{ $movimiento->semestre }}</p>
                    <p><strong>Fecha de Uso:</strong> {{ \Carbon\Carbon::parse($movimiento->fch_uso)->format('d/m/Y') }}</p>
                    <p><strong>Hora de Uso:</strong> {{ $movimiento->hora_uso }}</p>
                    <p><strong>Engardo de Turno:</strong> {{ $personal_inicio->nombre }} {{ $personal_inicio->ap_paterno }} {{ $personal_inicio->ap_materno }}</p>
                    <p><strong>Celular:</strong> {{ $personal_inicio->celular }}</p>
                </div>

                @if($personal_fin)
                <div class="mb-3">
                    <h4>Finalizacion del Movimiento</h4>
                    <p><strong>Fecha de Fin:</strong> {{ \Carbon\Carbon::parse($movimiento->fch_fin)->format('d/m/Y') }}</p>
                    <p><strong>Hora de Fin:</strong> {{ $movimiento->hora_fin }}</p>
                    <p><strong>Encargado de turno:</strong> {{ $personal_fin->nombre }} {{ $personal_fin->ap_paterno }} {{ $personal_fin->ap_materno }}</p>
                    <p><strong>Celular:</strong> {{ $personal_fin->celular }}</p>
                </div>
                @endif

                @if(!$personal_fin)
                    <div class="mb-3">
                        <h4>Aun no finaliza el Uso del Ambiente</h4>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection

@section('dropdown-items')
    @foreach($edificios as $edificio)
        @if($edificio->nombre_edi == 'Edificio Central')
            <li><a class="dropdown-item" href="{{ route('coordinator.inicio') }}">{{ $edificio->nombre_edi }}</a></li>
        @elseif($edificio->nombre_edi == 'Clinica Odontologia')
            <li><a class="dropdown-item" href="{{ route('coordinator.clinica.inicio') }}">{{ $edificio->nombre_edi }}</a></li>
        @else
            <li><a class="dropdown-item" href="#">{{ $edificio->nombre_edi }}</a></li>
        @endif
    @endforeach
@endsection
