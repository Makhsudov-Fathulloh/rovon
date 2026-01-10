<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RawMaterialVariationSearch
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

        if (!empty($filters['raw_material_id'])) {
            $query->where('raw_material_id', $filters['raw_material_id']);
        }

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(code) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(title) LIKE ?', ["%{$search}%"]);
            });
        }

        if (!empty($filters['code'])) {
            $query->whereRaw('LOWER(code) LIKE ?', ['%' . strtolower($filters['code']) . '%']);
        }

        if (!empty($filters['title'])) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($filters['title']) . '%']);
        }

        if (!empty($filters['subtitle'])) {
            $query->whereRaw('LOWER(subtitle) LIKE ?', ['%' . strtolower($filters['subtitle']) . '%']);
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

        if (!empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        if (isset($filters['total_price']) && $filters['total_price'] !== '') {
            $filters['total_price'] = preg_replace('/[^\d.]/', '', $filters['total_price']);
            $query->where('total_price', (float) $filters['total_price']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', 'like', '%' . $filters['type'] . '%');
        }

        if (!empty($filters['slug'])) {
            $query->where('slug', 'like', '%' . $filters['slug'] . '%');
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
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
