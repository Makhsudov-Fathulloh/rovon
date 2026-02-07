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

        if (isset($filters['price_count_total_price']) && $filters['price_count_total_price'] !== '') {
            $numeric = preg_replace('/[^\d.]/', '', $filters['price_count_total_price']);

            if ($numeric !== '') {
                $query->where(function ($q) use ($numeric) {
                    $q->whereRaw('CAST(count AS TEXT) LIKE ?', ["%{$numeric}%"])
                        ->orWhereRaw('CAST(price AS TEXT) LIKE ?', ["%{$numeric}%"])
                        ->orWhereRaw('CAST(total_price AS TEXT) LIKE ?', ["%{$numeric}%"]);
                });
            }
        }

        if (!empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
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
