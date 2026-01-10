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

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (!empty($filters['shift_id'])) {
            $query->where('shift_id', $filters['shift_id']);
        }

        if (!empty($filters['stage_id'])) {
            $query->where('stage_id', $filters['stage_id']);
        }

        if (!empty($filters['stage_count'])) {
            $query->where('stage_count', $filters['stage_count']);
        }

        if (isset($filters['defect_amount']) && $filters['defect_amount'] !== '') {
            $filters['defect_amount'] = preg_replace('/[^\d.]/', '', $filters['defect_amount']);
            $query->where('defect_amount', (float) $filters['defect_amount']);
        }

        if (isset($filters['defect_percent']) && $filters['defect_percent'] !== '') {
            $filters['defect_percent'] = preg_replace('/[^\d.]/', '', $filters['defect_percent']);
            $query->where('defect_percent', (float) $filters['defect_percent']);
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
