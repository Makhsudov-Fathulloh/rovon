<?php

namespace App\Models\Search;

use App\Models\ShiftOutput;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ShiftOutputSearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function search(Request $request)
    {
        $filters = $request->get('filters', []);
        $query = ShiftOutput::query();

        if (!empty($filters['id'])) {
            $query->where('shift_output.id', $filters['id']);
        }

        if ($organizationId = data_get($filters, 'organization_id')) {
            $query->whereHas('shift.section.organization', function ($q) use ($organizationId) {
                $q->where('id', $organizationId);
            });
        }

        if ($sectionId = data_get($filters, 'section_id')) {
            $query->whereHas('shift.section', function ($q) use ($sectionId) {
                $q->where('id', $sectionId);
            });
        }

        if (!empty($filters['shift_id'])) {
            $query->where('shift_id', $filters['shift_id']);
        }

        if (!empty($filters['stage_id'])) {
            $query->where('stage_id', $filters['stage_id']);
        }

        if (isset($filters['stage_count']) && $filters['stage_count'] !== '') {
            $filters['stage_count'] = preg_replace('/\D/', '', $filters['stage_count']);
            $query->where('stage_count', (int) $filters['stage_count']);
        }

        if (isset($filters['defect_amount']) && $filters['defect_amount'] !== '') {
            $filters['defect_amount'] = preg_replace('/[^\d.]/', '', $filters['defect_amount']);
            $query->where('defect_amount', (float) $filters['defect_amount']);
        }

        // Sanadan-sanagacha filter
        $from = $filters['created_from'] ?? null;
        $to   = $filters['created_to'] ?? null;

        try {
            if ($from && $to) {
                $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
                $toDate   = \Carbon\Carbon::parse($to)->endOfDay();

                if ($toDate->lt($fromDate)) {
                    Session::flash('date_format_errors', ['Охирги сана бошланғич санадан олдин бўлиши мумкин эмас.']);
                } else {
                    $query->whereBetween('shift_output.created_at', [$fromDate, $toDate]);
                }
            } elseif ($from) {
                // faqat o‘sha kun
                $query->whereBetween('shift_output.created_at', [
                    \Carbon\Carbon::parse($from)->startOfDay(),
                    \Carbon\Carbon::parse($from)->endOfDay(),
                ]);
            }
        } catch (\Exception $e) {
            Session::flash('date_format_errors', ['Сана формати нотўғри.']);
        }

        // 🔥 Default sort (sort parametri yo‘q bo‘lsa)
        if (!$request->has('sort')) {
            $query->orderByDesc('id');
        }

        return $query;
    }
}
