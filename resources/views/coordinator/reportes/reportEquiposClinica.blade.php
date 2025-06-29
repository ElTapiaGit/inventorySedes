<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte_Equipos_Clinica</title>
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
            text-align: center;
        }
        .filter-form input[type="date"] {
            padding: 5px;
            margin-right: 10px;
            border: none;
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
            <div class="clinic-title">CLINICA ODONTOLOGICA</div>
        </div>
        
        <div class="title">
            <h4>Reporte De Equipos Adquiridos</h4>
        </div>

        <!-- Formulario de Filtrado -->
        <div class="filter-form">
            <form action="{{ route('reporte.clinica.equipos') }}" method="GET">
                <label for="fecha_inicio">Desde:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                
                <label for="fecha_fin">Hasta:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}">
                
                <button type="submit" class="no-print">Filtrar</button>
                <button type="button" class="no-print" onclick="window.print()">Imprimir</button>
            </form>
        </div>

        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Nro.</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Fecha Adquirida</th>
                        <th>Ambiente</th>
                        <th>Piso</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipos as $index => $equipo)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $equipo->cod_equipo }}</td>
                            <td>{{ $equipo->nombre_equi }}</td>
                            <td>{{ \Carbon\Carbon::parse($equipo->fch_registro)->format('d/m/Y') }}</td>

                            <td>{{ $equipo->nombre_ambiente }}</td>
                            <td>{{ $equipo->numero_piso }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>