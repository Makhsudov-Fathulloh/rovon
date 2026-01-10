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

                // ===== HEADER =====
                if (!empty($this->header['title'])) {
                $sheet->setCellValue('C1', $this->header['title']);
                $sheet->mergeCells('C1:F1');

                $sheet->getStyle('C1')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 16,
                        'color' => ['argb' => 'FF000000'], // text rangi (qora)
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFFFF'], // background rangi (oq)
                    ],
                ]);
            }

            if (!empty($this->header['subtitle'])) {
                $sheet->setCellValue('C2', $this->header['subtitle']);
                $sheet->mergeCells('C2:F2');

                $sheet->getStyle('C2')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 12,
                        'color' => ['argb' => 'FF000000'], // text rangi (qora)
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFFFF'], // background rangi (oq)
                    ],
                ]);
            }

            if (!empty($this->header['date'])) {
                $sheet->setCellValue('C3', Carbon::parse($this->header['date'])->format('d.m.Y H:i'));
                $sheet->mergeCells('C3:F3');

                $sheet->getStyle('C3')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'color'  => ['argb' => 'FF000000'], // text rangi qora
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFFFF'], // background rangi (oq)
                    ],
                ]);
            }

                // ===== LOGO =====
                if (!empty($this->header['logo_left'])) {
                    $logoLeft = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $logoLeft->setPath(public_path($this->header['logo_left']));
                    $logoLeft->setHeight(65);
                    // $logoRight->setWidth();
                    $logoLeft->setCoordinates('A1');
                    $logoLeft->setOffsetX(15);   // chapdan biroz ichkariga
                    $logoLeft->setOffsetY(5);   // yuqoridan biroz pastga
                    $logoLeft->setWorksheet($sheet);
                }

                $highestColumn = $sheet->getHighestColumn();
                $logoCoord = $highestColumn . '1';

                if (!empty($this->header['logo_right'])) {
                    $logoRight = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $logoRight->setPath(public_path($this->header['logo_right']));
                    $logoRight->setHeight(65);
                    // $logoRight->setWidth();
                    $logoRight->setCoordinates($logoCoord);
                    $logoRight->setOffsetX(5); // o'ngdan biroz ichkariga
                    $logoRight->setOffsetY(5);  // yuqoridan biroz pastga
                    $logoRight->setWorksheet($sheet);
                }

                // ===== TOTALS =====
                $startRow = $sheet->getHighestRow() + 3;
                foreach ($this->totals as $label => $value) {
                    $sheet->setCellValue("A{$startRow}", $label);
                    $sheet->setCellValue("B{$startRow}", $value);
                    $startRow++;
                }

                // ===== COLUMN HEADINGS (5-qator) BOLD QILISH =====
                $headerRange = 'A6:' . $highestColumn . '6';

                $sheet->getStyle($headerRange)->applyFromArray([
                   'font' => [
                      'bold'  => true,
                      'color' => ['argb' => 'FF444444'], // Yumshoqroq (to'q kulrang) rang
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    // 'borders' => [
                    //     'allBorders' => [
                    //         'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    //     ],
                    // ],
                ]);

               // ===== DATA USTUNLARINI TEXT-CENTER VA WRAP QILISH =====
               $highestRow = $sheet->getHighestRow();
               $highestColumn = $sheet->getHighestColumn();
               $sheet->getStyle("A6:{$highestColumn}{$highestRow}")
                   ->getAlignment()
                   ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                   ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                   ->setWrapText(true);

               // ===== COLUMN WIDTH AUTOSIZE =====
               foreach (range('A', $highestColumn) as $col) {
                   $sheet->getColumnDimension($col)->setAutoSize(true);
               }
           },
       ];
   }


    public function startCell(): string
    {
        return 'A6';
    }
}
