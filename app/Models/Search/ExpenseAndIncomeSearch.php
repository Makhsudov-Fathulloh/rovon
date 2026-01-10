<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ExpenseAndIncomeSearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function apply($query, Request $request)
    {
        $filters = $request->get('filters', []);

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['title'])) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($filters['title']) . '%']);
        }

        if (isset($filters['type_payment']) && $filters['type_payment'] !== '') {
            $query->where('type_payment', (int) $filters['type_payment']);
        }

        if (isset($filters['amount']) && $filters['amount'] !== '') {
            $filters['amount'] = preg_replace('/[^\d.]/', '', $filters['amount']);
            $query->where('amount', (float) $filters['amount']);
        }

        if (isset($filters['type']) && $filters['type'] !== '') {
            $query->where('type', (int) $filters['type']);
        }

        if (isset($filters['currency']) && $filters['currency'] !== '') {
            $query->where('currency', (int) $filters['currency']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
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
