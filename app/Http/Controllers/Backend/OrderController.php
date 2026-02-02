<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CashReport;
use App\Models\ExchangeRates;
use App\Models\ExpenseAndIncome;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariation;
use App\Models\ProfitAndLoss;
use App\Models\Role;
use App\Models\Search\OrderSearch;
use App\Models\User;
use App\Models\UserDebt;
use App\Services\DateFilterService;
use App\Helpers\TelegramHelper;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new OrderSearch(new DateFilterService());
        $query = $searchModel->applyFilters(Order::query(), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('order', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $userIds = Order::distinct()->pluck('user_id');
        $users = User::whereIn('id', $userIds)->where('role_id', Role::where('title', 'Client')->value('id'))->pluck('username', 'id');
        $sellers = User::whereIn('id', Order::pluck('seller_id'))->orderBy('username')->pluck('username', 'id');
        $todayReport = CashReport::today()->first();

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $baseQuery = clone $query;

            // Agar filtrda user_id bo‘lsa, uni ExpenseAndIncome uchun ham qo‘llaymiz
            $filters = $request->get('filters', []);

            // --- UZS ---
            $filteredUzsOrders = (clone $baseQuery)->where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year);

            $orderCountUzs = (clone $filteredUzsOrders)->count();
            $orderTotalPriceUzs = (clone $filteredUzsOrders)->sum('total_price');
            $orderAmountPaidUzs = (clone $filteredUzsOrders)->sum('total_amount_paid');
            $orderRemainingDebtUzs = (clone $filteredUzsOrders)->sum('remaining_debt');

            $filteredDebtUzs = ExpenseAndIncome::where('type', ExpenseAndIncome::TYPE_DEBT)->where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year);

            if (!empty($filters['user_id'])) {
                $filteredDebtUzs->where('user_id', $filters['user_id']);
            }
            $totalDebtPaidUzs = $filteredDebtUzs->sum('amount');
            $orderAmountPaidUzs += $totalDebtPaidUzs;
            $orderRemainingDebtUzs -= $totalDebtPaidUzs;

            // --- USD ---
            $filteredUsdOrders = (clone $baseQuery)->where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year);

            $orderCountUsd = (clone $filteredUsdOrders)->count();
            $orderTotalPriceUsd = (clone $filteredUsdOrders)->sum('total_price');
            $orderAmountPaidUsd = (clone $filteredUsdOrders)->sum('total_amount_paid');
            $orderRemainingDebtUsd = (clone $filteredUsdOrders)->sum('remaining_debt');

            $filteredDebtUsd = ExpenseAndIncome::where('type', ExpenseAndIncome::TYPE_DEBT)->where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year);

            if (!empty($filters['user_id'])) {
                $filteredDebtUsd->where('user_id', $filters['user_id']);
            }
            $totalDebtPaidUsd = $filteredDebtUsd->sum('amount');
            $orderAmountPaidUsd += $totalDebtPaidUsd;
            $orderRemainingDebtUsd -= $totalDebtPaidUsd;
        } else {
            $totalDebtPaidUzs = ExpenseAndIncome::where('type', ExpenseAndIncome::TYPE_DEBT)->where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year)->sum('amount');
            $uzsOrders = Order::where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year);
            $orderCountUzs = $uzsOrders->count();
            $orderTotalPriceUzs = $uzsOrders->sum('total_price');
            $orderAmountPaidUzs = $uzsOrders->sum('total_amount_paid') + $totalDebtPaidUzs;
            // $orderRemainingDebtUzs = $uzsOrders->sum('remaining_debt') - $totalDebtPaidUzs;
            $orderRemainingDebtUzs = UserDebt::where('currency', StatusService::CURRENCY_UZS)->sum('amount');

            $totalDebtPaidUsd = ExpenseAndIncome::where('type', ExpenseAndIncome::TYPE_DEBT)->where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year)->sum('amount');
            $usdOrders = Order::where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year);
            $orderCountUsd = $usdOrders->count();
            $orderTotalPriceUsd = $usdOrders->sum('total_price');
            $orderAmountPaidUsd = $usdOrders->sum('total_amount_paid') + $totalDebtPaidUsd;
            // $orderRemainingDebtUsd = $usdOrders->sum('remaining_debt') - $totalDebtPaidUsd;
            $orderRemainingDebtUsd = UserDebt::where('currency', StatusService::CURRENCY_USD)->sum('amount');
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('backend.order.index', compact(
            'orders',
            'users',
            'todayReport',
            'isFiltered',
            'sellers',
            'orderCountUzs',
            'orderCountUsd',
            'orderTotalPriceUzs',
            'orderTotalPriceUsd',
            'orderAmountPaidUzs',
            'orderAmountPaidUsd',
            'orderRemainingDebtUzs',
            'orderRemainingDebtUsd',
        ));
    }

    public function show(Order $order)
    {
        $order->load('orderItems.productVariation.product');

        return view('backend.order.show', compact('order'));
    }

    public function create()
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        $users = User::select('id', 'username', 'phone')->get();

        $variations = ProductVariation::with('product:id,title')
            ->where('count', '>', 0)
            ->get(['id', 'product_id', 'code', 'title', 'price', 'currency', 'count', 'unit']);

        $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');

        // Narxlarni UZS ga konvertatsiya qilish
        $variations->transform(function ($variation) use ($usdRate) {
            if ($variation->currency === StatusService::CURRENCY_USD) {
                $variation->price = $variation->price * $usdRate;
                $variation->currency = StatusService::CURRENCY_UZS;
            }
            return $variation;
        });

        $clientRoleId = Role::where('title', 'Client')->value('id');
        $defaultUserId = User::where('username', 'Стандарт клиент')->value('id') ?? null;

        $currentCurrency = old('currency', StatusService::CURRENCY_UZS);
        $currencyLabel = $currentCurrency == StatusService::CURRENCY_UZS ? 'сўм' : '$';
        $oldItems = [['product_variation_id' => null, 'quantity' => 1, 'price' => '']];
        $order = null;
        $totalPriceValue = null;
        $cashPaidValue = null;
        $cardPaidValue = null;
        $transferPaidValue = null;
        $bankPaidValue = null;
        $totalPaidValue = null;
        $remainingDebtValue = null;

        return view('backend.order.create', compact(
            'users',
            'clientRoleId',
            'variations',
            'defaultUserId',
            'currentCurrency',
            'currencyLabel',
            'oldItems',
            'order',
            'totalPriceValue',
            'cashPaidValue',
            'cardPaidValue',
            'transferPaidValue',
            'bankPaidValue',
            'totalPaidValue',
            'remainingDebtValue',
        ));
    }

    public function store(Request $request)
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        // 🔹 Raqamli maydonlarni tozalaymiz (faqat raqam va nuqtani qoldiramiz)
        $numericFields = ['total_price', 'total_amount_paid', 'cash_paid', 'card_paid', 'transfer_paid', 'bank_paid', 'remaining_debt'];

        foreach ($numericFields as $field) {
            $request->merge([$field => (float)preg_replace('/[^\d.]/', '', $request->$field)]);
        }

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

        // 🔹 Validatsiya
        $validated = $request->validate([
            'user_id' => 'required|exists:user,id',
            'status' => 'required|integer',
            'currency' => 'required|in:' . implode(',', [StatusService::CURRENCY_UZS, StatusService::CURRENCY_USD]),
            'total_amount_paid' => 'nullable|numeric|min:0',
            'cash_paid' => 'nullable|numeric|min:0',
            'card_paid' => 'nullable|numeric|min:0',
            'transfer_paid' => 'nullable|numeric|min:0',
            'bank_paid' => 'nullable|numeric|min:0',
            'items' => 'required|array',
            'items.*.product_variation_id' => 'required|exists:product_variation,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
            'remaining_debt' => 'nullable|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 🔹 Valyuta kursini olish
            $currency = $validated['currency'] == StatusService::CURRENCY_UZS ? 'UZS' : 'USD';
            $exchangeRate = ExchangeRates::where('currency', $currency)->latest('created_at')->value('rate') ?? 1;

            if ($exchangeRate <= 0) {
                throw new \Exception('Валюта курси топилмади ёки нотўғри!');
            }

            $totalPrice = 0;
            $orderItems = [];

            // 🔹 Har bir itemni tekshirib, stokdan ayiramiz
            foreach ($validated['items'] as $item) {
                $variation = ProductVariation::with('product')->findOrFail($item['product_variation_id']);
                $quantity = $item['quantity'];
                $price = $item['price'];

                if ($variation->count < $quantity) {
                    throw new \Exception("Омборда етарли маҳсулот йўқ: " . $variation->product->title . ' - ' . $variation->title);
                }

                $variation->decrementStock($quantity);

                $totalPrice += $price * $quantity;

                $orderItems[] = [
                    'product_variation_id' => $variation->id,
                    'title' => $variation->product->title . ' - ' . $variation->title,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total_price' => round($price * $quantity, 2),
                ];
            }

            // 🔹 Umumiy summani UZS ga o‘tkazamiz
            if ($validated['currency'] == StatusService::CURRENCY_USD) {
                // USD → so‘m
                $totalPriceBase = round($totalPrice * $exchangeRate, 2);
            } else {
                // UZS → UZS
                $totalPriceBase = $totalPrice;
            }

            // 🧾 Buyurtma yaratish
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'status' => $validated['status'],
                'currency' => $validated['currency'],
                'exchange_rate' => $exchangeRate,
                'seller_id' => auth()->id(),
                'total_price' => $totalPrice,           // USD yoki UZS
                'total_price_base' => $totalPriceBase,  // Har doim UZS
                'cash_paid' => $request->cash_paid ?? 0,
                'card_paid' => $request->card_paid ?? 0,
                'transfer_paid' => $request->transfer_paid ?? 0,
                'bank_paid' => $request->bank_paid ?? 0,
                'total_amount_paid' => $request->total_amount_paid ?? 0,
                'remaining_debt' => $totalPrice - ($request->total_amount_paid ?? 0),
            ]);

            // 🔹 Foydalanuvchi qarzi
            if ($order->remaining_debt > 0) {
                UserDebt::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'amount' => $order->remaining_debt,
                    'currency' => $order->currency,
                    'source' => UserDebt::SOURCE_ORDER,
                ]);
            }

            // 🔹 OrderItems va Profit/Loss yozuvlari
            $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');

            if (!$usdRate || $usdRate <= 0) {
                throw new \Exception('USD kurs topilmadi');
            }

            foreach ($orderItems as $item) {

                $item['order_id'] = $order->id;
                $orderItem = OrderItem::create($item);

                $productVariation = ProductVariation::find($item['product_variation_id']);

                /*
                |--------------------------------------------------------------------------
                | ORIGINAL PRICE → UZS
                |--------------------------------------------------------------------------
                */
                $originalPriceUZS = $productVariation->body_price;

                if ($productVariation->currency == StatusService::CURRENCY_USD) {
                    $originalPriceUZS *= $usdRate;
                }

                /*
                |--------------------------------------------------------------------------
                | SOLD PRICE → UZS
                |--------------------------------------------------------------------------
                */
                if ($order->currency == StatusService::CURRENCY_USD) {
                    // Sotish USD da → UZS ga o‘tkazamiz
                    $soldPriceUZS = $item['price'] * $usdRate;
                } else {
                    // Sotish UZS da
                    $soldPriceUZS = $item['price'];
                }

                $difference = $soldPriceUZS - $originalPriceUZS;

                if ($difference > 0) {   // 🟢 Foyda
                    ProfitAndLoss::create([
                        'product_variation_id' => $item['product_variation_id'],
                        'order_item_id'        => $orderItem->id,
                        'original_price'       => $originalPriceUZS,
                        'sold_price'           => $soldPriceUZS,
                        'profit_amount'        => $difference,
                        'loss_amount'          => 0,
                        'count'                => $item['quantity'],
                        'type'                 => ProfitAndLoss::TYPE_PROFIT,
                        'total_amount'         => $difference * $item['quantity'],
                    ]);
                } elseif ($difference < 0) {   // 🔴 Zarar
                    ProfitAndLoss::create([
                        'product_variation_id' => $item['product_variation_id'],
                        'order_item_id'        => $orderItem->id,
                        'original_price'       => $originalPriceUZS,
                        'sold_price'           => $soldPriceUZS,
                        'profit_amount'        => 0,
                        'loss_amount'          => abs($difference),
                        'count'                => $item['quantity'],
                        'type'                 => ProfitAndLoss::TYPE_LOSS,
                        'total_amount'         => abs($difference) * $item['quantity'],
                    ]);
                }
            }

            DB::commit();

            TelegramHelper::sendOrderToClients($order, 'create');

            //            return redirect()->route('order.show', $order)->with('success', 'Буюртма яратилди!');
            return redirect()->route('order.create')->with('success', 'Буюртма яратилди!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Буюртма яратишда хатолик: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Хатолик: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Order $order)
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        $users = User::where('role_id', Role::where('title', 'Client')->value('id'))->get();
        $clientRoleId = Role::where('title', 'Client')->value('id');

        $variations = ProductVariation::with('product:id,title')
            ->where('count', '>', 0)
            ->get(['id', 'product_id', 'code', 'title', 'price', 'currency', 'count', 'unit']);

        $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');

        // Narxlarni UZS ga konvertatsiya qilish
        $variations->transform(function ($variation) use ($usdRate) {
            if ($variation->currency === StatusService::CURRENCY_USD) {
                $variation->price = $variation->price * $usdRate;
                $variation->currency = StatusService::CURRENCY_UZS;
            }
            return $variation;
        });

        $order->load('orderItems.productVariation.product');

        $currentCurrency = old('currency', $order->currency ?? StatusService::CURRENCY_UZS);

        // Ma'lumotlarni formatlash uchun yordamchi funksiya
        $format = function ($value) use ($currentCurrency) {
            $value = $value ?? 0;
            $value = (float)$value;

            return $currentCurrency == StatusService::CURRENCY_USD
                ? number_format(round($value, 3), 3, '.', ' ')
                : number_format(round($value, 0), 0, '', ' ');
        };

        return view('backend.order.update', [
            'order' => $order,
            'users' => $users,
            'clientRoleId' => $clientRoleId,
            'variations' => $variations,
            'currentCurrency' => $currentCurrency,
            'currencyLabel' => $currentCurrency == StatusService::CURRENCY_USD ? '$' : 'сўм',
            // 'oldItems' => old('items', $order->orderItems ?? [['product_variation_id' => '', 'quantity' => 1, 'price' => '']]),
            'oldItems' => old('items', $order->orderItems->toArray() ?? [['product_variation_id' => '', 'quantity' => 1, 'price' => '']]),
            'totalPriceValue' => old('total_price', $format($order->total_price)),
            'totalPaidValue' => old('total_amount_paid', $format($order->total_amount_paid)),
            'remainingDebtValue' => old('remaining_debt', $format($order->remaining_debt)),
            'cashPaidValue' => old('cash_paid', $format($order->cash_paid)),
            'cardPaidValue' => old('card_paid', $format($order->card_paid)),
            'transferPaidValue' => old('transfer_paid', $format($order->transfer_paid)),
            'bankPaidValue' => old('bank_paid', $format($order->bank_paid)),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        // 🔹 Formatlash (raqam bo‘lmagan belgilarni olib tashlaymiz)
        $numericFields = ['total_price', 'total_amount_paid', 'cash_paid', 'card_paid', 'transfer_paid', 'bank_paid', 'remaining_debt'];

        foreach ($numericFields as $field) {
            $request->merge([$field => (float)preg_replace('/[^\d.]/', '', $request->$field)]);
        }

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
            'user_id' => 'required|exists:user,id',
            'status' => 'required|integer',
            'currency' => 'required|in:' . implode(',', [StatusService::CURRENCY_UZS, StatusService::CURRENCY_USD]),
            'total_amount_paid' => 'nullable|numeric|min:0',
            'cash_paid' => 'nullable|numeric|min:0',
            'card_paid' => 'nullable|numeric|min:0',
            'transfer_paid' => 'nullable|numeric|min:0',
            'bank_paid' => 'nullable|numeric|min:0',
            'items' => 'required|array',
            'items.*.product_variation_id' => 'required|exists:product_variation,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
            'remaining_debt' => 'nullable|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 🔹 Valyuta kursini olish
            $currency = $validated['currency'] == StatusService::CURRENCY_UZS ? 'UZS' : 'USD';
            $exchangeRate = ExchangeRates::where('currency', $currency)->latest('created_at')->value('rate') ?? 1;

            if ($exchangeRate <= 0) {
                throw new \Exception('Валюта курси топилмади ёки нотўғри!');
            }

            // Eski itemlarni qayta qo‘yishdan oldin qaytarib olish (stokni tiklash)
            foreach ($order->orderItems as $oldItem) {
                $variation = ProductVariation::find($oldItem->product_variation_id);
                if ($variation) {
                    $variation->incrementStock($oldItem->quantity);
                }
            }

            // Eski itemlar va foyda/zarar yozuvlarini o‘chirish
            $oldItemIds = $order->orderItems->pluck('id');
            $order->orderItems()->delete();
            ProfitAndLoss::whereIn('order_item_id', $oldItemIds)->delete();

            $totalPrice = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $variation = ProductVariation::with('product')->findOrFail($item['product_variation_id']);
                $quantity = $item['quantity'];
                $price = $item['price'];

                if ($variation->count < $quantity) {
                    throw new \Exception("Омборда етарли маҳсулот йўқ: " . $variation->product->title . ' - ' . $variation->title);
                }

                $variation->decrementStock($quantity);

                $totalPrice += $price * $quantity;

                $orderItems[] = [
                    'product_variation_id' => $variation->id,
                    'title' => $variation->product->title . ' - ' . $variation->title,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total_price' => round($price * $quantity, 2),
                ];
            }

            if ($validated['currency'] == StatusService::CURRENCY_USD) {
                $totalPriceBase = round($totalPrice * $exchangeRate, 2); // USD → UZS
            } else {
                $totalPriceBase = $totalPrice; // UZS → UZS
            }

            // 🧾 Buyurtmani yangilash
            $order->update([
                'user_id' => $validated['user_id'],
                'status' => $validated['status'],
                'currency' => $validated['currency'],
                'exchange_rate' => $exchangeRate,
                'seller_id' => auth()->id(),
                'total_price' => $totalPrice,         // USD yoki UZS
                'total_price_base' => $totalPriceBase, // Har doim UZS
                'cash_paid' => $request->cash_paid ?? 0,
                'card_paid' => $request->card_paid ?? 0,
                'transfer_paid' => $request->transfer_paid ?? 0,
                'bank_paid' => $request->bank_paid ?? 0,
                'total_amount_paid' => $request->total_amount_paid ?? 0,
                'remaining_debt' => $totalPrice - $request->total_amount_paid,
            ]);

            // 🔹 Foydalanuvchi qarzi
            // $userDebt = UserDebt::where('order_id', $order->id)->first();

            // if ($userDebt) {
            //     // Eski qarz mavjud bo‘lsa — yangilaymiz
            //     $userDebt->update([
            //         'amount' => $order->remaining_debt,
            //         'currency' => $order->currency,
            //     ]);
            // } else {
            //     // Yangi order uchun qarz yozilmagan bo‘lsa — yaratamiz
            //     if ($order->remaining_debt > 0) {
            //         UserDebt::create([
            //             'user_id' => $order->user_id,
            //             'order_id' => $order->id,
            //             'amount' => $order->remaining_debt,
            //             'currency' => $order->currency,
            //         ]);
            //     }
            // }

            // 🔹 OrderItems va Profit/Loss
            $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');

            if (!$usdRate || $usdRate <= 0) {
                throw new \Exception('USD kurs topilmadi');
            }

            foreach ($orderItems as $item) {

                // 🔹 OrderItem yaratish
                $item['order_id'] = $order->id;
                $orderItem = OrderItem::create($item);

                $productVariation = ProductVariation::findOrFail($item['product_variation_id']);

                /*
                |--------------------------------------------------------------------------
                | ORIGINAL PRICE → UZS
                |--------------------------------------------------------------------------
                */
                $originalPriceUZS = $productVariation->body_price;

                if ($productVariation->currency == StatusService::CURRENCY_USD) {
                    $originalPriceUZS *= $usdRate;
                }

                /*
                |--------------------------------------------------------------------------
                | SOLD PRICE → UZS
                |--------------------------------------------------------------------------
                */
                $soldPriceUZS = $order->currency == StatusService::CURRENCY_USD
                    ? $item['price'] * $usdRate
                    : $item['price'];

                $difference = $soldPriceUZS - $originalPriceUZS;

                if ($difference > 0) {   // 🟢 Foyda
                    ProfitAndLoss::create([
                        'product_variation_id' => $item['product_variation_id'],
                        'order_item_id'        => $orderItem->id,
                        'original_price'       => $originalPriceUZS,
                        'sold_price'           => $soldPriceUZS,
                        'profit_amount'        => $difference,
                        'loss_amount'          => 0,
                        'count'                => $item['quantity'],
                        'type'                 => ProfitAndLoss::TYPE_PROFIT,
                        'total_amount'         => $difference * $item['quantity'],
                    ]);
                } elseif ($difference < 0) {   // 🔴 Zarar
                    ProfitAndLoss::create([
                        'product_variation_id' => $item['product_variation_id'],
                        'order_item_id'        => $orderItem->id,
                        'original_price'       => $originalPriceUZS,
                        'sold_price'           => $soldPriceUZS,
                        'profit_amount'        => 0,
                        'loss_amount'          => abs($difference),
                        'count'                => $item['quantity'],
                        'type'                 => ProfitAndLoss::TYPE_LOSS,
                        'total_amount'         => abs($difference) * $item['quantity'],
                    ]);
                }
            }

            DB::commit();

            TelegramHelper::sendOrderToClients($order, 'update');

            //            return redirect()->route('order.show', $order)->with('success', 'Буюртма янгиланди!');
            return redirect()->route('order.index')->with('success', 'Буюртма янгиланди!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Буюртма янгилашда хатолик: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Хатолик: ' . $e->getMessage())->withInput();
        }
    }


    public function destroy(Order $order)
    {
        DB::beginTransaction();

        try {
            // 1. Buyurtma mahsulotlarini yuklab olish va omborga qaytarish
            $order->load('orderItems.productVariation');
            foreach ($order->orderItems as $item) {
                $item->productVariation->incrementStock($item->quantity);
            }

            // 2. Buyurtma va uning elementlarini o'chirish
            OrderItem::where('order_id', $order->id)->delete();
            $order->delete();

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Буюртма ўчирилди ва қарз қайta ҳисобланди!',
                    'redirect' => route('order.index')
                ]);
            }

            return redirect()->route('order.index')->with('success', 'Буюртма ўчирилди!');
        } catch (\Throwable $e) {
            DB::rollBack();
            // Log::error($e->getMessage()); // Xatolikni logga yozish tavsiya etiladi

            if (request()->expectsJson()) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Xatolik: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }
}
