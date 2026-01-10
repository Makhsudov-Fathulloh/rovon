<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductReturnItemSearch
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

        if (!empty($filters['product_return_id'])) {
            $query->where('product_return_id', $filters['product_return_id']);
        }

        if (!empty($filters['product_variation_id'])) {
            $query->where('product_variation_id', $filters['product_variation_id']);
        }

        if (isset($filters['count']) && $filters['count'] !== '') {
            $filters['count'] = preg_replace('/\D/', '', $filters['count']);
            $query->where('count', (int) $filters['count']);
        }

        if (isset($filters['price']) && $filters['price'] !== '') {
            $filters['price'] = preg_replace('/\D/', '', $filters['price']);
            $query->where('price', (int) $filters['price']);
        }

        if (!empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        if (isset($filters['total_price']) && $filters['total_price'] !== '') {
            $filters['total_price'] = preg_replace('/\D/', '', $filters['total_price']);
            $query->where('total_price', (int) $filters['total_price']);
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
