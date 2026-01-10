<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    protected $query;
    protected $columns;
    protected $header;
    protected $totals;

    public function __construct($query, $columns, $header = [], $totals = [])
    {
        $this->query   = $query;
        $this->columns = $columns;
        $this->header  = $header;
        $this->totals  = $totals;
    }

    public function sheets(): array
    {
        return [
            new DataExport(
                $this->query->get(),
                $this->columns,
                $this->header,
                $this->totals
            ),
        ];
    }
}
