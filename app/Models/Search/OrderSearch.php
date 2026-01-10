<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderSearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function applyFilters($query, Request $request)
    {
        $filters = $request->get('filters', []);

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['total_price']) && $filters['total_price'] !== '') {
            $filters['total_price'] = preg_replace('/[^\d.]/', '', $filters['total_price']);
            $query->where('total_price', (float) $filters['total_price']);
        }

        if (isset($filters['total_amount_paid']) && $filters['total_amount_paid'] !== '') {
            $filters['total_amount_paid'] = preg_replace('/[^\d.]/', '', $filters['total_amount_paid']);
            $query->where('total_amount_paid', (float) $filters['total_amount_paid']);
        }

        if (isset($filters['remaining_debt']) && $filters['remaining_debt'] !== '') {
            $filters['remaining_debt'] = preg_replace('/[^\d.]/', '', $filters['remaining_debt']);
            $query->where('remaining_debt', (float) $filters['remaining_debt']);
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
