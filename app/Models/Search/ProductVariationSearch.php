<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductVariationSearch
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

        if (!empty($filters['product_code_title'])) {
            $keyword = strtolower($filters['product_code_title']);

            $query->where(function($q) use ($keyword) {
                // 1. Standart qidiruv (code va title)
                $q->whereRaw('LOWER(code) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"])
                    ->orWhereHas('product', function($subQuery) use ($keyword) {
                        $subQuery->whereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"]);
                    });

                // 2. Agar qidiruvda nuqta bo'lsa (Masalan: "Meva.Olma")
                if (str_contains($keyword, '.')) {
                    $parts = explode('.', $keyword);
                    $productPart = trim($parts[0]);
                    $titlePart = trim($parts[1]);

                    $q->orWhere(function($subQ) use ($productPart, $titlePart) {
                        $subQ->whereRaw('LOWER(title) LIKE ?', ["%{$titlePart}%"])
                            ->whereHas('product', function($pQ) use ($productPart) {
                                $pQ->whereRaw('LOWER(title) LIKE ?', ["%{$productPart}%"]);
                            });
                    });
                }
            });
        }

        if (!empty($filters['subtitle'])) {
            $query->whereRaw('LOWER(subtitle) LIKE ?', ['%' . strtolower($filters['subtitle']) . '%']);
        }

        if (isset($filters['body_price']) && $filters['body_price'] !== '') {
            $filters['body_price'] = preg_replace('/[^\d.]/', '', $filters['body_price']);
            $query->where('body_price', (int) $filters['body_price']);
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

        if (!empty($filters['unit'])) {
            $query->where('unit', $filters['unit']);
        }

        if (!empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', 'like', '%' . $filters['type'] . '%');
        }

        if (isset($filters['top']) && $filters['top'] !== '') {
            $query->where('top', $filters['top']);
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
