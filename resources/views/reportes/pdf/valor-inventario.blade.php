<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Valor Inventario - KardexPro</title>
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
        <h1>Valor Inventario</h1>
        <p><strong>KardexPro - Sistema de Inventario</strong></p>
        <p>Fecha de generación: {{ $fecha }}</p>
    </div>

    @if($producto)
        <div class="info-box">
            <p><strong>Producto:</strong> {{ $producto->Producto . ' - ' . $producto->Descripcion }}</p>
        </div>
    @else
        <div class="info-box">
            <p><strong>Reporte:</strong> Todos los productos</p>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Descripción</th>
                <th>Unidad Medida</th>
                <th style="text-align: right;">Stock Real</th>
                <th style="text-align: right;">Costo Promedio</th>
                <th style="text-align: right;">Valor Inventario</th>
                <th style="text-align: right;">Precio Venta</th>
                <th style="text-align: right;">Valor Venta</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data) && count($data) > 0)
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row->Producto ?? '—' }}</td>
                        <td>{{ $row->Descripcion ?? '—' }}</td>
                        <td>{{ $row->UniMed ?? '—' }}</td>
                        <td style="text-align: right;">{{ number_format($row->StockReal ?? 0, 2) }}</td>
                        <td style="text-align: right;">S/ {{ number_format($row->CostoPromedio ?? 0, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;">S/ {{ number_format($row->ValorInventario ?? 0, 2) }}</td>
                        <td style="text-align: right;">S/ {{ number_format($row->PrecVenta ?? 0, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;">S/ {{ number_format($row->ValorVenta ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" style="text-align: center;">No hay datos de inventario disponibles</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Generado por KardexPro - Sistema de Gestión de Inventario</p>
    </div>
</body>
</html>
