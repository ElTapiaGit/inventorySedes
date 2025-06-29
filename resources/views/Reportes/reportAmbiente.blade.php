<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Contenido Ambientes</title>
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
            .no-print { /*para ocultar al imprimir*/
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
            <h4>Contenido de Ambientes</h4>
        </div>
        
        <div class="filter-form no-print">
            <form action="{{ route('reportes.ambientes') }}" method="GET">
                <label for="ambiente_id">Seleccionar Ambiente:</label>
                <select id="ambiente_id" name="ambiente_id">
                    <option value="">Todos los ambientes</option>
                    @foreach($ambientes as $ambiente)
                        <option value="{{ $ambiente->id_ambiente }}" {{ request('ambiente_id') == $ambiente->id_ambiente ? 'selected' : '' }}>
                            {{ $ambiente->nombre }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="no-print">Filtrar</button>
            </form>
        </div>

        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Nombre del Ambiente</th>
                        <th>Tipo de Ambiente</th>
                        <th>Nombre del Artículo</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($articulos as $articulo)
                        <tr>
                            <td>{{ $articulo->ambiente->nombre }}</td>
                            <td>{{ $articulo->ambiente->tipoAmbiente->nombre_amb }}</td>
                            <td>{{ $articulo->nombre }}</td>
                            <td>{{ $articulo->descripcion }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
