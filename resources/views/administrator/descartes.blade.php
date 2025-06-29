@extends('layouts.admin')

@section('title', 'Descartes')

@section('content')
<div class="content p-3 pagDescarte">
    <div class="container-fluid">
        <h1 class="text-center">Artículos Descartados</h1>
        <form action="{{ route('descartes.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="codigo" class="form-control" placeholder="Código" value="{{ request('codigo') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre" value="{{ request('nombre') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha_inicio" class="form-control" placeholder="Fecha Inicio" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha_fin" class="form-control" placeholder="Fecha Fin" value="{{ request('fecha_fin') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tablaDescartes">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Orden de Descarte</th>
                        <th>Fecha descarte</th>
                        <th>Personal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($descartes as $descarte)
                        <tr>
                            <td>{{ $descarte->codigo }}</td>
                            <td>{{ $descarte->nombre }}</td>
                            <td>{{ $descarte->descrpcion_descarte }}</td>
                            <td>{{ $descarte->orden_desacarte }}</td>
                            <td>{{ \Carbon\Carbon::parse($descarte->fch_descarte)->format('d/m/Y') }}</td>
                            <td>{{ $descarte->personal->nombre }} {{ $descarte->personal->ap_paterno }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $descartes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
