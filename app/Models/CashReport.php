<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashReport extends Model
{
    const CASH_OPEN = 1;
    const CASH_CLOSE = 2;

    protected $table = 'cash_report';

    protected $fillable = [
        'report_date',
//        'currency',
        'total_order_amount', // jami_buyurtma_summa
        'total_amount_paid', // jami_to'langan_summa
        'total_remaining_debt', // jami_qolgan_qarz
        'total_return_amount', // jami_raytish_miqdori
        'total_expense', // jami_xarajat
        'total_income', // jami_kirim
        'total_debt_paid', // jami_qarz_so'ndirish
        'status',

    ];

    protected $casts = [
        'report_date' => 'date',

        'total_order_amount' => 'array',
        'total_amount_paid' => 'array',
        'total_remaining_debt' => 'array',
        'total_return_amount' => 'array',
        'total_expense' => 'array',
        'total_income' => 'array',
        'total_debt_paid' => 'array',
    ];

    public function scopeToday($query)
    {
        return $query->where('report_date', today());
    }

    public function isOpen()
    {
        return $this->status === $this::CASH_OPEN;
    }

    public function isClose()
    {
        return $this->status === $this::CASH_CLOSE;
    }

    public static function checkCashReport()
    {
        $todayCash = CashReport::today()->first();

        if (!$todayCash || $todayCash->isClose()) {
            return redirect()
                ->route('expense-and-income.index')
                ->with('error', 'Аввал кунлик касса ҳисоботини очинг!');
        }

        return true;
    }
}
