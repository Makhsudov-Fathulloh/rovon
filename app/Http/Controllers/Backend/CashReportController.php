<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\TelegramHelper;
use App\Http\Controllers\Controller;
use App\Models\CashReport;
use App\Models\ExpenseAndIncome;
use App\Models\Order;
use App\Models\User;
use App\Models\UserDebt;
use App\Services\StatusService;
use Illuminate\Http\Request;

class CashReportController extends Controller
{
    public function index(Request $request)
    {
        $query = CashReport::query();

        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        $todayReport = CashReport::whereDate('report_date', today())->first();
        $currencies = StatusService::getCurrency();

        $cashReports = $query->orderByDesc('created_at')->paginate(20)->withQueryString(); // filterni paginationda saqlash uchun

        return view('backend.cash-report.index', compact('todayReport', 'currencies', 'cashReports'));
    }

    //    public function openDailyReport()
    //    {
    //        $today = today();
    //
    //        // 🔹 Valyutalar ro'yxati (agar kelajakda yangi valyuta qo'shilsayu shunchaki arrayga qo'shamiz)
    //        $currencies = [
    //            StatusService::CURRENCY_UZS,
    //            StatusService::CURRENCY_USD,
    //            // StatusService::CURRENCY_EUR, va hokazo
    //        ];
    //
    //        foreach ($currencies as $currency) {
    //            // 🔹 Agar allaqachon hisobot mavjud bo'lsa, faqat statusni o'zgartiramiz
    //            $report = CashReport::firstOrCreate(
    //                [
    //                    'report_date' => $today,
    //                    'currency'    => $currency, // har valyuta uchun alohida
    //                ],
    //                [
    //                    'status' => CashReport::CASH_OPEN,
    //                ]
    //            );
    //
    //            // Agar allaqachon ochiq bo'lsa, update qilish shart emas
    //            if ($report->isOpen()) {
    //                continue;
    //            }
    //
    //            // Statusni ochiq qilib belgilash
    //            $report->update([
    //                'status' => CashReport::CASH_OPEN,
    //            ]);
    //        }
    //
    //        return back()->with('success', 'Барча валюта бўйича кунлик ҳисобот очилди!');
    //    }

    public function openDailyReport()
    {
        $today = today();

        $currencies = [
            StatusService::CURRENCY_UZS,
            StatusService::CURRENCY_USD,
        ];

        // 🔹 Bitta qator – har bir ustun JSON
        $report = CashReport::whereDate('report_date', $today)->first();

        // 🔹 Agar yo‘q bo‘lsa — yangi ochiladi
        if (!$report) {
            $report = CashReport::create([
                'report_date' => $today,
                'total_order_amount' => array_fill_keys($currencies, 0),
                'total_amount_paid' => array_fill_keys($currencies, 0),
                'total_remaining_debt' => array_fill_keys($currencies, 0),
                'total_expense' => array_fill_keys($currencies, 0),
                'total_income' => array_fill_keys($currencies, 0),
                'total_debt_paid' => array_fill_keys($currencies, 0),
                'status' => CashReport::CASH_OPEN,
            ]);
        } else {
            // 🔹 Agar yopilgan bo‘lsa — qayta ochiladi, qiymatlar o‘zgarishsiz qoladi
            if ($report->isClose()) {
                $report->update(['status' => CashReport::CASH_OPEN]);
            }
        }

        return back()->with('success', 'Барча валюта бўйича кунлик ҳисобот очилди!');
    }

    //    public function closeDailyReport()
    //    {
    //        $today = now()->toDateString();
    //
    //        // Valyutalar ro‘yxati
    //        $currencies = [
    //            StatusService::CURRENCY_UZS,
    //            StatusService::CURRENCY_USD,
    //        ];
    //
    //        foreach ($currencies as $currency) {
    //
    //            // Agar kunlik hisobot mavjud bo‘lmasa, yaratamiz
    //            $report = CashReport::firstOrCreate(
    //                ['report_date' => $today, 'currency' => $currency],
    //                ['status' => CashReport::CASH_OPEN]
    //            );
    //
    //            if (!$report) {
    //                return back()->with('error', 'Бугунги ҳисобот очилмаган!');
    //            }
    //
    //            if ($report->isClose()) {
    //                return back()->with('error', 'Бугунги ҳисобот аллақачон ёпилган!');
    //            }
    //
    //            $totalOrderAmount = Order::where('currency', $currency)->whereDate('created_at', $today)->sum('total_price');
    //            $totalAmountPaid = Order::where('currency', $currency)->whereDate('created_at', $today)->sum('total_amount_paid');
    //            $totalRemainingDebt = Order::where('currency', $currency)->whereDate('created_at', $today)->sum('remaining_debt');
    //            $totalExpense = ExpenseAndIncome::where('currency', $currency)->where('type', ExpenseAndIncome::TYPE_EXPENSE)->whereDate('created_at', $today)->sum('amount');
    //            $totalIncome = ExpenseAndIncome::where('currency', $currency)->where('type', ExpenseAndIncome::TYPE_INCOME)->whereDate('created_at', $today)->sum('amount');
    //            $totalDebtPaid = ExpenseAndIncome::where('currency', $currency)->where('type', ExpenseAndIncome::TYPE_DEBT)->whereDate('created_at', $today)->sum('amount');
    //
    //            $report->update([
    //                'total_order_amount' => $totalOrderAmount,
    //                'total_amount_paid' => $totalAmountPaid,
    //                'total_remaining_debt' => $totalRemainingDebt,
    //                'total_expense' => $totalExpense,
    //                'total_income' => $totalIncome,
    //                'total_debt_paid' => $totalDebtPaid,
    //                'status' => CashReport::CASH_CLOSE,
    //            ]);
    //        }
    //
    //        return back()->with('success', 'Барча валюта бўйича кунлик ҳисобот ёпилди!');
    //    }

