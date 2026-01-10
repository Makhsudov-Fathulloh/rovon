<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\CashReport;
use App\Models\ExpenseAndIncome;
use App\Models\Search\ExpenseAndIncomeSearch;
use App\Models\User;
use App\Models\UserDebt;
use App\Services\DateFilterService;
use App\Services\StatusService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ExpenseAndIncomeController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new ExpenseAndIncomeSearch(new DateFilterService());
        $query = $searchModel->apply(ExpenseAndIncome::query(), $request);

        // Sortlash
        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = Str::startsWith($sort, '-') ? 'desc' : 'asc';
            $sort = ltrim($sort, '-');
            if (Schema::hasColumn('expense_and_income', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $users = User::pluck('username', 'id');
        $todayReport = CashReport::today()->first();

        $isFiltered = count($request->get('filters', [])) > 0;

        // Sana
        if ($isFiltered) {
            $filters = $request->get('filters', []);
            $filterDate = $filters['created_at'] ?? null;
            $date = Carbon::parse($filterDate);
        } else {
            $date = today();
        }

        // Filterlarni olish
        $filters = $request->get('filters', []);
        $from = $filters['created_from'] ?? null;
        $to = $filters['created_to'] ?? null;
        $isFiltered = (!empty($from) || !empty($to));

        // Metodni chaqirish va destructuring orqali o'zgaruvchilarga ajratish
        [
            'expense'   => $expense,
            'income'    => $income,
            'debt'      => $debt,
            'order'     => $order,
            'payment'   => $payment,
            'remaining' => $remaining,
        ] = ExpenseAndIncome::calculateTotals(clone $query, $from, $to);

        $expenseAndIncomes = $query->paginate(20)->withQueryString();

        return view('backend.expense-and-income.index', compact(
            'expenseAndIncomes',
            'isFiltered',
            'users',
            'todayReport',
            'expense',
            'income',
            'debt',
            'order',
            //            'amount',
            'payment',
            'remaining',
        ));
    }

    public function show(ExpenseAndIncome $expenseAndIncome)
    {
        return view('backend.expense-and-income.show', compact('expenseAndIncome'));
    }

    public function getUsersByCurrency(Request $request)
    {
        $currency = (int)$request->get('currency');
        $type = (int)$request->get('type');

        // 🔹 Agar type qarz so‘ndirish (DEBT) bo‘lmasa — bo‘sh option
        if ($type !== ExpenseAndIncome::TYPE_DEBT) {
            return response()->json(['options' => '<option value=""></option>']);
        }

        // 🔹 Har bir user uchun shu valyutadagi umumiy qarzni hisoblaymiz
        $users = User::with(['userDebt' => fn($q) => $q->where('currency', $currency)])->get();

        $options = '<option value=""></option>'; // placeholder

        foreach ($users as $user) {
            // 🔹 Agar bir nechta qarz yozuvlari bo‘lsa, ularning jami summasini olamiz
            $totalDebt = $user->userDebt->sum('amount');

            if ($totalDebt > 0) {
                $options .= '<option value="' . $user->id . '">'
                    . $user->username . ' (Қарздорлик: ' . PriceHelper::format($totalDebt, $currency) . ')</option>';
            }
        }

        return response()->json(['options' => $options]);
    }

    public function create(Request $request)
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        $expenseAndIncome = new ExpenseAndIncome();
        $expenseAndIncome->type = $request->get('type'); // query'dan type ni olish
        $expenseAndIncome->type_payment = ExpenseAndIncome::TYPE_PAYMENT_CASH; // default qilib qo‘yamiz

        return view('backend.expense-and-income.create', compact('expenseAndIncome'));
    }

    public function store(Request $request)
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        $floatFields = ['amount']; // faqat float bo‘lishi kerak bo‘lganlar
        $intFields = ['type_payment', 'type', 'currency']; // integer bo‘lishi kerak bo‘lganlar

        foreach ($floatFields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => (float)preg_replace('/[^\d.]/', '', $request->$field)]);
            }
        }

        foreach ($intFields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => (int)preg_replace('/[^\d]/', '', $request->$field)]);
            }
        }

        $data = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'type_payment' => 'required|in:1,2,3',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:' . implode(',', [ExpenseAndIncome::TYPE_DEBT, ExpenseAndIncome::TYPE_INCOME, ExpenseAndIncome::TYPE_EXPENSE]),
            'user_id' => ['nullable', 'required_if:type,' . ExpenseAndIncome::TYPE_DEBT, 'exists:user,id'],
            'currency' => 'required|in:' . implode(',', [StatusService::CURRENCY_UZS, StatusService::CURRENCY_USD]),
        ], [
            'title.required' => 'Номини киритиш мажбурий.',
            'title.string' => 'Номи матн бўлиши керак.',
            'title.max' => 'Номи 100 та белгидан ошмаслиги керак.',
            'amount.required' => 'Миқдори киритиш мажбурий.',
            'user_id.required_if' => 'Қарздор танлаш мажбурий.',
            'user_id.exists' => 'Танланган қарздор мавжуд эмас.',
        ]);

        try {
            DB::transaction(function () use ($request, &$data) {
                // 🔹 Debt bo‘lsa qarz tekshiramiz
                if ($request->type == ExpenseAndIncome::TYPE_DEBT) {
                    $user = User::with(['userDebt' => fn($q) => $q->where('currency', $data['currency'])])
                        ->find($data['user_id']);

                    // 🔹 Umumiy qarz miqdorini olamiz
                    $totalDebt = $user->userDebt->sum('amount');

                    if ($totalDebt <= 0) {
                        throw new \RuntimeException('Қарздорда танланган валюта бўйича қарз мавжуд эмас.');
                    }

                    if ($data['amount'] > $totalDebt) {
                        throw new \RuntimeException('Киритилган сумма қарздорликдан ортиқ. Қарздорлик: ' . $totalDebt);
                    }

                    // 🔹 Qarzni kamaytirish — bir nechta yozuvlarda ketma-ketlik bilan ayiramiz
                    $totalDebtBefore = $user->userDebt->sum('amount');
                    $remaining = $data['amount'];

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

                    $remainingDebtAfter = $user->userDebt->sum('amount');

                    // 🔹 Telegramga yuboramiz
                    \App\Helpers\TelegramHelper::sendDebtToUser(
                        $user,
                        $totalDebtBefore,
                        $data['amount'],
                        $remainingDebtAfter,
                        $data['currency'],
                        'create'
                    );
                }

                ExpenseAndIncome::create($data);
            });

            switch ($request->type) {
                case ExpenseAndIncome::TYPE_EXPENSE:
                    $message = 'Харажат яратилди!';
                    break;
                case ExpenseAndIncome::TYPE_INCOME:
                    $message = 'Кирим яратилди!';
                    break;
                case ExpenseAndIncome::TYPE_DEBT:
                    $message = 'Қарз сўндирилди!';
                    break;
                default:
                    $message = 'Муваффақиятли яратилди!';
            }

            return redirect()->route('expense-and-income.index')->with('success', $message);
            // } catch (\Throwable $e) {
            //     // Orqaga qaytarmasdan, xatoni ekranga chiqarish
            //     dd($e->getMessage(), $e->getFile(), $e->getLine());
            // }
        } catch (\RuntimeException $e) {
            return back()->withErrors(['user_id' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['general' => 'Кутилмаган хато: ' . $e->getMessage()])->withInput();
        }
    }


    public function edit(ExpenseAndIncome $expenseAndIncome)
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        return view('backend.expense-and-income.update', compact('expenseAndIncome'));
    }

    public function update(Request $request, ExpenseAndIncome $expenseAndIncome)
    {
        if (($check = CashReport::checkCashReport()) !== true) {
            return $check;
        }

        $floatFields = ['amount']; // float bo‘lishi kerak bo‘lganlar
        $intFields = ['type_payment', 'type', 'currency']; // integer bo‘lishi kerak bo‘lganlar

        // 🔹 Raqamli qiymatlarni tozalaymiz
        foreach ($floatFields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => (float)preg_replace('/[^\d.]/', '', $request->$field)]);
            }
        }

        foreach ($intFields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => (int)preg_replace('/[^\d]/', '', $request->$field)]);
            }
        }

        // 🔹 Validatsiya
        $data = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'type_payment' => 'required|in:1,2,3',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:' . implode(',', [ExpenseAndIncome::TYPE_DEBT, ExpenseAndIncome::TYPE_INCOME, ExpenseAndIncome::TYPE_EXPENSE]),
            'user_id' => ['nullable', 'required_if:type,' . ExpenseAndIncome::TYPE_DEBT, 'exists:user,id'],
            'currency' => 'required|in:' . implode(',', [StatusService::CURRENCY_UZS, StatusService::CURRENCY_USD]),
        ], [
            'title.required' => 'Номини киритиш мажбурий.',
            'title.string' => 'Номи матн бўлиши керак.',
            'title.max' => 'Номи 100 та белгидан ошмаслиги керак.',
            'amount.required' => 'Миқдори киритиш мажбурий.',
            'user_id.required_if' => 'Қарздор танлаш мажбурий.',
            'user_id.exists' => 'Танланган қарздор мавжуд эмас.',
        ]);

        try {
            DB::transaction(function () use ($request, &$data, $expenseAndIncome) {
                // 🔹 Eski yozuv qarz to‘lovi bo‘lsa — qarzni qaytarib qo‘yamiz
                if ($expenseAndIncome->type == ExpenseAndIncome::TYPE_DEBT && $expenseAndIncome->user_id) {
                    $oldUser = User::with(['userDebt' => fn($q) => $q->where('currency', $expenseAndIncome->currency)])
                        ->find($expenseAndIncome->user_id);

                    $remaining = $expenseAndIncome->amount;

                    foreach ($oldUser->userDebt as $debt) {
                        $debt->amount += $remaining;
                        $debt->save();
                        break; // faqat bitta yozuvga qo‘shish kifoya (yoki istasangiz oldingi kabi taqsimlash)
                    }
                }

                // 🔹 Yangi ma’lumotni yangilaymiz
                $totalDebtBefore = $oldUser->userDebt->sum('amount') + $data['amount']; // update oldingi holatni hisoblash
                $expenseAndIncome->update($data);

                // 🔹 Agar yangi yozuv DEBT bo‘lsa — qarzni kamaytiramiz
                if ($request->type == ExpenseAndIncome::TYPE_DEBT) {
                    $user = User::with(['userDebt' => fn($q) => $q->where('currency', $data['currency'])])
                        ->find($data['user_id']);

                    $totalDebt = $user->userDebt->sum('amount');

                    if ($totalDebt <= 0) {
                        throw new \RuntimeException('Қарздорда танланган валюта бўйича қарз мавжуд эмас.');
                    }

                    if ($data['amount'] > $totalDebt) {
                        throw new \RuntimeException('Киритилган сумма қарздорликдан ортиқ. Қарздорлик: ' . $totalDebt);
                    }

                    $remaining = $data['amount'];

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

                    $remainingDebtAfter = $user->userDebt->sum('amount');

                    \App\Helpers\TelegramHelper::sendDebtToUser(
                        $user,
                        $totalDebtBefore,
                        $data['amount'],
                        $remainingDebtAfter,
                        $data['currency'],
                        'update'
                    );
                }
            });

            // 🔹 Xabar
            switch ($request->type) {
                case ExpenseAndIncome::TYPE_EXPENSE:
                    $message = 'Харажат янгиланди!';
                    break;
                case ExpenseAndIncome::TYPE_INCOME:
                    $message = 'Кирим янгиланди!';
                    break;
                case ExpenseAndIncome::TYPE_DEBT:
                    $message = 'Қарз сўндириш янгиланди!';
                    break;
                default:
                    $message = 'Муваффақиятли янгиланди!';
            }

            return redirect()->route('expense-and-income.index')->with('success', $message);
            // } catch (\Throwable $e) {
            //     // Orqaga qaytarmasdan, xatoni ekranga chiqarish
            //     dd($e->getMessage(), $e->getFile(), $e->getLine());
            // }
        } catch (\RuntimeException $e) {
            return back()->withErrors(['user_id' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['general' => 'Кутилмаган хато: ' . $e->getMessage()])->withInput();
        }
    }


    public function destroy(ExpenseAndIncome $expenseAndIncome)
    {
        try {
            DB::transaction(function () use ($expenseAndIncome) {
                // Foydalanuvchini yuklaymiz (qarzni tiklash uchun)
                $user = $expenseAndIncome->user;

                // 🔹 Agar yozuv qarz to‘lovi (TYPE_DEBT) bo‘lsa, uni ortga qaytaramiz
                if ($expenseAndIncome->type === ExpenseAndIncome::TYPE_DEBT && $user) {

                    // Foydalanuvchining shu valyutadagi qarzini topamiz
                    $userDebt = $user->userDebt()
                        ->where('currency', $expenseAndIncome->currency)
                        ->first();

                    if ($userDebt) {
                        // 🔹 Qarz miqdorini qaytaramiz
                        $userDebt->amount += $expenseAndIncome->amount;
                        $userDebt->save();
                    } else {
                        // 🔹 Agar yo‘q bo‘lsa — yangi qarz yozuvi yaratamiz
                        $user->userDebt()->create([
                            'amount' => $expenseAndIncome->amount,
                            'currency' => $expenseAndIncome->currency,
                        ]);
                    }
                }

                // 🔹 So‘ng yozuvni o‘chirib tashlaymiz
                $expenseAndIncome->delete();
            });

            // 🔹 Xabarni tayyorlaymiz
            switch ($expenseAndIncome->type) {
                case ExpenseAndIncome::TYPE_EXPENSE:
                    $message = 'Харажат ўчирилди!';
                    break;
                case ExpenseAndIncome::TYPE_INCOME:
                    $message = 'Кирим ўчирилди!';
                    break;
                case ExpenseAndIncome::TYPE_DEBT:
                    $message = 'Қарз сўндириш ўчирилди!';
                    break;
                default:
                    $message = 'Муваффақиятли ўчирилди!';
            }

            return response()->json([
                'message' => $message,
                'type' => 'delete',
                'redirect' => route('expense-and-income.index')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Кутилмаган хато: ' . $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
}
