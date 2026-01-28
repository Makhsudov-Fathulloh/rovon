<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Helpers\PriceHelper;
use Carbon\Carbon;

class DataExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell
{
    protected $data;
    protected $columns;
    protected $totals;
    protected $header;

    public function __construct($data, $columns, $header = [], $totals = [])
    {
        $this->data    = $data;
        $this->columns = $columns;
        $this->header  = $header;
        $this->totals  = $totals;
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            $row = [];

            foreach ($this->columns as $key => $label) {
                if (str_contains($key, ':')) {
                    [$field, $format] = explode(':', $key);
                    $value = data_get($item, $field);

                    $row[$label] = match ($format) {
                        'datetime' => $value?->format('Y-m-d H:i:s'),
                        'date'     => $value?->format('Y-m-d'),

                        'price_format' => PriceHelper::format(
                             $value, // Order/OrderOutput modelidagi total_price
                             data_get($item, 'currency')    // Order/OrderOutput modelidagi currency
                         ),

                        default    => $value,
                    };
                    continue;
                }

                $value = data_get($item, $key);
                $row[$label] = $value === 0 ? "0" : $value;
            }

            return $row;
        });
    }

    public function headings(): array
    {
        return array_values($this->columns);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                // 1. Sarlavhalarni dinamik markazga olish (A dan oxirgi ustungacha)
                $titleRange = "A1:{$highestColumn}1";
                $subtitleRange = "A2:{$highestColumn}2";
                $dateRange = "A3:{$highestColumn}3";

                // Header styling helper function
                $headerStyle = function($size, $bold = true, $italic = false) {
                    return [
                        'font' => ['bold' => $bold, 'size' => $size, 'italic' => $italic],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                    ];
                };

                if (!empty($this->header['title'])) {
                    $sheet->mergeCells($titleRange);
                    $sheet->setCellValue('A1', $this->header['title']);
                    $sheet->getStyle('A1')->applyFromArray($headerStyle(16));
                }

                if (!empty($this->header['subtitle'])) {
                    $sheet->mergeCells($subtitleRange);
                    $sheet->setCellValue('A2', $this->header['subtitle']);
                    $sheet->getStyle('A2')->applyFromArray($headerStyle(12));
                }

                if (!empty($this->header['date'])) {
                    $sheet->mergeCells($dateRange);
                    $dateValue = Carbon::parse($this->header['date'])->format('d.m.Y H:i');
                    $sheet->setCellValue('A3', $dateValue);
                    $sheet->getStyle('A3')->applyFromArray($headerStyle(10, false, true));
                }

                // 2. LOGOLAR (Dinamik joylashuv)
                if (!empty($this->header['logo_left'])) {
                    $this->addLogo($sheet, $this->header['logo_left'], 'A1', 15);
                }

                if (!empty($this->header['logo_right'])) {
                    // Oxirgi ustunga qo'yish
                    $this->addLogo($sheet, $this->header['logo_right'], $highestColumn . '1', 20);
                }

                // 3. JADVAL BOSHI (A5 dan boshlab)
                $headerRange = "A5:{$highestColumn}5";
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FF444444']],
                    'alignment' => ['horizontal' => 'center'],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF2F2F2'] // Och kulrang fon
                    ]
                ]);

                // 4. MA'LUMOTLARNI FORMATLASH
                $dataRange = "A5:{$highestColumn}{$highestRow}";
                $sheet->getStyle($dataRange)->getAlignment()->setHorizontal('center')->setWrapText(true);

                // Borders qo'shish (ixtiyoriy, jadvalni aniq ko'rsatadi)
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // 5. TOTALS (Jami qismi)
                $footerRow = $highestRow + 2;
                foreach ($this->totals as $label => $value) {
                    $sheet->setCellValue("A{$footerRow}", $label);
                    $sheet->setCellValue("B{$footerRow}", $value);

                    $sheet->getStyle("A{$footerRow}")->getFont()->setBold(true);
                    $sheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal('right');
                    $sheet->getStyle("B{$footerRow}")->getAlignment()->setHorizontal('left');
                    $footerRow++;
                }

                // 6. AVTO-WIDTH (Ustun kengligi)
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }

    private function addLogo($sheet, $path, $coordinates, $offsetX = 0)
    {
        $drawing = new Drawing();
        $drawing->setPath(public_path($path));
        $drawing->setHeight(50);
        $drawing->setCoordinates($coordinates);
        $drawing->setOffsetX($offsetX);
        $drawing->setOffsetY(15);
        $drawing->setWorksheet($sheet);
    }

    public function startCell(): string
    {
        return 'A5';
    }
}