    public function closeDailyReport()
    {
        $today = today();
        $currencies = [
            StatusService::CURRENCY_UZS,
            StatusService::CURRENCY_USD,
        ];

        $report = CashReport::whereDate('report_date', $today)->first();

        if (!$report) {
            return back()->with('error', 'Бугунги ҳисобот очилмаган!');
        }

        // 🔹 Hisoblash
        $totals = [
            'total_order_amount' => [],
            'total_amount_paid' => [],
            'total_remaining_debt' => [],
            'total_expense' => [],
            'total_income' => [],
            'total_debt_paid' => [],
        ];

        foreach ($currencies as $currency) {
            $totals['total_order_amount'][$currency] = Order::where('currency', $currency)->whereDate('created_at', $today)->sum('total_price');
            $totals['total_amount_paid'][$currency] = Order::where('currency', $currency)->whereDate('created_at', $today)->sum('total_amount_paid');
            $totals['total_remaining_debt'][$currency] = UserDebt::where('currency', $currency)->whereDate('created_at', $today)->sum('amount');
            // $totals['total_return_amount'][$currency] = ProductReturn::where('currency', $currency)->whereDate('created_at', $today)->sum('total_amount');
            $totals['total_expense'][$currency] = ExpenseAndIncome::where('currency', $currency)->where('type', ExpenseAndIncome::TYPE_EXPENSE)->whereDate('created_at', $today)->sum('amount');
            $totals['total_income'][$currency] = ExpenseAndIncome::where('currency', $currency)->where('type', ExpenseAndIncome::TYPE_INCOME)->whereDate('created_at', $today)->sum('amount');
            $totals['total_debt_paid'][$currency] = ExpenseAndIncome::where('currency', $currency)->where('type', ExpenseAndIncome::TYPE_DEBT)->whereDate('created_at', $today)->sum('amount');
        }

        // 🔹 Ma’lumotlar yangilanadi (eski qiymatlar o‘rniga)
        $report->update([
            'total_order_amount' => $totals['total_order_amount'],
            'total_amount_paid' => $totals['total_amount_paid'],
            'total_remaining_debt' => $totals['total_remaining_debt'],
            // 'total_return_amount' => $totals['total_return_amount'],
            'total_expense' => $totals['total_expense'],
            'total_income' => $totals['total_income'],
            'total_debt_paid' => $totals['total_debt_paid'],
            'status' => CashReport::CASH_CLOSE,
        ]);

        $this->sendDailyReportToTelegram($report, $totals);

        return back()->with('success', 'Барча валюта бўйича кунлик ҳисобот ёпилди!');
    }

    private function sendDailyReportToTelegram($report, $totals)
    {
        // $users = User::whereHas('role', function ($query) {
        //     $query->whereIn('title', ['Developer', 'Admin', 'Manager']);
        // })->whereNotNull('telegram_chat_id')->get();

        // if ($users->isEmpty()) {
        //     return;
        // }

        $adminChatIds = array_filter(
            array_map('trim', explode(',', env('TELEGRAM_ADMINS')))
        );

        if (empty($adminChatIds)) {
            return;
        }

        $labels = [
            'total_order_amount'   => 'Жами буюртма',
            'total_amount_paid'    => 'Жами тўланган',
            'total_remaining_debt' => 'Жами қолган қарз',
            'total_expense'        => 'Жами харажат',
            'total_income'         => 'Жами кирим',
            'total_debt_paid'      => 'Жами қарз сўндириш',
        ];

        $currencyLabels = [
            StatusService::CURRENCY_UZS => 'сўм',
            StatusService::CURRENCY_USD => '$',
        ];

        $message = "📅 <b>Кунлик касса ҳисоботи</b>\n\n";
        $message .= "Сана: <b>{$report->report_date->format('d.m.Y')}</b>\n\n";

        foreach ($totals as $key => $values) {

            $title = $labels[$key] ?? strtoupper(str_replace('_', ' ', $key));
            $message .= "<b>🔸 {$title}</b>\n";

            foreach ($values as $currency => $amount) {

                $symbol = $currencyLabels[$currency] ?? $currency;
                $precision = $currency == StatusService::CURRENCY_UZS ? 0 : 2;

                $amountFormatted = number_format($amount, $precision, '.', ' ');
                $message .= " — {$amountFormatted} {$symbol}\n";
            }

            $message .= "\n";
        }

        // foreach ($users as $user) {
        //     TelegramHelper::send($user->telegram_chat_id, $message);
        // }

        foreach ($adminChatIds as $chatId) {
            TelegramHelper::send($chatId, $message);
        }
    }
}
