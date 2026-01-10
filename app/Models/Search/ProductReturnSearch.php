<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductReturnSearch
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

        if (!empty($filters['expense_id'])) {
            $query->where('expense_id', $filters['expense_id']);
        }

        if (!empty($filters['title'])) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($filters['title']) . '%']);
        }

        if (isset($filters['total_amount']) && $filters['total_amount'] !== '') {
            $filters['total_amount'] = preg_replace('/\D/', '', $filters['total_amount']);
            $query->where('total_amount', (int) $filters['total_amount']);
        }

        if (!empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        if (!empty($filters['rate'])) {
            $query->where('rate', $filters['rate']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Sanadan-sanagacha filter
        $errors = $this->dateFilter->applyDateToFilters($query, $filters);
        Session::flash('date_format_errors', $errors);

        // 🔥 Default sort (sort parametri yo‘q bo‘lsa)
        if (!$request->has('sort')) {
            $query->orderByDesc('created_at');
        }

        return $query;
    }
}
