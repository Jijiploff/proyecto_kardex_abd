<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen Mensual - KardexPro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4FA3FF;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4FA3FF;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-box {
            background-color: #f0f8ff;
            border: 1px solid #4FA3FF;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .info-box strong {
            color: #4FA3FF;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        thead {
            background-color: #4FA3FF;
            color: white;
        }
        th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .positive {
            color: #35e0b8;
            font-weight: bold;
        }
        .negative {
            color: #ff5a5a;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Resumen Mensual</h1>
        <p><strong>KardexPro - Sistema de Inventario</strong></p>
        <p>Fecha de generación: {{ $fecha }}</p>
    </div>

    <div class="info-box">
        <p><strong>Producto:</strong> {{ $producto ? $producto->Producto . ' - ' . $producto->Descripcion : $idProducto }}</p>
        <p><strong>Año:</strong> {{ $anio }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th style="text-align: right;">Entradas</th>
                <th style="text-align: right;">Salidas</th>
                <th style="text-align: right;">Valor Entradas</th>
                <th style="text-align: right;">Valor Salidas</th>
                <th style="text-align: right;">Stock Final</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data) && count($data) > 0)
                @php
                    $mesesNombres = [
                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                    ];
                @endphp
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row->NombreMes ?? ($mesesNombres[$row->Mes] ?? $row->Mes) }}</td>
                        <td style="text-align: right;" class="positive">+{{ number_format($row->TotalEntradas ?? 0, 2) }}</td>
                        <td style="text-align: right;" class="negative">{{ number_format($row->TotalSalidas ?? 0, 2) }}</td>
                        <td style="text-align: right;">S/ {{ number_format($row->ValorEntradas ?? 0, 2) }}</td>
                        <td style="text-align: right;">S/ {{ number_format($row->ValorSalidas ?? 0, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($row->StockFinal ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" style="text-align: center;">No hay datos para el producto y año seleccionados</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Generado por KardexPro - Sistema de Gestión de Inventario</p>
    </div>
</body>
</html>
