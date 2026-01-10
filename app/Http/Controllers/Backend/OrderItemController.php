<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRates;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariation;
use App\Models\ProfitAndLoss;
use App\Models\Search\OrderItemSearch;
use App\Models\UserDebt;
use App\Services\DateFilterService;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OrderItemController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new OrderItemSearch(new DateFilterService());
        $query = $searchModel->applyFilters(OrderItem::query(), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('order_item', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $orderIds = OrderItem::distinct()->pluck('order_id');
        $users = Order::with('user')
            ->whereIn('id', $orderIds)
            ->get()
            ->mapWithKeys(function($order) {
                $key = $order->id; // select option uchun value
                $label = $order->user->username . ' (' . $order->created_at->format('d-m-Y') . ')';
                return [$key => $label];
            });

        $productVariationIds = OrderItem::distinct()->pluck('product_variation_id');
        $productVariations = ProductVariation::whereIn('id', $productVariationIds)->pluck('title', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        $currencies = [
            StatusService::CURRENCY_UZS,
            StatusService::CURRENCY_USD
        ];

        $orderItemsQuery = $isFiltered ? $query : OrderItem::query();
        $orderItems = $orderItemsQuery
            ->with('order')
            ->whereYear('created_at', now()->year)
            ->get()
            ->groupBy(fn($item) => $item->order->currency)
            ->sortKeys();

        $orderItemCount = collect($currencies)->mapWithKeys(function ($currency) use ($orderItems) {
            return [$currency => isset($orderItems[$currency]) ? $orderItems[$currency]->count() : 0];
        });

        $orderItemPrice = collect($currencies)->mapWithKeys(function ($currency) use ($orderItems) {
            return [$currency => isset($orderItems[$currency]) ? $orderItems[$currency]->sum('total_price') : 0];
        });

        $orderItems = $query->paginate(20)->withQueryString();

        return view('backend.order-item.index', compact(
            'orderItems',
            'users',
            'productVariations',
            'isFiltered',
            'orderItemCount',
            'orderItemPrice',
        ));
    }


    public function list(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);

        $searchModel = new OrderItemSearch(new DateFilterService());
        $query = OrderItem::query()->where('order_id', $order_id)->with('order');
        $query = $searchModel->applyFilters($query, $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('order_item', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $productVariationIds = OrderItem::where('order_id', $order_id)
            ->distinct()
            ->pluck('product_variation_id');

        $productVariations = ProductVariation::whereIn('id', $productVariationIds)
            ->pluck('title', 'id');


        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $allCount = $query->whereYear('created_at', now()->year)->count();
            $totalPrice = $query->whereYear('created_at', now()->year)->sum('total_price');
        } else {
            $allCount = OrderItem::where('order_id', $order_id)->whereYear('created_at', now()->year)->count();
            $totalPrice = OrderItem::where('order_id', $order_id)->whereYear('created_at', now()->year)->sum('total_price');
        }

        $orderItems = $query->paginate(20)->withQueryString();

        return view('backend.order-item.list', compact(
            'order',
            'orderItems',
            'productVariations',
            'isFiltered',
            'allCount',
            'totalPrice'
        ));
    }


    public function show(OrderItem $orderItem)
    {
        $orderItem->load(['productVariation.product', 'order']);

        return view('backend.order-item.show', compact('orderItem'));
    }


    public function create($order_id)
    {
        $order = Order::findOrFail($order_id);

        $variations = ProductVariation::with('product:id,title')
            ->where('count', '>', 0)
            ->get(['id', 'product_id', 'code', 'title', 'price', 'count', 'unit']);

        $currency = $order->currency == StatusService::CURRENCY_UZS ? 'сўм' : '$';
        $exchangeRate = $order->exchange_rate ?? ExchangeRates::where('currency', 'USD')->value('rate') ?? 1;
        $items = [['product_variation_id' => '', 'quantity' => 1, 'price' => '']];

        return view('backend.order-item.create', compact('order', 'variations', 'currency', 'exchangeRate', 'items'));
    }

    public function store(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);

        // 🔹 Formatlash (raqam bo‘lmagan belgilarni olib tashlaymiz)
        $numericFields = ['total_price', 'total_amount_paid', 'cash_paid', 'card_paid', 'transfer_paid', 'bank_paid', 'remaining_debt'];
        foreach ($numericFields as $field) {
            $request->merge([$field => (float)preg_replace('/[^\d.]/', '', $request->$field)]);
        }

        // items ichidagi price ham shunday tozalanadi:
        if ($request->has('items')) {
            $items = collect($request->items)->map(function ($item) {
                return [
                    'product_variation_id' => $item['product_variation_id'],
                    'quantity' => (float)preg_replace('/[^\d.]/', '', $item['quantity']),
                    'price' => (float)preg_replace('/[^\d.]/', '', $item['price']),
                ];
            })->toArray();
            $request->merge(['items' => $items]);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_variation_id' => 'required|exists:product_variation,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'cash_paid' => 'nullable|numeric|min:0',
            'card_paid' => 'nullable|numeric|min:0',
            'transfer_paid' => 'nullable|numeric|min:0',
            'bank_paid' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $exchangeRate = $order->exchange_rate ?? 1;

            foreach ($validated['items'] as $item) {
                $variation = ProductVariation::with('product')->findOrFail($item['product_variation_id']);
                $quantity = $item['quantity'];
                $price = $item['price'];

                if ($variation->count < $quantity) {
                    throw new \Exception("Омборда етарли маҳсулот йўқ: {$variation->product->title} - {$variation->title}");
                }

                $variation->decrementStock($quantity);

                $totalPriceBase = round($price * $exchangeRate, 2);

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_variation_id' => $variation->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total_price' => round($price * $quantity, 2),
                    'price_base' => $totalPriceBase,
                    'total_price_base' => $totalPriceBase * $quantity,
                ]);

                $totalAmount += $orderItem->total_price;

                $originalPrice = $variation->body_price;
                $difference = $orderItem->total_price_base - $originalPrice * $quantity;

                if ($difference > 0) {
                    ProfitAndLoss::create([
                        'product_variation_id' => $variation->id,
                        'order_item_id' => $orderItem->id,
                        'original_price' => $originalPrice,
                        'sold_price' => $orderItem->price_base,
                        'profit_amount' => $difference / $quantity,
                        'loss_amount' => 0,
                        'count' => $quantity,
                        'type' => ProfitAndLoss::TYPE_PROFIT,
                        'total_amount' => $difference,
                    ]);
                } elseif ($difference < 0) {
                    ProfitAndLoss::create([
                        'product_variation_id' => $variation->id,
                        'order_item_id' => $orderItem->id,
                        'original_price' => $originalPrice,
                        'sold_price' => $orderItem->price_base,
                        'profit_amount' => 0,
                        'loss_amount' => abs($difference) / $quantity,
                        'count' => $quantity,
                        'type' => ProfitAndLoss::TYPE_LOSS,
                        'total_amount' => abs($difference),
                    ]);
                }
            }

            // Order qiymatlarini yangilash
            $order->total_price += $totalAmount;
            $order->cash_paid += $request->cash_paid;
            $order->card_paid += $request->card_paid;
            $order->transfer_paid += $request->transfer_paid;
            $order->bank_paid += $request->bank_paid;

            $order->total_amount_paid = $order->cash_paid + $order->card_paid + $order->transfer_paid + $order->bank_paid;
            $order->remaining_debt = $order->total_price - $order->total_amount_paid;
            $order->save();

            // User qarzi
            if ($order->user) {
                $userDebt = UserDebt::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id' => $order->user_id,
                        'amount' => $order->remaining_debt,
                        'currency' => $order->currency,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('order-item.list', $order->id)->with('success', 'Буюртма элементи қўшилди!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Буюртма элементи яратишда хатолик!: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $item = OrderItem::with('order', 'order.user', 'productVariation')->findOrFail($id);

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
                    $totalDebt = Order::where('user_id', $order->user_id)
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
