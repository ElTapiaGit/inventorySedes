<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Equipos</title>
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
            margin-bottom: 10px;
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
            <h4>Reporte de Estado de Equipos</h4>
        </div>

        <!-- Formulario de Filtro -->
        <div class="filter-form">
            <form action="{{ route('reportes.estado.equipos') }}" method="GET">
                <label for="estado_equi">Estado de Equipo:</label>
                <input list="estados_equipos" id="estado_equi" name="estado_equi" value="{{ request('estado_equi') }}">
                <datalist id="estados_equipos">
                    <option value="Para reparar">
                    <option value="Para mantenimiento">
                    <option value="Nuevo">
                </datalist>

                <label for="edificio">Edificio:</label>
                <select id="edificio" name="edificio">
                    <option value="">Todos</option>
                    @foreach($edificios as $edificio)
                        <option value="{{ $edificio->id_edificio }}" {{ request('edificio') == $edificio->id_edificio ? 'selected' : '' }}>
                            {{ $edificio->nombre_edi }}
                        </option>
                    @endforeach
                </select>
                
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
                        <th>Marca</th>
                        <th>Estado</th>
                        <th>Edificio</th>
                        <th>Piso</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $contador=1;
                    @endphp
                    @foreach($equipos as $equipo)
                        <tr>
                            <td>{{$contador ++}}</td>
                            <td>{{ $equipo->cod_equipo }}</td>
                            <td>{{ $equipo->nombre_equi }}</td>
                            <td>{{ $equipo->marca }}</td>
                            <td>{{ ucfirst($equipo->estado_equi) }}</td>
                            <td>{{ $equipo->ambiente->piso->edificios->nombre_edi }}</td>
                            <td>{{ $equipo->ambiente->piso->numero_piso }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
