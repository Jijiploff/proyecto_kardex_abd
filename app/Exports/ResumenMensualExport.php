<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ResumenMensualExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents, WithTitle, WithCustomStartCell
{
    protected $data;
    protected $producto;
    protected $anio;

    public function __construct($data, $producto, $anio)
    {
        $this->data = $data;
        $this->producto = $producto;
        $this->anio = $anio;
    }

    public function collection()
    {
        return $this->data;
    }

    public function map($row): array
    {
        $mesesNombres = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // Handle both object and array input
        $mes = is_array($row) ? ($row['NombreMes'] ?? ($row['Mes'] ?? '')) : ($row->NombreMes ?? ($row->Mes ?? ''));
        $totalEntradas = is_array($row) ? ($row['TotalEntradas'] ?? 0) : ($row->TotalEntradas ?? 0);
        $totalSalidas = is_array($row) ? ($row['TotalSalidas'] ?? 0) : ($row->TotalSalidas ?? 0);
        $valorEntradas = is_array($row) ? ($row['ValorEntradas'] ?? 0) : ($row->ValorEntradas ?? 0);
        $valorSalidas = is_array($row) ? ($row['ValorSalidas'] ?? 0) : ($row->ValorSalidas ?? 0);
        $stockFinal = is_array($row) ? ($row['StockFinal'] ?? 0) : ($row->StockFinal ?? 0);

        return [
            $mesesNombres[$mes] ?? $mes,
            $totalEntradas,
            $totalSalidas,
            $valorEntradas,
            $valorSalidas,
            $stockFinal,
        ];
    }

    public function headings(): array
    {
        return [
            'Mes',
            'Entradas',
            'Salidas',
            'Valor Entradas',
            'Valor Salidas',
            'Stock Final'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 13,
                    'name' => 'Calibri'
                ],
                'fill' => [
                    'fillType' => Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => ['rgb' => '1E3A8A'],
                    'endColor' => ['rgb' => '3B82F6']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'name' => 'Calibri', 'color' => ['rgb' => '1E3A8A']]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Auto-size columns
                foreach (range('A', 'F') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Set minimum width for better readability
                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(18);
                
                // Add borders to all cells
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // Header row border
                $sheet->getStyle('A2:F2')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['rgb' => '1E3A8A']
                        ]
                    ]
                ]);
                
                // Data cells border
                $sheet->getStyle('A3:F' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB']
                        ]
                    ]
                ]);
                
                // Alternate row colors
                for ($i = 3; $i <= $highestRow; $i++) {
                    if ($i % 2 == 1) {
                        $sheet->getStyle('A' . $i . ':F' . $i)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F9FAFB']
                            ]
                        ]);
                    }
                }
                
                // Align numbers to the right
                $sheet->getStyle('B3:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Format numbers
                $sheet->getStyle('B3:C' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('D3:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
                
                // Freeze header row
                $sheet->freezePane('A3');
                
                // Set row height for header
                $sheet->getRowDimension(2)->setRowHeight(30);
                
                // Set default font for data cells
                $sheet->getStyle('A3:F' . $highestRow)->applyFromArray([
                    'font' => [
                        'size' => 11,
                        'name' => 'Calibri'
                    ]
                ]);
            },
        ];
    }

    public function title(): string
    {
        return 'Resumen Mensual';
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function prepareRows($rows)
    {
        $headerRow = [
            'Producto: ' . ($this->producto ? $this->producto->Producto . ' - ' . $this->producto->Descripcion : 'N/A'),
            'Año: ' . $this->anio,
            '', '', '', ''
        ];

        // Check if $rows is already an array or a collection
        if (is_array($rows)) {
            return array_merge([$headerRow], $rows);
        }
        
        return array_merge([$headerRow], $rows->toArray());
    }
}
