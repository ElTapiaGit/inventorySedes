<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte_MobiliariosMateriales_Adquiridos</title>
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
            background-color: #f4f4f4;
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
        .title {
            text-align: center;
            margin-bottom: 20px;
        }
        .filter-form {
            margin-bottom: 20px;
            margin-left: 40px;
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
            <h2>Reporte de Mobiliarios y Materiales Adquiridos</h2>
        </div>

        <!-- Formulario de Filtrado -->
        <div class="filter-form">
            <form action="{{ route('reportes.mobiliarioMate') }}" method="GET">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}">
                
                <button type="submit" class="no-print">Filtrar</button>
                <button type="button" class="no-print" onclick="window.print()">Imprimir</button>
            </form>
        </div>

        @if($resultados->isEmpty())
            <p>No se encontraron resultados con los filtros aplicados.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Código del Artículo</th>
                        <th>Nombre</th>
                        <th>Fecha de Adquisición</th>
                        <th>Ambiente</th>
                        <th>Piso</th>
                        <th>Edificio</th>
                        <th>Sede</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resultados as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->cod_mueble }}</td>
                        <td>{{ $item->nombre }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->fch_registro)->format('d/m/Y') }}</td>
                        <td>{{ $item->ambiente }}</td>
                        <td>{{ $item->numero_piso }}</td>
                        <td>{{ $item->nombre_edi }}</td>
                        <td>{{ $item->sede }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>

