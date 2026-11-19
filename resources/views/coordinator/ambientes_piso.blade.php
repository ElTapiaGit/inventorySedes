@extends('layouts.coordinator')

@section('title', 'Ambientes del Piso')

@section('content')
<main class="content px-3 ambiente-clinica">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="text-center titulo fw-bold">Ambientes {{ isset($edificioActivo) ? '- ' . $edificioActivo->nombre_edi : '' }} &nbsp;(Piso {{ $piso->numero_piso ?? ($pisos->numero_piso ?? '') }})</h3>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nro.</th>
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
