<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ExportComponent extends Component
{
    public $model;
    public $where;
    public $columns;
    public $totals;

    public function __construct($model, $where = [], $columns = [], $totals = [])
    {
        $this->model = $model;
        $this->where = $where;
        $this->columns = $columns;
        $this->totals = $totals;
    }

    public function render()
    {
        return view('components.backend.export');
    }
}
