<?php

namespace App\Models\Search;

use App\Models\Shift;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ShiftSearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function search(Request $request, $query = null)
    {
        $filters = $request->get('filters', []);

        // Agar query uzatilgan bo'lsa o'shani ishlatamiz, yo'q bo'lsa yangi Shift::query()
        $query = $query ?: Shift::query();

        if (!empty($filters['id'])) {
            $query->where('shift.id', $filters['id']);
        }

        if ($organizationId = data_get($filters, 'organization_id')) {
            $query->whereHas('section.organization', function ($q) use ($organizationId) {
                $q->where('id', $organizationId);
            });
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (!empty($filters['title'])) {
            $query->whereRaw('LOWER(shift.title) LIKE ?', ['%' . strtolower($filters['title']) . '%']);
        }

        if (!empty($filters['user_id'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('user.id', $filters['user_id']);
            });
        }

        if (!empty($filters['description'])) {
            $query->whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($filters['description']) . '%']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
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
                    $query->whereBetween('shift.created_at', [$fromDate, $toDate]);
                }
            } elseif ($from) {
                // faqat o‘sha kun
                $query->whereBetween('shift.created_at', [
                    \Carbon\Carbon::parse($from)->startOfDay(),
                    \Carbon\Carbon::parse($from)->endOfDay(),
                ]);
            }
        } catch (\Exception $e) {
            Session::flash('date_format_errors', ['Сана формати нотўғри.']);
        }

        // 🔥 Default sort (sort parametri yo‘q bo‘lsa)
        if (!$request->has('sort')) {
            $query->orderBy('id');
        }

        return $query;
    }
}
