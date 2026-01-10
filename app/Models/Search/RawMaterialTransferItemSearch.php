<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RawMaterialTransferItemSearch
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

        if (!empty($filters['raw_material_transfer_id'])) {
            $query->where('raw_material_transfer_id', $filters['raw_material_transfer_id']);
        }

        if (!empty($filters['raw_material_variation_id'])) {
            $query->where('raw_material_variation_id', $filters['raw_material_variation_id']);
        }

        if (isset($filters['price']) && $filters['price'] !== '') {
            $filters['price'] = preg_replace('/[^\d.]/', '', $filters['price']);
            $query->where('price', (float) $filters['price']);
        }

        if (isset($filters['count']) && $filters['count'] !== '') {
            $filters['count'] = preg_replace('/[^\d.]/', '', $filters['count']);
            $query->where('count', (float) $filters['count']);
        }

        if (!empty($filters['unit'])) {
            $query->where('unit', $filters['unit']);
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
