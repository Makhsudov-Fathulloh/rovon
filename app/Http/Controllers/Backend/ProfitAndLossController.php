<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ProfitAndLoss;
use App\Models\ProductVariation;
use App\Models\Search\ProfitAndLossSearch;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProfitAndLossController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new ProfitAndLossSearch(new DateFilterService());
        $query = $searchModel->applyFilters(ProfitAndLoss::query(), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('profit_and_loss', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        // ProfitAndLoss ichidagi barcha product_variation_id lar
        $productVariationIds = ProfitAndLoss::distinct()->pluck('product_variation_id');
        $productVariations = ProductVariation::whereIn('id', $productVariationIds)
            ->pluck('title', 'id'); // [id => title]

        // ProfitAndLoss ichidagi barcha order_item_id lar
//        $orderItemIds = ProfitAndLoss::distinct()->pluck('order_item_id');
//        $orderItems = OrderItem::query()
//            ->whereIn('order_item.id', $orderItemIds)
//            ->join('product_variation', 'product_variation.id', '=', 'order_item.product_variation_id')
//            ->pluck('product_variation.title', 'order_item.id');

        $data = ProfitAndLoss::calculateTotals($request);

        $profitAndLosses = $query->paginate(20)->withQueryString();

        return view('backend.profit-and-loss.index', compact(
            'profitAndLosses',
            'productVariations',
//            'orderItems',
            'data',
        ));
    }


    public function show(ProfitAndLoss $profitAndLoss)
    {
        return view('backend.profit-and-loss.show', compact('profitAndLoss'));
    }


    public function destroy(ProfitAndLoss $profitAndLoss)
    {
        $profitAndLoss->delete();

        return response()->json([
            'message' => 'Фойда ва зарар ўчирилди!',
            'type' => 'delete',
            'redirect' => route('profit-and-loss.index')
        ]);
    }
}
