<?php

namespace App\Models\Search;

use App\Models\ProductLog;
use App\Models\MaterialLog;
use Illuminate\Http\Request;
use App\Services\DateFilterService;
use Illuminate\Support\Facades\Session;

class LogSearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function materialLogSearch(Request $request)
    {
        $filters = $request->get('filters', []);
        $query = MaterialLog::query();

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['code'])) {
            $query->whereHas('rawMaterialVariation', function ($q) use ($filters) {
                $q->whereRaw('LOWER(code) LIKE ?', ['%' . strtolower($filters['code']) . '%']);
            });
        }

        if (!empty($filters['raw_material_variation_id'])) {
            $query->where('raw_material_variation_id', $filters['raw_material_variation_id']);
        }

        if (isset($filters['old_count']) && $filters['old_count'] !== '') {
            $filters['old_count'] = preg_replace('/[^\d.]/', '', $filters['old_count']);
            $query->where('old_count', (float) $filters['old_count']);
        }

        if (isset($filters['added_count']) && $filters['added_count'] !== '') {
            $filters['added_count'] = preg_replace('/[^\d.]/', '', $filters['added_count']);
            $query->where('added_count', (float) $filters['added_count']);
        }

        if (isset($filters['new_count']) && $filters['new_count'] !== '') {
            $filters['count'] = preg_replace('/[^\d.]/', '', $filters['new_count']);
            $query->where('new_count', (float) $filters['new_count']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Sanadan-sanagacha filter
        $errors = $this->dateFilter->applyDateToFilters($query, $filters);
        Session::flash('date_format_errors', $errors);

        // 🔥 Default sort (sort parametri yo‘q bo‘lsa)
        if (!$request->has('sort')) {
            $query->orderBy('id');
        }

        return $query;
    }

    public function productLogSearch(Request $request)
    {
        $filters = $request->get('filters', []);
        $query = ProductLog::query();

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['code'])) {
            $query->whereHas('productVariation', function ($q) use ($filters) {
                $q->whereRaw('LOWER(code) LIKE ?', ['%' . strtolower($filters['code']) . '%']);
            });
        }

        if (!empty($filters['product_variation_id'])) {
            $query->where('product_variation_id', $filters['product_variation_id']);
        }

        if (isset($filters['old_count']) && $filters['old_count'] !== '') {
            $filters['old_count'] = preg_replace('/[^\d.]/', '', $filters['old_count']);
            $query->where('old_count', (float) $filters['old_count']);
        }

        if (isset($filters['added_count']) && $filters['added_count'] !== '') {
            $filters['added_count'] = preg_replace('/[^\d.]/', '', $filters['added_count']);
            $query->where('added_count', (float) $filters['added_count']);
        }

        if (isset($filters['new_count']) && $filters['new_count'] !== '') {
            $filters['count'] = preg_replace('/[^\d.]/', '', $filters['new_count']);
            $query->where('new_count', (float) $filters['new_count']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Sanadan-sanagacha filter
        $errors = $this->dateFilter->applyDateToFilters($query, $filters);
        Session::flash('date_format_errors', $errors);

        // 🔥 Default sort (sort parametri yo‘q bo‘lsa)
        if (!$request->has('sort')) {
            $query->orderBy('id');
        }

        return $query;
    }
}
