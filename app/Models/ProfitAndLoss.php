<?php

namespace App\Models;

use App\Models\Search\ProfitAndLossSearch;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ProfitAndLoss extends Model
{
    protected $table = 'profit_and_loss';

    const TYPE_PROFIT  = 1;
    const TYPE_LOSS  = 2;

    protected $fillable = [
        'product_variation_id',
        'order_item_id',
        'original_price',
        'sold_price',
        'profit_amount',
        'loss_amount',
        'count',
        'type',
        'total_amount',
    ];

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public static function getTypeList()
    {
        return [
            self::TYPE_PROFIT => '🟢', // Фойда
            self::TYPE_LOSS => '🔴', // Зарар
        ];
    }

    public static function calculateTotals(Request $request): array
    {
        // Foydalanuvchi tanlagan barcha filterlar qo‘llanadi
        $baseQuery = (new ProfitAndLossSearch(new DateFilterService()))
            ->applyFilters(self::query(), $request);

        $result = [];

        // Kun / Oy / Yil kesimlariga bo‘lamiz
        $periods = [
            'daily' => fn() => (clone $baseQuery)->whereDate('created_at', now()->toDateString()),
            'monthly' => fn() => (clone $baseQuery)->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year),
            'yearly' => fn() => (clone $baseQuery)->whereYear('created_at', now()->year),
        ];

        foreach ($periods as $key => $queryFn) {

            $filteredQuery = $queryFn();  // Barcha filterlardan o‘tgan

            $profitQuery = (clone $filteredQuery)->where('type', self::TYPE_PROFIT);
            $lossQuery   = (clone $filteredQuery)->where('type', self::TYPE_LOSS);

            $profitSum   = $profitQuery->sum('total_amount');
            $lossSum     = $lossQuery->sum('total_amount');

            $profitCount = $profitQuery->count();
            $lossCount   = $lossQuery->count();

            // Xarajatlar: faqat created_at bo‘yicha cheklanadi
            $expenseQuery = ExpenseAndIncome::where('type', ExpenseAndIncome::TYPE_EXPENSE);

            $expenseSum =
                match ($key) {
                    'daily'   => $expenseQuery->whereDate('created_at', now()->toDateString())->sum('amount'),
                    'monthly' => $expenseQuery->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->sum('amount'),
                    'yearly'  => $expenseQuery->whereYear('created_at', now()->year)->sum('amount'),
                };

            $result[$key] = [
                'profit' => ['UZS' => $profitSum, 'count' => $profitCount],
                'loss'   => ['UZS' => $lossSum, 'count' => $lossCount],
                'net'    => [
                    'UZS'   => $profitSum - $expenseSum,
                    'count' => $profitCount + $lossCount,
                ],
            ];
        }

        return $result;
    }
}
