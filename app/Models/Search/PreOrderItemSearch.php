<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PreOrderItemSearch
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

        if (!empty($filters['pre_order_id'])) {
            $query->where('pre_order_id', $filters['pre_order_id']);
        }

        if (!empty($filters['product_variation_id'])) {
            $query->where('product_variation_id', $filters['product_variation_id']);
        }

        if (!empty($filters['code'])) {
            $query->whereRaw('LOWER(code) LIKE ?', ['%' . strtolower($filters['code']) . '%']);
        }

        if (!empty($filters['product_variation_title'])) {
            $query->whereHas('productVariation', function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['product_variation_title'] . '%');
            });
        }

        if (isset($filters['count']) && $filters['count'] !== '') {
            $filters['count'] = preg_replace('/\D/', '', $filters['count']);
            $query->where('count', (int) $filters['count']);
        }

        if (!empty($filters['unit'])) {
            $query->where('unit', $filters['unit']);
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
