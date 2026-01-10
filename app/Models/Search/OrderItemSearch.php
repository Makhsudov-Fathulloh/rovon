<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderItemSearch
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

        if (!empty($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }

        if (!empty($filters['product_variation_id'])) {
            $query->where('product_variation_id', $filters['product_variation_id']);
        }

        if (!empty($filters['product_variation_title'])) {
            $query->whereHas('productVariation', function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['product_variation_title'] . '%');
            });
        }

        if (isset($filters['price']) && $filters['price'] !== '') {
            $filters['price'] = preg_replace('/[^\d.]/', '', $filters['price']);
            $query->where('price', (float) $filters['price']);
        }

        if (isset($filters['quantity']) && $filters['quantity'] !== '') {
            $filters['quantity'] = preg_replace('/[^\d.]/', '', $filters['quantity']);
            $query->where('quantity', (float) $filters['quantity']);
        }

        if (isset($filters['total_price']) && $filters['total_price'] !== '') {
            $filters['total_price'] = preg_replace('/[^\d.]/', '', $filters['total_price']);
            $query->where('total_price', (float) $filters['total_price']);
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
