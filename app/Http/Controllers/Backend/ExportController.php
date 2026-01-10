<?php

namespace App\Http\Controllers\Backend;

use App\Services\ExportService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        $model = $request->get('model');
        if (!$model || !class_exists($model)) abort(404, "Модел топилмади!");

        $where = $request->get('where', []);
        $columns = $request->get('columns', []);

        $modelInstance = new $model;
        $tableColumns = \Schema::getColumnListing($modelInstance->getTable());

        $query = $model::query();

        // Agar array bo‘lsa -> whereIn, oddiy → where
        foreach ($where as $column => $value) {
            if (!in_array($column, $tableColumns)) continue;

            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }

        // Nested relations aniqlash
        $relations = [];
        foreach (array_keys($columns) as $col) {
            if (str_contains($col, '.')) $relations[] = explode('.', $col)[0];
        }

        $header = $request->get('header', []);

        $query->with(array_unique($relations));
        $totals = $request->get('totals', []);

        $type = $request->get('type', 'excel');

        if ($type === 'pdf') {
            return ExportService::pdfExport($query, class_basename($model), $columns, $header, $totals);
        }

        return ExportService::excelExport($query, class_basename($model), $columns, $header, $totals);
    }
}
