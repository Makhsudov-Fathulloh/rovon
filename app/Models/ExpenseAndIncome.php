<?php

namespace App\Models;

use App\Services\StatusService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseAndIncome extends Model
{
    use HasFactory;

    protected $table = 'expense_and_income';

    const TYPE_DEBT  = 1;
    const TYPE_INCOME  = 2;
    const TYPE_EXPENSE  = 3;

    const TYPE_PAYMENT_CASH  = 1;
    const TYPE_PAYMENT_TRANSFER  = 2;
    const TYPE_PAYMENT_BANK  = 3;


    protected $fillable = [
        'title',
        'description',
        'amount',
        'currency',
        'type',
        'type_payment',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getTypeList()
    {
        return [
            self::TYPE_DEBT => '🟢',
            self::TYPE_INCOME => '🔵',
            self::TYPE_EXPENSE => '🔴',
        ];
    }

    public static function getTypePaymentList()
    {
        return [
            self::TYPE_PAYMENT_CASH     => '🟢',
            self::TYPE_PAYMENT_TRANSFER => '🟡',
            self::TYPE_PAYMENT_BANK     => '🟣',
        ];
    }

    public static function calculateTotals($query, $from = null, $to = null)
    {
        $currencies = [
            'UZS' => StatusService::CURRENCY_UZS ?? 1,
            'USD' => StatusService::CURRENCY_USD ?? 2,
        ];

        // 1. Periodlarni ssenariy bo'yicha aniqlash
        if (!$from && !$to) {
            $now = today();
            $periods = [
                'daily'   => fn($q) => $q->whereDate('created_at', $now),
                'monthly' => fn($q) => $q->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->whereDate('created_at', '<=', $now),
                'yearly'  => fn($q) => $q->whereYear('created_at', $now->year)->whereDate('created_at', '<=', $now),
            ];
        } elseif ($from && !$to) {
            $fDate = Carbon::parse($from);
            $periods = [
                'daily'   => fn($q) => $q->whereDate('created_at', $fDate),
                'monthly' => fn($q) => $q->whereMonth('created_at', $fDate->month)->whereYear('created_at', $fDate->year)->whereDate('created_at', '<=', $fDate),
                'yearly'  => fn($q) => $q->whereYear('created_at', $fDate->year)->whereDate('created_at', '<=', $fDate),
            ];
        } else {
            $fDate = Carbon::parse($from);
            $tDate = Carbon::parse($to);
            $periods = [
                'daily'   => fn($q) => $q->whereDate('created_at', $fDate),
                'monthly' => fn($q) => $q->whereBetween('created_at', [$fDate->startOfDay(), $tDate->endOfDay()]),
                'yearly'  => fn($q) => $q->whereDate('created_at', $tDate),
            ];
        }

        $results = [];
        $types = [
            'expense'   => ['const' => self::TYPE_EXPENSE, 'prefix' => 'Expense'],
            'income'    => ['const' => self::TYPE_INCOME, 'prefix' => 'Income'],
            'debt'      => ['const' => self::TYPE_DEBT, 'prefix' => 'Debt'],
        ];

        // Expense, Income, Debt uchun
        foreach ($types as $key => $typeInfo) {
            foreach ($periods as $period => $filterFunc) {
                $qBase = (clone $query)->where('type', $typeInfo['const']);
                $filterFunc($qBase);

                // View kutayotgan kalit: dailyExpense, monthlyIncome va h.k.
                $viewKey = $period . $typeInfo['prefix'];

                $results[$key][$viewKey] = [
                    'UZS'   => (clone $qBase)->where('currency', $currencies['UZS'])->sum('amount'),
                    'USD'   => (clone $qBase)->where('currency', $currencies['USD'])->sum('amount'),
                    'count' => (clone $qBase)->count(),
                ];
            }
        }

        // Orders (Буюртмалар)
        foreach ($periods as $period => $filterFunc) {
            $qOrder = \App\Models\Order::query();
            $filterFunc($qOrder);

            $viewKey = $period . 'Order';
            $results['order'][$viewKey] = [
                'UZS'   => (clone $qOrder)->where('currency', $currencies['UZS'])->sum('total_price'),
                'USD'   => (clone $qOrder)->where('currency', $currencies['USD'])->sum('total_price'),
                'count' => $qOrder->count(),
            ];
        }

        // Payment (Касса - Нақд тўлов)
        $results['payment'] = \App\Models\Order::calculatePaymentTotals($periods, $currencies);

        // Remaining (Қарздорлик)
        foreach ($periods as $period => $filterFunc) {
            $qRemaining = \App\Models\UserDebt::query();
            $filterFunc($qRemaining);

            $viewKey = $period . 'RemainingDebt';
            $results['remaining'][$viewKey] = [
                'UZS'   => (clone $qRemaining)->where('currency', $currencies['UZS'])->sum('amount'),
                'USD'   => (clone $qRemaining)->where('currency', $currencies['USD'])->sum('amount'),
                'count' => $qRemaining->count(),
            ];
        }

        return $results;
    }
}
