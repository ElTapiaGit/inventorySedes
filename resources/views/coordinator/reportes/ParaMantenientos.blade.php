<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento Clinica Odontologica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ddddde
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
            height: 60px; /* Ajusta el tamaño del logo según tus necesidades */
            margin-right: 10px; /* Espacio entre el logo y el nombre */
        }
        .header .company-name {
            font-size: 18px; /* Ajusta el tamaño del texto según tus necesidades */
            font-weight: bold;
        }
        .header .date {
            font-size: 14px;
        }
        .header .clinic-title {
            text-align: center;
            font-size: 18px; /* Ajusta el tamaño del texto del título */
            font-weight: bold;  
        }
        .title {
            text-align: center;
            margin-bottom: 20px;
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
                <div class="date"><strong>Fecha:</strong> {{ now()->format('d/m/Y') }}</div>
            </div>
            <div class="clinic-title">CLINICA ODONTOLOGICA</div>
        </div>
        
        <div class="title">
            <h4>Equipos Para Mantenimiento</h4>
        </div>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Observaciones</th>
                        <th>Estado</th>
                        <th>Ambiente</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipos as $equipo)
                        <tr>
                            <td>{{ $equipo->Cod_equipo }}</td>
                            <td>{{ $equipo->nombre_equi }}</td>
                            <td>{{ $equipo->observaciones_equi }}</td>
                            <td>{{ $equipo->estado_equi }}</td>
                            <td>{{ $equipo->nombre_ambiente }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.querySelector('.btn-success').addEventListener('click', function () {
            const url = '{{ route('print.equipos') }}'; // Asegúrate de ajustar esta ruta según sea necesario
            window.open(url, '_blank');
        });
    </script>
</body>
</html>
