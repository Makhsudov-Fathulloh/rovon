<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\ProductVariation;
use App\Models\Search\PreOrderItemSearch;
use App\Models\UserDebt;
use App\Services\DateFilterService;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PreOrderItemController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new PreOrderItemSearch(new DateFilterService());
        $query = $searchModel->applyFilters(PreOrderItem::query(), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('pre_order_item', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

//        $preOrderIds = PreOrderItem::distinct()->pluck('pre_order_id');
//        $orders = PreOrder::with('user')->whereIn('id', $preOrderIds)->get();
//        $users = $orders->pluck('user.username', 'id');

        $preOrderIds = PreOrderItem::distinct()->pluck('pre_order_id');
        $users = PreOrder::with('user')
            ->whereIn('id', $preOrderIds)
            ->get()
            ->mapWithKeys(function($pre_order) {
                $key = $pre_order->id; // select option uchun value
                $label = $pre_order->user->username . ' - ' . $pre_order->title . '(' . $pre_order->created_at->format('d-m-Y') . ')';
                return [$key => $label];
            });

        $productVariationIds = PreOrderItem::distinct()->pluck('product_variation_id');
        $productVariations = ProductVariation::whereIn('id', $productVariationIds)->pluck('title', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        // 🔹 Queryni tayyorlash va paginate qilish
        $orderItemsQuery = $isFiltered ? $query : PreOrderItem::query();

        $newCount = (clone $orderItemsQuery)
            ->join('pre_order', 'pre_order_item.pre_order_id', '=', 'pre_order.id')
            ->where('pre_order.status', PreOrder::STATUS_NEW)
            ->count();

        $inProgressCount = (clone $orderItemsQuery)
            ->join('pre_order', 'pre_order_item.pre_order_id', '=', 'pre_order.id')
            ->where('pre_order.status', PreOrder::STATUS_INPROGRESS)
            ->count();

        $doneCount = (clone $orderItemsQuery)
            ->join('pre_order', 'pre_order_item.pre_order_id', '=', 'pre_order.id')
            ->where('pre_order.status', PreOrder::STATUS_DONE)
            ->count();

        $canceledCount = (clone $orderItemsQuery)
            ->join('pre_order', 'pre_order_item.pre_order_id', '=', 'pre_order.id')
            ->where('pre_order.status', PreOrder::STATUS_CANCELLED)
            ->count();

        $orderItems = $orderItemsQuery
            ->with(['preOrder.user', 'productVariation'])
            ->whereYear('created_at', now()->year)
            ->paginate(20)
            ->withQueryString();


        return view('backend.pre-order-item.index', compact(
            'orderItems',
            'users',
            'productVariations',
            'isFiltered',
            'newCount',
            'inProgressCount',
            'doneCount',
            'canceledCount',
        ));
    }


    public function list(Request $request, $pre_order_id)
    {
        $order = PreOrder::findOrFail($pre_order_id);

        $searchModel = new PreOrderItemSearch(new DateFilterService());
        $query = PreOrderItem::query()->where('pre_order_id', $pre_order_id)->with('preOrder');
        $query = $searchModel->applyFilters($query, $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('pre_order_item', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $productVariationIds = PreOrderItem::where('pre_order_id', $pre_order_id)->distinct()->pluck('product_variation_id');
        $productVariations = ProductVariation::whereIn('id', $productVariationIds)->pluck('title', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $baseQuery = clone $query;

            $orderCount = (clone $baseQuery)->whereYear('created_at', now()->year)->count();
        } else {
            $orderCount = PreOrderItem::where('pre_order_id', $pre_order_id)->whereYear('created_at', now()->year)->count();
        }

        $orderItems = $query->paginate(20)->withQueryString();

        return view('backend.pre-order-item.list', compact(
            'order',
            'orderItems',
            'productVariations',
            'isFiltered',
            'orderCount',
        ));
    }


    public function show(PreOrderItem $preOrderItem)
    {
        $preOrderItem->load(['productVariation.product', 'preOrder']);

        return view('backend.pre-order-item.show', compact('preOrderItem'));
    }

    public function destroy($id)
    {
        $item = PreOrderItem::with('order', 'order.user', 'productVariation')->findOrFail($id);

        DB::beginTransaction();

        try {
            $order = $item->order;
            $itemTotal = $item->total_price;

            // 🔹 Mahsulotni omborga qaytarish
            if ($item->productVariation) {
                $item->productVariation->incrementStock($item->quantity);
            }

            // 🔹 Order itemni o‘chirish
            $item->delete();

            // 🔹 Buyurtma summasini yangilash
            $order->total_price -= $itemTotal;
            $order->remaining_debt = $order->total_price - $order->total_amount_paid;
            $order->save();

            // 🔹 Foydalanuvchi umumiy qarzini yangilash
            if ($order->user_id) {
                $userDebt = UserDebt::where('user_id', $order->user_id)
                    ->where('currency', $order->currency)
                    ->first();

                if ($userDebt) {
                    $totalDebt = PreOrder::where('user_id', $order->user_id)
                        ->sum('remaining_debt');

                    $userDebt->amount = $totalDebt;
                    $userDebt->save();
                }
            }

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Буюртма элементи ўчирилди!',
                    'redirect' => route('order-item.index')
                ]);
            }

            return redirect()->route('order-item.index', $order->id)
                ->with('success', 'Буюртма элементи ўчирилди!');
        } catch (\Throwable $e) {
            DB::rollBack();

            if (request()->expectsJson()) {
                return response()->json([
                    'type' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }
}
