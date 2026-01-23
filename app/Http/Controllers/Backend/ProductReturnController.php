<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\CashReport;
use App\Models\ExchangeRates;
use App\Models\ExpenseAndIncome;
use App\Models\ProductReturn;
use App\Models\ProductVariation;
use App\Models\Search\ProductReturnSearch;
use App\Models\User;
use App\Models\UserDebt;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductReturnController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new ProductReturnSearch(new \App\Services\DateFilterService());
        $query = $searchModel->applyFilters(ProductReturn::query()->with('items.variation'), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            if (Schema::hasColumn('product_return', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $expenses = ExpenseAndIncome::whereIn('id', ProductReturn::distinct()->pluck('expense_id'))->pluck('title', 'id');
        $users = User::whereIn('id', ProductReturn::distinct()->pluck('user_id'))->pluck('username', 'id');
        $todayReport = CashReport::today()->first();

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $productReturnCount = $query->count();
        } else {
            $productReturnCount = ProductReturn::count();
        }

        $returns = $query->paginate(20)->withQueryString();

        return view('backend.product-return.index', compact(
            'returns',
            'expenses',
            'users',
            'todayReport',
            'isFiltered',
            'productReturnCount'
        ));
    }

    public function show(ProductReturn $productReturn)
    {
        return view('backend.product-return.show', compact('productReturn'));
    }

    public function create()
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

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

        return view('backend.product-return.create', compact('variations'));
    }

    public function store(Request $request)
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_variation_id' => 'required|exists:product_variation,id',
            'items.*.count' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
            'user_id' => 'nullable|exists:user,id', // Foydalanuvchi tanlanishi mumkin
            'currency' => 'nullable|integer',
        ]);

        // Dollardan so'mga o'tkatish kursini olish (masalan, 1-id dagi USD kursi)
        $usdRate = ExchangeRates::where('currency', 'USD')->value('rate') ?? 1;

        DB::transaction(function () use ($data, $usdRate, $request) {
            $grandTotalUzs = 0;

            // 1. Dastlabki hisob-kitob (Hamma narsani UZS ga keltirish)
            foreach ($data['items'] as $item) {
                $variation = ProductVariation::findOrFail($item['product_variation_id']);

                $itemPrice = $item['price'];
                $itemCount = $item['count'];

                // Agar variatsiya USD bo'lsa, narxni kursga ko'paytiramiz
                if ($variation->currency == StatusService::CURRENCY_USD) {
                    // $totalPriceUzs = ($itemPrice * $itemCount) * $usdRate;
                    $totalPriceUzs = $itemPrice * $itemCount;
                } else {
                    $totalPriceUzs = $itemPrice * $itemCount;
                }

                $grandTotalUzs += $totalPriceUzs;
            }

            // 2. Bitta Expense yaratish (Faqat UZS da)
            $title = "Маҳсулотлар қайтими #" . (ExpenseAndIncome::max('id') + 1);

            if ($request->filled('user_id')) {
                $title .= ', қарздорликдан олибташланди!';

                $expense = ExpenseAndIncome::create([
                    'title' => $title,
                    'amount' => $grandTotalUzs,
                    'currency' => StatusService::CURRENCY_UZS,
                    'type' => ExpenseAndIncome::TYPE_RETURN,
                    'type_payment' => ExpenseAndIncome::TYPE_PAYMENT_DEBT_RETURN,
                    'user_id' => $request->user_id,
                    ]);
            } else {
                $expense = ExpenseAndIncome::create([
                    'title' => $title,
                    'amount' => $grandTotalUzs,
                    'currency' => StatusService::CURRENCY_UZS,
                    'type' => ExpenseAndIncome::TYPE_EXPENSE,
                    'type_payment' => ExpenseAndIncome::TYPE_PAYMENT_CASH,
                ]);
            }

            // 3. ProductReturn yaratish
            $productReturn = ProductReturn::create([
                'expense_id' => $expense->id,
                'title' => "Қайтим " . now()->format('d.m.Y H:i'),
                'total_amount' => $grandTotalUzs,
                'currency' => StatusService::CURRENCY_UZS,
                'rate' => $usdRate, // O'sha vaqtdagi kursni saqlab qo'yamiz
                'user_id' => auth()->id(),
            ]);

            // 4. Itemlarni saqlash va stockni yangilash
            foreach ($data['items'] as $item) {
                $variation = ProductVariation::lockForUpdate()->find($item['product_variation_id']);

                $totalPrice = $item['price'] * $item['count'];

                $productReturn->items()->create([
                    'product_variation_id' => $variation->id,
                    'count' => $item['count'],
                    'price' => $item['price'], // Kirish narxi (USD bo'lsa USD, UZS bo'lsa UZS)
                    'total_price' => $totalPrice,
                ]);

                // Omborga qaytarish
                $variation->count += $item['count'];
                $variation->save();
                // $variation->increment('count', $item['count']);
            }

            // 5. Agar user_id tanlangan bo'lsa, qarzni kamaytirish
            if ($request->filled('user_id') && $request->filled('currency')) {
                $user = User::with(['userDebt' => fn($q) => $q->where('currency', $request->currency)])
                    ->find($request->user_id);

                // Agar USD bo'lsa, kursni olamiz
                $rate = $request->currency == StatusService::CURRENCY_USD ? $usdRate : 1;

                // GrandTotalni tanlangan currency ga o'tkazish
                $amountForDebt = $grandTotalUzs;
                if ($request->currency == StatusService::CURRENCY_USD) {
                    $amountForDebt = $grandTotalUzs / $rate; // UZS -> USD
                }

                $totalDebt = $user->userDebt->sum('amount');

                // Qarzdorlik yetarli emas bo'lsa to'xtatish
                if ($totalDebt < $amountForDebt) {
                    throw ValidationException::withMessages([
                        'user_id' => "Қарздорлик миқдори йетарли емас. Максимал суммаси: " . PriceHelper::format($totalDebt, $request->currency, false)
                    ]);
                }

                $remaining = $amountForDebt;

                foreach ($user->userDebt as $debt) {
                    if ($remaining <= 0) break;

                    if ($debt->amount >= $remaining) {
                        $debt->amount -= $remaining;
                        $remaining = 0;
                    } else {
                        $remaining -= $debt->amount;
                        $debt->amount = 0;
                    }
                    $debt->save();
                }
            }
        });

        return redirect()->route('product-return.index')->with('success', 'Махсулот қайтарилди!');
    }


    public function edit(ProductReturn $productReturn)
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        $productReturn->load('items.variation.product');

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

        // Agar user_id va currency old value ko‘rsatilishini xohlasak
        $oldUserId = $productReturn->expense->user_id ?? null;
        $oldCurrency = $productReturn->currency ?? StatusService::CURRENCY_UZS;

        return view('backend.product-return.update', compact('productReturn', 'variations', 'oldUserId', 'oldCurrency'));
    }

    public function update(Request $request, ProductReturn $productReturn)
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_variation_id' => 'required|exists:product_variation,id',
            'items.*.count' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
            'user_id' => 'nullable|exists:user,id',
            'currency' => 'nullable|integer',
        ]);

        $usdRate = ExchangeRates::where('currency', 'USD')->value('rate') ?? 1;

        DB::transaction(function () use ($data, $productReturn, $usdRate, $request) {

            // 1. Avval eski itemlar bo'yicha ombor qoldig'ini kamaytiramiz
            foreach ($productReturn->items as $oldItem) {
                $oldVariation = ProductVariation::lockForUpdate()->find($oldItem->product_variation_id);
                if ($oldVariation) {
                    $oldVariation->count -= $oldItem->count;
                    $oldVariation->save();
                }
            }

            // 2. Eski itemlarni o'chiramiz
            $productReturn->items()->delete();

            $grandTotalUzs = 0;

            // 3. Yangi itemlar va stockni yangilash
            foreach ($data['items'] as $item) {
                $variation = ProductVariation::lockForUpdate()->findOrFail($item['product_variation_id']);

                $itemPrice = $item['price'];
                $itemCount = $item['count'];
                $totalPrice = $itemPrice * $itemCount;

                // USD bo'lsa, kursga o'tkazish (grandTotalUzs uchun)
                if ($variation->currency == StatusService::CURRENCY_USD) {
                    $grandTotalUzs += $totalPrice;
                } else {
                    $grandTotalUzs += $totalPrice;
                }

                $productReturn->items()->create([
                    'product_variation_id' => $variation->id,
                    'count' => $itemCount,
                    'price' => $itemPrice,
                    'total_price' => $totalPrice,
                ]);

                $variation->count += $itemCount;
                $variation->save();
            }

            // 4. Qarzdorlik tekshiruvi va kamaytirish
            if ($request->filled('user_id') && $request->filled('currency')) {
                $user = User::with(['userDebt' => fn($q) => $q->where('currency', $request->currency)])
                    ->find($request->user_id);

                $rate = $request->currency == StatusService::CURRENCY_USD ? $usdRate : 1;

                $amountForDebt = $grandTotalUzs;
                if ($request->currency == StatusService::CURRENCY_USD) {
                    $amountForDebt = $grandTotalUzs / $rate;
                }

                $totalDebt = $user->userDebt->sum('amount');

                if ($totalDebt < $amountForDebt) {
                    throw ValidationException::withMessages([
                        'user_id' => "Қарздорлик миқдори йетарли емас. Максимал суммаси: " .
                            PriceHelper::format($totalDebt, $request->currency, false)
                    ]);
                }

                $remaining = $amountForDebt;
                foreach ($user->userDebt as $debt) {
                    if ($remaining <= 0) break;

                    if ($debt->amount >= $remaining) {
                        $debt->amount -= $remaining;
                        $remaining = 0;
                    } else {
                        $remaining -= $debt->amount;
                        $debt->amount = 0;
                    }
                    $debt->save();
                }
            }

            // 5. Master ProductReturn yangilash
            $title = "Қайтим " . now()->format('d.m.Y H:i');
            if ($request->filled('user_id')) {
                $title .= ', қарздорликдан олибташланди!';
            }

            $productReturn->update([
                'total_amount' => $grandTotalUzs,
                'rate' => $usdRate,
                'title' => $title,
                'user_id' => auth()->id(),
            ]);

            // 6. Bog'langan Expense update
            if ($productReturn->expense) {
                $expenseTitle = "Маҳсулотлар қайтими #" . $productReturn->expense->id;
                if ($request->filled('user_id')) {
                    $expenseTitle .= ', қарздорликдан олибташланди!';
                }

                $productReturn->expense->update([
                    'amount' => $grandTotalUzs,
                    'title' => $expenseTitle,
                ]);
            }
        });

        return redirect()->route('product-return.index')->with('success', 'Қайтим муваффақиятли янгиланди!');
    }


    public function destroy(ProductReturn $productReturn)
    {
        DB::transaction(function () use ($productReturn) {

            // 1. Stockni orqaga qaytarish
            foreach ($productReturn->items as $item) {
                $variation = $item->variation;
                // Ombordagi countni kamaytirish
                $variation->count -= $item->count;
                $variation->save();
                // $variation->decrement('count', $item->count);
            }

            $expense = $productReturn->expense;

            // 2. Agar qarzdan chegirilgan bo'lsa, qarzni qayta tiklash
            if ($expense && $expense->type_payment == ExpenseAndIncome::TYPE_PAYMENT_DEBT_RETURN) {

                $customerId = $expense->user_id;

                // Summani hisoblash
                $restoreAmount = $productReturn->total_amount;
                if ($productReturn->currency == StatusService::CURRENCY_USD && $productReturn->rate > 0) {
                    $restoreAmount = $productReturn->total_amount / $productReturn->rate;
                }

                // Yangi qarz yozuvi yaratish (Bu sum('amount') qilganda qarzni ko'paytiradi)
                UserDebt::create([
                    'user_id'  => $customerId,
                    'amount'   => $restoreAmount,
                    'currency' => $productReturn->currency ?? StatusService::CURRENCY_UZS,
                    'source'   => UserDebt::SOURCE_MANUAL, // Manual source deb belgilaymiz
                    'order_id' => null
                ]);
            }

            $productReturn->expense?->delete();
            $productReturn->delete();
        });

        return response()->json([
            'message' => 'Қайтим ўчирилди',
            'type' => 'delete',
            'redirect' => route('product-return.index')
        ]);
    }
}
