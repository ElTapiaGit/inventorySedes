<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte_Artículos_Descartados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ddddde;
        }
        .container {
            padding: 20px;
            max-width: 800px;
            margin: auto;
            background-color: #fdfdfd;
        }
        .header {
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header .top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .logo-container {
            display: flex;
            align-items: center;
        }
        .header img {
            height: 60px;
            margin-right: 10px;
        }
        .header .company-name {
            font-size: 18px;
            font-weight: bold;
        }
        .header .date {
            font-size: 14px;
        }
        .header .clinic-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .title {
            text-align: center;
            margin-bottom: 20px;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        .filter-form label {
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="top-row">
                <div class="logo-container">
                    <img src="{{ asset('img/coordinator/logo_sinFondo.png') }}" alt="Logo">
                    <span class="company-name">UNIVERSIDAD <br> LATINOAMERICANA</span>
                </div>
                <div class="date"><strong>Fecha:</strong> {{ now()->setTimezone('America/La_Paz')->format('d/m/Y') }}</div>
            </div>
        </div>
        
        <div class="title">
            <h4>Artículos Descartados</h4>
        </div>
        
        <div class="filter-form">
            <form action="{{ route('reportes.articulosDescartados') }}" method="GET">
                <label for="start_date">Fecha Inicio:</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">

                <label for="end_date">Fecha Fin:</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">

                <button type="submit" class="no-print">Filtrar</button>
                <button type="button" class="no-print" onclick="window.print()">Imprimir</button>
            </form>
        </div>

        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Orden de Descarte</th>
                        <th>Fecha de Descarte</th>
                        <th>Personal que Registró</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                     $contador = 1;   
                    @endphp
                    @foreach($descartes as $descarte)
                        <tr>
                            <td>{{$contador++}}</td>
                            <td>{{ $descarte->codigo }}</td>
                            <td>{{ $descarte->nombre }}</td>
                            <td>{{ $descarte->descrpcion_descarte }}</td>
                            <td>{{ $descarte->orden_desacarte }}</td>
                            <td>{{ \Carbon\Carbon::parse($descarte->fch_descarte)->format('d/m/Y') }}</td>
                            <td>{{ $descarte->personal->ap_paterno }} {{ $descarte->personal->ap_materno }} {{ $descarte->personal->nombre }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>