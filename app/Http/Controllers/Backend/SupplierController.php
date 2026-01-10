<?php

namespace App\Http\Controllers\Backend;

use App\Models\Supplier;
use App\Models\SupplierItem;
use Illuminate\Http\Request;
use App\Models\ExchangeRates;
use App\Services\StatusService;
use App\Models\ExpenseAndIncome;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->search($request->search)
            ->with('items:id,supplier_id,type,amount,rate') // rate ham kerak
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        $suppliers->getCollection()->transform(function ($supplier) {
            $total_uzs_balance = 0;

            foreach ($supplier->items as $item) {
                // Miqdorni kursga ko'paytirib so'mga aylantiramiz
                $amount_in_uzs = $item->amount * $item->rate;

                if ($item->type == 1) {
                    // Yuk kelsa qarz ko'payadi
                    $total_uzs_balance += $amount_in_uzs;
                } else {
                    // To'lov qilinsa qarz kamayadi
                    $total_uzs_balance -= $amount_in_uzs;
                }
            }

            $supplier->calculated_balance = $total_uzs_balance;
            return $supplier;
        });

        return view('backend.supplier.index', compact('suppliers'));
    }


    public function show(Request $request, Supplier $supplier)
    {
        // 1. Tranzaksiyalar tarixi
        $items = $supplier->items()
            ->filterByDate($request->from_date, $request->to_date)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // 2. Jami qarzni hisoblash (Valyutalar bo'yicha ajratib olish yaxshi amaliyot)
        $balances = $supplier->items()
            ->selectRaw('currency,
            SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_yuk,
            SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_tolov')
            ->groupBy('currency')
            ->get();

        return view('backend.supplier.show', compact('supplier', 'items', 'balances'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'address'  => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:255',
            'amount'   => 'nullable|numeric|min:0', // Formadagi name 'amount'
            'currency' => 'required|integer',
            'rate'     => 'required|numeric|min:1',
            'status'   => 'required|integer',
        ]);

        // 1. Firmani yaratamiz
        $supplier = Supplier::create([
            'title'   => $data['title'],
            'address' => $data['address'],
            'phone'   => $data['phone'],
            'status'  => $data['status'],
        ]);

        // 2. Agar boshlang‘ich balans (amount) bo‘lsa
        if ($request->amount > 0) {
            $supplier->items()->create([
                'type'        => 1, // Boshlang'ich qarz sifatida
                'amount'      => $data['amount'],
                'currency'    => $data['currency'],
                'rate'        => $data['rate'],
                'description' => 'Бошланғич баланс',
            ]);
        }

        return back()->with('success', 'Фирма яратилди!');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'address'  => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:255',
            'status'   => 'required|integer',
        ]);

        $supplier->update($data);

        return back()->with('success', 'Фирма янгиланди!');
    }

    public function storeItem(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'type'        => 'required|in:1,2',
            'currency'    => 'required|in:1,2',
            'amount'      => 'required|numeric|min:0.001',
            'rate'        => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
            'paid_amount' => 'nullable|numeric|min:0'
        ]);

        // 1. Asosiy yukni yoki to'lovni saqlash
        $mainItem = $supplier->items()->create([
            'type'        => $data['type'],
            'currency'    => $data['currency'],
            'amount'      => $data['amount'],
            'rate'        => $data['rate'],
            'description' => $data['description'],
        ]);

        // 2. Agar to'lov bo'lsa (type=2) YOKI yuk bilan birga to'lov qilingan bo'lsa
        if ($data['type'] == SupplierItem::TYPE_PAYMENT || ($data['type'] == 1 && $request->filled('paid_amount') && $request->paid_amount > 0)) {

            $paymentAmount = ($data['type'] == SupplierItem::TYPE_PAYMENT) ? $data['amount'] : $request->paid_amount;
            $currency = $data['currency'];
            $rate = $data['rate'];

            // Valyutani hisoblash (Faqat UZS da saqlash uchun)
            $amountInUzs = $paymentAmount;
            if ($currency == StatusService::CURRENCY_USD) {
                // Agar kurs requestda bo'lsa shuni olamiz, bo'lmasa bazadan
                $usdRate = $rate ?: ExchangeRates::where('currency', 'USD')->value('rate');
                $amountInUzs = $paymentAmount * $usdRate;
            }

            // Xarajatlar jadvaliga yozish
            $expense = ExpenseAndIncome::create([
                'title'        => "Фирмага тўлов #" . $mainItem->id . " | " . $supplier->title,
                'amount'       => $amountInUzs,
                'currency'     => StatusService::CURRENCY_UZS,
                'type'         => ExpenseAndIncome::TYPE_EXPENSE,
                'type_payment' => ExpenseAndIncome::TYPE_PAYMENT_CASH,
                'user_id'      => auth()->id(),
            ]);

            // Agar yuk ichida to'lov qilingan bo'lsa, yangi SupplierItem yaratamiz
            if ($data['type'] == 1) {
                $supplier->items()->create([
                    'type'        => SupplierItem::TYPE_PAYMENT,
                    'currency'    => $currency,
                    'amount'      => $paymentAmount,
                    'rate'        => $rate,
                    'expense_id'  => $expense->id, // Expense ID ni bog'laymiz
                    'description' => "Yuk uchun to'lov: " . ($data['description'] ?? ''),
                ]);
            } else {
                // Agar to'g'ridan-to'g'ri to'lov bo'lsa, yaratilgan mainItem ni yangilaymiz
                $mainItem->update(['expense_id' => $expense->id]);
            }
        }

        return back()->with('success', 'Aмалиёт сақланди!');
    }

    public function updateItem(Request $request, SupplierItem $item)
    {
        $data = $request->validate([
            'type'        => 'required|in:1,2',
            'currency'    => 'required|in:1,2',
            'amount'      => 'required|numeric|min:0.001',
            'rate'        => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500'
        ]);

        // 1. Yangi summani har doim UZS (so'm) ga o'girib olamiz
        // Chunki kassa (ExpenseAndIncome) asosan so'mda yuritiladi deb hisoblaymiz
        $newAmountInUzs = $data['amount'];
        if ($data['currency'] == StatusService::CURRENCY_USD) {
            $newAmountInUzs = $data['amount'] * $data['rate'];
        }

        $currentExpenseId = $item->expense_id;

        // 2. Kassa (Expense) mantiqi
        if ($data['type'] == SupplierItem::TYPE_PAYMENT) {

            $expense = ExpenseAndIncome::find($item->expense_id);

            if ($expense) {
                // MAVJUD BO'LSA - barcha parametrlarini yangilaymiz
                $expense->update([
                    'amount'      => $newAmountInUzs,
                    'user_id'     => auth()->id(),
                    'description' => "Янгиланди (" . ($data['currency'] == StatusService::CURRENCY_UZS ? 'сўм' : '$') . "): " . ($data['description'] ?? ''),
                ]);
            } else {
                // MAVJUD BO'LMASA - yangi yaratamiz
                $newExpense = ExpenseAndIncome::create([
                    'title'        => "Фирмага тўлов янгиланди#" . $item->suplier->id . " | " . $item->suplier->title,
                    'amount'       => $newAmountInUzs,
                    'currency'     => StatusService::CURRENCY_UZS,
                    'type'         => ExpenseAndIncome::TYPE_EXPENSE,
                    'type_payment' => ExpenseAndIncome::TYPE_PAYMENT_CASH,
                    'user_id'      => auth()->id(),
                    'description'  => $data['description']
                ]);
                $currentExpenseId = $newExpense->id;
            }
        } else {
            // Agar "To'lov" (Payment) turidan "Yuk" (Invoice) turiga o'zgarsa, kassadagi chiqimni o'chiramiz
            if ($item->expense_id) {
                ExpenseAndIncome::where('id', $item->expense_id)->delete();
            }
            $currentExpenseId = null;
        }

        // 3. SupplierItem'ni yangilaymiz
        $item->update(array_merge($data, [
            'expense_id' => $currentExpenseId
        ]));

        return back()->with('success', 'Амалиёт ва касса харажати янгиланди!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Фирма ўчирилди');
    }
}
