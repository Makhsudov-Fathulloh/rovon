<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\CashReport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ExchangeRates;
use App\Models\ProductReturn;
use App\Services\StatusService;
use App\Models\ExpenseAndIncome;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Models\Search\ProductReturnSearch;

class ProductReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        ]);

        // Dollardan so'mga o'tkatish kursini olish (masalan, 1-id dagi USD kursi)
        $usdRate = \App\Models\ExchangeRates::where('currency', 'USD')->value('rate') ?? 1;

        DB::transaction(function () use ($data, $usdRate) {
            $grandTotalUzs = 0;

            // 1. Dastlabki hisob-kitob (Hamma narsani UZS ga keltirish)
            foreach ($data['items'] as $item) {
                $variation = ProductVariation::findOrFail($item['product_variation_id']);

                $itemPrice = $item['price'];
                $itemCount = $item['count'];

                // Agar variatsiya USD bo'lsa, narxni kursga ko'paytiramiz
                if ($variation->currency == \App\Services\StatusService::CURRENCY_USD) {
                    // $totalPriceUzs = ($itemPrice * $itemCount) * $usdRate;
                    $totalPriceUzs = $itemPrice * $itemCount;
                } else {
                    $totalPriceUzs = $itemPrice * $itemCount;
                }

                $grandTotalUzs += $totalPriceUzs;
            }

            // 2. Bitta Expense yaratish (Faqat UZS da)
            $expense = ExpenseAndIncome::create([
                'title'        => "Mahsulotlar qaytimi #" . (ExpenseAndIncome::max('id') + 1),
                'amount'       => $grandTotalUzs,
                'currency'     => \App\Services\StatusService::CURRENCY_UZS,
                'type'         => ExpenseAndIncome::TYPE_EXPENSE,
                'type_payment' => ExpenseAndIncome::TYPE_PAYMENT_CASH,
                'user_id'      => auth()->id(),
            ]);

            // 3. Master ProductReturn yaratish
            $productReturn = ProductReturn::create([
                'expense_id'   => $expense->id,
                'title'        => "Qaytim " . now()->format('d.m.Y H:i'),
                'total_amount' => $grandTotalUzs,
                'currency'     => \App\Services\StatusService::CURRENCY_UZS,
                'rate'         => $usdRate, // O'sha vaqtdagi kursni saqlab qo'yamiz
                'user_id'      => auth()->id(),
            ]);

            // 4. Itemlarni saqlash va stockni yangilash
            foreach ($data['items'] as $item) {
                $variation = ProductVariation::lockForUpdate()->find($item['product_variation_id']);

                $totalPrice = $item['price'] * $item['count'];

                $productReturn->items()->create([
                    'product_variation_id' => $variation->id,
                    'count'                => $item['count'],
                    'price'                => $item['price'], // Kirish narxi (USD bo'lsa USD, UZS bo'lsa UZS)
                    'total_price'             => $totalPrice,
                ]);

                // Omborga qaytarish
                $variation->count += $item['count'];
                $variation->save();
                // $variation->increment('count', $item['count']);
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
        
        return view('backend.product-return.update', compact('productReturn', 'variations'));
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
        ]);

        $usdRate = \App\Models\ExchangeRates::where('currency', 'USD')->value('rate') ?? 1;

        DB::transaction(function () use ($data, $productReturn, $usdRate) {

            // 1. Avval eski itemlar bo'yicha ombor qoldig'ini qaytaramiz
            // Chunki qaytim bo'lganda stock ko'paygan edi, endi o'sha ko'paygan qismini ayiramiz
            foreach ($productReturn->items as $oldItem) {
                $oldVariation = ProductVariation::lockForUpdate()->find($oldItem->product_variation_id);
                if ($oldVariation) {
                    $oldVariation->count -= $oldItem->count;
                    $oldVariation->save();
                }
            }

            // 2. Eski itemlarni o'chirib tashlaymiz
            $productReturn->items()->delete();

            $grandTotalUzs = 0;

            // 3. Yangi ma'lumotlar asosida itemlarni yaratamiz va stockni yangilaymiz
            foreach ($data['items'] as $item) {
                $variation = ProductVariation::lockForUpdate()->findOrFail($item['product_variation_id']);

                $itemPrice = $item['price'];
                $itemCount = $item['count'];
                $totalPrice = $itemPrice * $itemCount;

                // Kurs bo'yicha UZS ga hisoblash
                if ($variation->currency == \App\Services\StatusService::CURRENCY_USD) {
                    // $grandTotalUzs += ($totalPrice * $usdRate);
                    $grandTotalUzs += $totalPrice;
                } else {
                    $grandTotalUzs += $totalPrice;
                }

                // Yangi item yaratish
                $productReturn->items()->create([
                    'product_variation_id' => $variation->id,
                    'count'                => $itemCount,
                    'price'                => $itemPrice,
                    'total_price'          => $totalPrice,
                ]);

                // Ombor qoldig'ini oshirish
                $variation->count += $itemCount;
                $variation->save();
            }

            // 4. Master ProductReturn modelini yangilash
            $productReturn->update([
                'total_amount' => $grandTotalUzs,
                'rate'         => $usdRate,
                'title'        => $request->title ?? $productReturn->title,
            ]);

            // 5. Bog'langan Expense (Xarajat) ma'lumotlarini yangilash
            if ($productReturn->expense) {
                $productReturn->expense->update([
                    'amount' => $grandTotalUzs,
                    // Agar xarajat nomi ham o'zgarishi kerak bo'lsa:
                    // 'title' => "Mahsulotlar qaytimi #" . $productReturn->expense->id
                ]);
            }
        });

        return redirect()->route('product-return.index')->with('success', 'Қайтим муваффақиятли янгиланди!');
    }


    public function destroy(ProductReturn $productReturn)
    {
        DB::transaction(function () use ($productReturn) {

            foreach ($productReturn->items as $item) {
                $variation = $item->variation;
                // Ombordagi countni kamaytirish
                $variation->count -= $item->count;
                $variation->save();
                // $variation->decrement('count', $item->count);
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
