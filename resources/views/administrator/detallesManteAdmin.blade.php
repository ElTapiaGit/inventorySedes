@extends('layouts.admin')

@section('title', 'Detalles Mantenimiento')

@section('content')
<main class="content px-3 pagDetallMante">
    <div class="container-fluid">
        <div class="mb-4">
            <h3 class="text-center mt-4 fw-bold">DETALLES DEL MANTENIMIENTO</h3>
        </div>
        <div class="row">
            <div class="col-md-7">
                <!-- Datos del mantenimiento -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Datos del Mantenimiento</h5>
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
                        <h5>Datos del Artículo</h5>
                    </div>
                    <div class="card-body">
                        @if($mantenimiento->cod_equipo)
                            <p><strong>Código del Equipo:</strong> {{ $mantenimiento->cod_equipo }}</p>
                            <p><strong>Nombre del Equipo:</strong> {{ $mantenimiento->nombre_equipo }}</p>
                        @elseif($mantenimiento->cod_mueble)
                            <p><strong>Código del Mobiliario:</strong> {{ $mantenimiento->cod_mueble }}</p>
                            <p><strong>Mobiliario:</strong> {{ $mantenimiento->nombre_mobiliario }}</p>
                            <p><strong>Tipo de Mueble:</strong> {{ $mantenimiento->tipo_mueble }}</p>
                        @elseif($mantenimiento->cod_material)
                            <p><strong>Código del Material:</strong> {{ $mantenimiento->cod_material }}</p>
                            <p><strong>Nombre del Material:</strong> {{ $mantenimiento->nombre_material }}</p>
                        @else
                            <p>No hay detalles del artículo disponibles.</p>
                        @endif
                    </div>
                </div>

                <!-- Datos del técnico -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Datos del Técnico</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $mantenimiento->nombre_tecnico }}</p>
                        <p><strong>Celular:</strong> {{ $mantenimiento->celular_tecnico }}</p>
                        <p><strong>Dirección:</strong> {{ $mantenimiento->direccion_tecnico }}</p>
                    </div>
                </div>

                <!-- Datos del personal encargado -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Datos del Personal Encargado</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $mantenimiento->nombre_personal }}</p>
                        <p><strong>Celular:</strong> {{ $mantenimiento->celular_personal }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <!-- Foto del artículo -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Foto del Artículo</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($mantenimiento->ruta_foto)
                            <img src="{{ asset($mantenimiento->ruta_foto) }}" alt="Foto del artículo" class="img-fluid">
                        @else
                            <p>No hay foto disponible.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<!--mensaje de error todo-->    
@if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
        });
    </script>
@endif
@endsection

