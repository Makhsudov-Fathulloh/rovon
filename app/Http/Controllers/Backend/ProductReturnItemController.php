<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductReturnItem;
use App\Services\DateFilterService;
use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Schema;
use App\Models\Search\ProductReturnItemSearch;

class ProductReturnItemController extends Controller
{
    public function list(Request $request, $product_return_id)
    {
        $productReturn = ProductReturn::findOrFail($product_return_id);

        $searchModel = new ProductReturnItemSearch(new DateFilterService());
        $query = ProductReturnItem::query()->where('product_return_id', $product_return_id)->with('productReturn');
        $query = $searchModel->applyFilters($query, $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('product_return_item', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $productVariations = ProductVariation::whereIn('id', ProductReturnItem::distinct()->pluck('product_variation_id'))->pluck('title', 'id');

        $isFiltered = $request->has('filters') && count($request->input('filters', [])) > 0;

        if ($isFiltered) {
            $filteredQuery = clone $query;

            $allCount = (clone $filteredQuery)->whereYear('created_at', now()->year)->count();
            $totalAmount = (clone $filteredQuery)->whereYear('created_at', now()->year)->sum('total_amount');
        } else {
            $allCount = ProductReturnItem::where('product_return_id', $product_return_id)->whereYear('created_at', now()->year)->count();
            $totalAmount = ProductReturnItem::where('product_return_id', $product_return_id)->whereYear('created_at', now()->year)->sum('total_price');
        }

        $productReturnItems = $query->paginate(20)->withQueryString();

        return view('backend.product-return-item.list', compact(
            'productReturn',
            'productReturnItems',
            'productVariations',
            'isFiltered',
            'allCount',
            'totalAmount'
        ));
    }


    public function show(ProductReturnItem $productReturnItem)
    {
        return view('backend.product-return-item.show', compact('productReturnItem'));
    }
}
