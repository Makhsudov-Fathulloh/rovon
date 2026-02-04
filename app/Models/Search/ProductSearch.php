<?php

namespace App\Models\Search;

use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductSearch
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

        if (!empty($filters['organization_warehouse_product'])) {
            $raw = trim($filters['organization_warehouse_product']);
            $parts = array_values(array_filter(
                array_map(fn($v) => mb_strtolower(trim($v)), explode('.', $raw))
            ));

            $query->where(function ($q) use ($parts) {
                // 🔹 1) NUQTASIZ — oddiy qidiruv
                if (count($parts) === 1) {
                    $keyword = $parts[0];

                    $q->whereHas('warehouse.organization', function($oq) use ($keyword) {
                        $oq->whereRaw('LOWER(organization.title) LIKE ?', ["%{$keyword}%"]);
                    })->orWhereHas('warehouse', function($wq) use ($keyword) {
                        $wq->whereRaw('LOWER(warehouse.title) LIKE ?', ["%{$keyword}%"]);
                    })->orWhereRaw('LOWER(product.title) LIKE ?', ["%{$keyword}%"]);
                }

                // 🔹 2) Organization.Warehouse
                elseif (count($parts) === 2) {
                    [$org, $warehouse] = $parts;

                    $q->whereHas('warehouse.organization', function($oq) use ($org) {
                        $oq->whereRaw('LOWER(organization.title) LIKE ?', ["%{$org}%"]);
                    })->whereHas('warehouse', function($wq) use ($warehouse) {
                        $wq->whereRaw('LOWER(warehouse.title) LIKE ?', ["%{$warehouse}%"]);
                    });
                }

                // 🔹 3) Organization.Warehouse.Product
                elseif (count($parts) >= 3) {
                    [$org, $warehouse, $product] = $parts;

                    $q->whereHas('warehouse.organization', function($oq) use ($org) {
                        $oq->whereRaw('LOWER(organization.title) LIKE ?', ["%{$org}%"]);
                    })->whereHas('warehouse', function($wq) use ($warehouse) {
                        $wq->whereRaw('LOWER(warehouse.title) LIKE ?', ["%{$warehouse}%"]);
                    })->whereRaw('LOWER(product.title) LIKE ?', ["%{$product}%"]);
                }
            });
        }

        if (!empty($filters['subtitle'])) {
            $query->whereRaw('LOWER(subtitle) LIKE ?', ['%' . strtolower($filters['subtitle']) . '%']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
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
            $query->orderBy('id');
        }

        return $query;
    }
}
