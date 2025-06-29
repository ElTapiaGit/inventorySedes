<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte_Ambientes</title>
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
            <h1>Reporte de Ambientes</h1>
        </div>

        <!-- Formulario de Filtrado -->
        <div class="filter-form">
            <form action="{{ route('reportes.ambientes') }}" method="GET">
                <label for="sede">Sede:</label>
                <select id="sede" name="sede_id">
                    <option value="">Todas las Sedes</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id_sede }}" {{ request('sede_id') == $sede->id_sede ? 'selected' : '' }}>
                            {{ $sede->nombre }}
                        </option>
                    @endforeach
                </select>

                <label for="edificio">Edificio:</label>
                <select id="edificio" name="edificio_id">
                    <option value="">Todos los Edificios</option>
                    @foreach($edificios as $edificio)
                        <option value="{{ $edificio->id_edificio }}" {{ request('edificio_id') == $edificio->id_edificio ? 'selected' : '' }}>
                            {{ $edificio->nombre_edi }}
                        </option>
                    @endforeach
                </select>

                <label for="piso">Piso:</label>
                <select id="piso" name="piso_id">
                    <option value="">Todos los Pisos</option>
                    @foreach($pisos as $piso)
                        <option value="{{ $piso->id_piso }}" {{ request('piso_id') == $piso->id_piso ? 'selected' : '' }}>
                            {{ $piso->numero_piso }}
                        </option>
                    @endforeach
                </select>
                <br> <br>
                <label for="tipo_ambiente">Tipo de Ambiente:</label>
                <select id="tipo_ambiente" name="tipo_ambiente_id">
                    <option value="">Todos los Tipos</option>
                    @foreach($tiposAmbiente as $tipo)
                        <option value="{{ $tipo->id_tipoambiente }}" {{ request('tipo_ambiente_id') == $tipo->id_tipoambiente ? 'selected' : '' }}>
                            {{ $tipo->nombre_amb }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="no-print">Filtrar</button>
                <button type="button" class="no-print" onclick="window.print()">Imprimir</button>
            </form>
        </div>

        @if($ambientes->isEmpty())
            <p>No se encontraron ambientes con los filtros aplicados.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Sede</th>
                        <th>Edificio</th>
                        <th>Piso</th>
                        <th>Tipo de Ambiente</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ambientes as $ambiente)
                    <tr>
                        <td>{{ $ambiente->nombre }}</td>
                        <td>{{ $ambiente->descripcion_amb }}</td>
                        <td>{{ $ambiente->sede_nombre }}</td>
                        <td>{{ $ambiente->nombre_edi }}</td>
                        <td>{{ $ambiente->numero_piso }}</td>
                        <td class="text-center">{{ $ambiente->nombre_amb }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
