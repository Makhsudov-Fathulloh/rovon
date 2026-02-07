<?php

namespace App\Models\Search;

use App\Models\DefectReport;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DefectReportSearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function search(Request $request)
    {
        $filters = $request->get('filters', []);
        $query = DefectReport::query();

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['organization_id'])) {
            $query->where('organization_id', $filters['organization_id']);
        }

        if (!empty($filters['section_shift_stage'])) {
            $raw = trim($filters['section_shift_stage']);
            $parts = array_values(array_filter(
                array_map(fn($v) => mb_strtolower(trim($v)), explode('.', $raw))
            ));

            $query->where(function ($q) use ($parts) {
                // 🔹 1) NUQTASIZ — oddiy qidiruv
                if (count($parts) === 1) {
                    $keyword = $parts[0];

                    $q->whereHas('section', fn($sq) =>
                    $sq->whereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"]))
                        ->orWhereHas('shift', fn($sq) =>
                    $sq->whereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"]))
                        ->orWhereHas('stage', fn($sq) =>
                    $sq->whereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"]));
                }

                // 🔹 2) Section.Shift
                elseif (count($parts) === 2) {
                    [$section, $shift] = $parts;

                    $q->whereHas('section', fn($sq) =>
                    $sq->whereRaw('LOWER(title) LIKE ?', ["%{$section}%"]))
                        ->whereHas('shift', fn($sq) =>
                    $sq->whereRaw('LOWER(title) LIKE ?', ["%{$shift}%"]));
                }

                // 🔹 3) Section.Shift.Stage
                else {
                    [$section, $shift, $stage] = $parts;

                    $q->whereHas('section', fn($sq) =>
                    $sq->whereRaw('LOWER(title) LIKE ?', ["%{$section}%"]))
                        ->whereHas('shift', fn($sq) =>
                    $sq->whereRaw('LOWER(title) LIKE ?', ["%{$shift}%"]))
                        ->whereHas('stage', fn($sq) =>
                    $sq->whereRaw('LOWER(title) LIKE ?', ["%{$stage}%"]));
                }
            });
        }

        if (!empty($filters['stage_defect_amount_percent'])) {
            $numeric = preg_replace('/[^\d.]/', '', $filters['stage_defect_amount_percent']);

            if ($numeric !== '') {
                $query->where(function ($q) use ($numeric) {
                    $q->whereRaw('CAST(stage_count AS TEXT) LIKE ?', ["%{$numeric}%"])
                        ->orWhereRaw('CAST(total_defect_amount AS TEXT) LIKE ?', ["%{$numeric}%"])
                        ->orWhereRaw('CAST(defect_amount AS TEXT) LIKE ?', ["%{$numeric}%"])
                        ->orWhereRaw('CAST(defect_percent AS TEXT) LIKE ?', ["%{$numeric}%"]);
                });
            }
        }

        // Sanadan-sanagacha filter
        $errors = $this->dateFilter->applyDateToFilters($query, $filters);
        Session::flash('date_format_errors', $errors);

        // 🔥 Default sort (sort parametri yo‘q bo‘lsa)
        if (!$request->has('sort')) {
            $query->orderByDesc('id');
        }

        return $query;
    }
}
