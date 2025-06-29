<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte_Personal</title>
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
            <h4>Personal Registrados</h4>
        </div>

        <!-- Formulario de Filtro -->
        <div class="filter-form">
            <form action="{{ route('reportes.personal') }}" method="GET">
                <label for="tipo_personal">Tipo de Personal:</label>
                <select id="tipo_personal" name="tipo_personal">
                    <option value="">Todos</option>
                    @foreach($tipos_personal as $tipo)
                        <option value="{{ $tipo->id_tipo_per }}" {{ request('tipo_personal') == $tipo->id_tipo_per ? 'selected' : '' }}>
                            {{ $tipo->descripcion_per }}
                        </option>
                    @endforeach
                </select>
                
                <label for="estado">Estado:</label>
                <select id="estado" name="estado">
                    <option value="">Todo</option>
                    <option value="1">Activos</option>
                    <option value="0">Inhabilitado</option>
                </select>
                
                <button type="submit" class="no-print">Filtrar</button>
                <button type="button" class="no-print" onclick="window.print()">Imprimir</button>
            </form>
        </div>

        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Nombre</th>
                        <th>Celular</th>
                        <th>Tipo de Personal</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($personal as $persona)
                        <tr>
                            <td>{{ $persona->ap_paterno }}</td>
                            <td>{{ $persona->ap_materno }}</td>
                            <td>{{ $persona->nombre }}</td>
                            <td>{{ $persona->celular }}</td>
                            <td>{{ $persona->descripcion_per }}</td>
                            <td>{{ $persona->estado ? 'Activo' : 'Inhabilitado' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
