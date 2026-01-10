<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashReport;
use App\Models\Order;
use App\Models\ExpenseAndIncome;
use Illuminate\Support\Facades\Log;

class AutoCloseCashReport extends Command
{
    protected $signature = 'cashreport:auto-close';
    protected $description = 'Кун охирида очилган кассани автоматик ёпиш.';

    public function handle()
    {
        $today = now()->toDateString();
        $report = CashReport::where('report_date', $today)->first();

        if (!$report || $report->isClose()) {
//            $this->info('Бугунги ҳисоб аллақачон ёпилган ёки очилмаган.');
            return Command::SUCCESS;
        }

        $totalOrderAmount = Order::whereDate('created_at', $today)->sum('total_price');
        $totalAmountPaid = Order::whereDate('created_at', $today)->sum('amount_paid');
        $totalRemainingDebt = Order::whereDate('created_at', $today)->sum('remaining_debt');
        $totalExpense = ExpenseAndIncome::where('type', ExpenseAndIncome::TYPE_EXPENSE)->whereDate('created_at', $today)->sum('amount');
        $totalIncome = ExpenseAndIncome::where('type', ExpenseAndIncome::TYPE_INCOME)->whereDate('created_at', $today)->sum('amount');
        $totalDebtPaid = ExpenseAndIncome::where('type', ExpenseAndIncome::TYPE_DEBT)->whereDate('created_at', $today)->sum('amount');

        $report->update([
            'total_order_amount'   => $totalOrderAmount,
            'total_amount_paid'    => $totalAmountPaid,
            'total_remaining_debt' => $totalRemainingDebt,
            'total_expense'        => $totalExpense,
            'total_income'         => $totalIncome,
            'total_debt_paid'      => $totalDebtPaid,
            'status'               => CashReport::CASH_CLOSE,
        ]);

        $this->info('Бугунги ҳисоб автоматик ёпилди! ✅');

        return Command::SUCCESS;
    }
}
