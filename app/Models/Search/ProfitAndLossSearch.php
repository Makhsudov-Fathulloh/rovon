<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProfitAndLossSearch
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

        if (!empty($filters['product_variation_id'])) {
            $query->where('product_variation_id', $filters['product_variation_id']);
        }

        if (!empty($filters['order_item_id'])) {
            $query->where('order_item_id', $filters['order_item_id']);
        }

        if (isset($filters['original_price']) && $filters['original_price'] !== '') {
            $filters['original_price'] = preg_replace('/\D/', '', $filters['original_price']);
            $query->where('original_price', (int) $filters['original_price']);
        }

        if (isset($filters['sold_price']) && $filters['sold_price'] !== '') {
            $filters['sold_price'] = preg_replace('/[^\d.]/', '', $filters['sold_price']);
            $query->where('sold_price', (float) $filters['sold_price']);
        }

        if (isset($filters['profit_amount']) && $filters['profit_amount'] !== '') {
            $filters['profit_amount'] = preg_replace('/[^\d.]/', '', $filters['profit_amount']);
            $query->where('profit_amount', (float) $filters['profit_amount']);
        }

        if (isset($filters['loss_amount']) && $filters['loss_amount'] !== '') {
            $filters['loss_amount'] = preg_replace('/[^\d.]/', '', $filters['loss_amount']);
            $query->where('loss_amount', (float) $filters['loss_amount']);
        }

        if (!empty($filters['count'])) {
            $query->where('count', $filters['count']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['total_amount']) && $filters['total_amount'] !== '') {
            $filters['total_amount'] = preg_replace('/\D/', '', $filters['total_amount']);
            $query->where('total_amount', (int) $filters['total_amount']);
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
