<?php

namespace App\Models\Search;

use App\Models\Stage;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StageSearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function search(Request $request)
    {
        $filters = $request->get('filters', []);
        $query = Stage::query();

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if ($organizationId = data_get($filters, 'section_id')) {
            $query->whereHas('section.organization', function ($q) use ($organizationId) {
                $q->where('id', $organizationId);
            });
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (!empty($filters['title'])) {
            $query->whereRaw('LOWER(stage.title) LIKE ?', ['%' . strtolower($filters['title']) . '%']);
        }

        if (!empty($filters['description'])) {
            $query->whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($filters['description']) . '%']);
        }

        if (isset($filters['price']) && $filters['price'] !== '') {
            $filters['price'] = preg_replace('/\D/', '', $filters['price']);
            $query->where('price', (int) $filters['price']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
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
                    $query->whereBetween('stage.created_at', [$fromDate, $toDate]);
                }
            } elseif ($from) {
                // faqat o‘sha kun
                $query->whereBetween('stage.created_at', [
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
