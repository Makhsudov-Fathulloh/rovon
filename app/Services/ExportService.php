<?php

namespace App\Services;

use App\Exports\MultiSheetExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportService
{
    public static function excelExport($query, $fileName, $columns = [], $header = [], $totals = [])
    {
        return Excel::download(new MultiSheetExport(
            $query,
            $columns,
            $header,
            $totals
        ), $fileName . '.xlsx');
    }

    public static function pdfExport($query, $fileName, $columns = [], $header = [], $totals = [])
    {
        $data = $query->get();

        $pdf = Pdf::loadView('/backend/export/pdf', [
            'data'    => $data,
            'columns' => $columns,
            'header'  => $header,
            'totals'  => $totals,
        ])->setPaper('a4', 'landscape')
          ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download($fileName . '.pdf');
    }
}
