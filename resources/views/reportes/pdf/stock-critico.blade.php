<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Stock Crítico - KardexPro</title>
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
        .negative {
            color: #ff5a5a;
            font-weight: bold;
        }
        .positive {
            color: #35e0b8;
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
        <h1>Stock Crítico</h1>
        <p><strong>KardexPro - Sistema de Inventario</strong></p>
        <p>Fecha de generación: {{ $fecha }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Descripción</th>
                <th style="text-align: right;">Stock Mínimo</th>
                <th style="text-align: right;">Stock Real</th>
                <th style="text-align: right;">Diferencia</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data) && count($data) > 0)
                @foreach($data as $row)
                    <?php
                        $diff = floatval($row->Diferencia ?? 0);
                        $diffClass = $diff < 0 ? 'negative' : ($diff > 0 ? 'positive' : '');
                    ?>
                    <tr>
                        <td>{{ $row->Producto ?? '—' }}</td>
                        <td>{{ $row->Descripcion ?? '—' }}</td>
                        <td style="text-align: right;">{{ number_format($row->StockMin ?? 0, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($row->StockReal ?? 0, 2) }}</td>
                        <td style="text-align: right;" class="{{ $diffClass }}">{{ number_format($diff, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" style="text-align: center;">No hay datos de stock crítico</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Generado por KardexPro - Sistema de Gestión de Inventario</p>
    </div>
</body>
</html>
