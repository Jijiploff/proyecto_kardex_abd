<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ValorInventarioExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithTitle, WithCustomStartCell
{
    protected $data;
    protected $producto;

    public function __construct($data, $producto)
    {
        $this->data = $data;
        $this->producto = $producto;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Producto',
            'Descripción',
            'Unidad Medida',
            'Stock Real',
            'Costo Promedio',
            'Valor Inventario',
            'Precio Venta',
            'Valor Venta'
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
                foreach (range('A', 'H') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Set minimum width for better readability
                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(45);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(20);
                
                // Add borders to all cells
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // Header row border
                $sheet->getStyle('A2:H2')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['rgb' => '1E3A8A']
                        ]
                    ]
                ]);
                
                // Data cells border
                $sheet->getStyle('A3:H' . $highestRow)->applyFromArray([
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
                        $sheet->getStyle('A' . $i . ':H' . $i)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F9FAFB']
                            ]
                        ]);
                    }
                }
                
                // Align numbers to the right
                $sheet->getStyle('D3:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Format numbers
                $sheet->getStyle('D3:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('F3:H' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
                
                // Freeze header row
                $sheet->freezePane('A3');
                
                // Set row height for header
                $sheet->getRowDimension(2)->setRowHeight(30);
                
                // Set default font for data cells
                $sheet->getStyle('A3:H' . $highestRow)->applyFromArray([
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
        return 'Valor Inventario';
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function prepareRows($rows)
    {
        $headerRow = [];
        if ($this->producto) {
            $headerRow = [
                'Producto: ' . $this->producto->Producto . ' - ' . $this->producto->Descripcion,
                '', '', '', '', '', '', ''
            ];
        } else {
            $headerRow = [
                'Todos los productos',
                '', '', '', '', '', '', ''
            ];
        }

        // Check if $rows is already an array or a collection
        if (is_array($rows)) {
            return array_merge([$headerRow], $rows);
        }
        
        return array_merge([$headerRow], $rows->toArray());
    }
}
