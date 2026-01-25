<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 1;
    const STATUS_PAID  = 2;
    const STATUS_SHIPPED = 3;

    const TOP_ACTIVE = 1;
    const TOP_INACTIVE = 0;

    protected $table = 'order';

    protected $fillable = [
        'user_id',
        'status',
        'currency',
        'exchange_rate',
        'total_price',
        'total_price_base',  // UZS (bazaviy valyuta)
        'cash_paid',
        'card_paid',
        'transfer_paid',
        'bank_paid',
        'total_amount_paid',
        'remaining_debt',
        'seller_id'
    ];

    /**
     * 🧮 Order yaratilganda avtomatik valyuta kursini olish
     */
    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->exchange_rate)) {
                $rate = ExchangeRates::where('currency', $order->currency)
                    ->latest('created_at')
                    ->value('rate') ?? 1;
                $order->exchange_rate = $rate;
            }
        });

        static::updating(function ($order) {
            if (!$order->isDirty('remaining_debt')) {
                return;
            }

            $oldDebt = (float) $order->getOriginal('remaining_debt');
            $newDebt = (float) $order->remaining_debt;
            $diff = $newDebt - $oldDebt;

            if ($diff == 0) {
                return;
            }

            $userDebt = UserDebt::where('user_id', $order->user_id)
                ->where('order_id', $order->id)
                ->where('currency', $order->currency)
                ->first();

            // 🔹 QARZ PAYDO BO‘LDI, LEKIN YO‘Q
            if (!$userDebt && $diff > 0) {
                UserDebt::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'amount' => $diff,
                    'currency' => $order->currency,
                    'source' => UserDebt::SOURCE_ORDER,
                ]);
                return;
            }

            if (!$userDebt) {
                return;
            }

            $newAmount = $userDebt->amount + $diff;

            if ($newAmount <= 0) {
                $userDebt->delete();
            } else {
                $userDebt->update(['amount' => $newAmount]);
            }
        });

        static::deleting(function ($order) {
            UserDebt::where('order_id', $order->id)
                ->where('source', UserDebt::SOURCE_ORDER)
                ->delete();
        });
    }

    /**
     * ✅ Ayni aniq arifmetik funksiyalar
     */
    //    public static function bc_mul($a, $b, $scale = 10) {
    //        return function_exists('bcmul') ? bcmul((string)$a, (string)$b, $scale) : number_format((float)$a * (float)$b, $scale, '.', '');
    //    }
    //
    //    public static function bc_div($a, $b, $scale = 10) {
    //        if (!$b || $b == 0) return 0;
    //        return function_exists('bcdiv') ? bcdiv((string)$a, (string)$b, $scale) : number_format((float)$a / (float)$b, $scale, '.', '');
    //    }

    public static function bc_add($a, $b, $scale = 10)
    {
        if (function_exists('bcadd')) {
            return bcadd((string)$a, (string)$b, $scale);
        }

        return number_format((float)$a + (float)$b, $scale, '.', '');
    }

    public static function bc_div($a, $b, $scale = 10)
    {
        if (!$b || $b == 0) return 0;
        if (function_exists('bcdiv')) {
            return bcdiv((string)$a, (string)$b, $scale);
        }

        return number_format((float)$a / (float)$b, $scale, '.', '');
    }

    public static function bc_mul($a, $b, $scale = 10)
    {
        if (function_exists('bcmul')) {
            return bcmul((string)$a, (string)$b, $scale);
        }

        // fallback agar bcmath yo‘q bo‘lsa
        return number_format((float)$a * (float)$b, $scale, '.', '');
    }

    public static function bc_sub($a, $b, $scale = 10)
    {
        if (function_exists('bcsub')) {
            return bcsub((string)$a, (string)$b, $scale);
        }
        return number_format((float)$a - (float)$b, $scale, '.', '');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_PENDING   => 'Кутилмоқда',
            self::STATUS_PAID => 'Тўланган',
            self::STATUS_SHIPPED  => 'Юборилди',
        ];
    }

    public static function getTopList()
    {
        return [
            self::TOP_INACTIVE => 'Not top',
            self::TOP_ACTIVE => 'TOP',
        ];
    }


    public static function calculatePaymentTotals($periods, $currencies)
    {
        $totals = [];

        foreach ($periods as $period => $filterFunc) {
            // View kutayotgan kalit: dailyPayment, monthlyPayment...
            $viewKey = $period . 'Payment';

            foreach ($currencies as $currKey => $currId) {
                // 1. Orderdagi to'lovlar
                $orderQ = self::where('currency', $currId);
                $filterFunc($orderQ);

                $totals[$viewKey][$currKey] = [
                    'cash_paid'     => $orderQ->sum('cash_paid'),
                    'card_paid'     => $orderQ->sum('card_paid'),
                    'transfer_paid' => $orderQ->sum('transfer_paid'),
                    'bank_paid'     => $orderQ->sum('bank_paid'),
                ];

                // 2. ExpenseAndIncome qo'shimchalari (+Income +Debt -Expense)
                $expQ = \App\Models\ExpenseAndIncome::where('currency', $currId);
                $filterFunc($expQ);

                $paymentMap = [
                    'cash_paid'     => \App\Models\ExpenseAndIncome::TYPE_PAYMENT_CASH,
                    'transfer_paid' => \App\Models\ExpenseAndIncome::TYPE_PAYMENT_TRANSFER,
                    'bank_paid'     => \App\Models\ExpenseAndIncome::TYPE_PAYMENT_BANK,
                ];

                foreach ($paymentMap as $field => $payType) {
                    $subExpQ = (clone $expQ)->where('type_payment', $payType);

                    $sumDebt    = (clone $subExpQ)->where('type', \App\Models\ExpenseAndIncome::TYPE_DEBT)->sum('amount');
                    $sumIncome  = (clone $subExpQ)->where('type', \App\Models\ExpenseAndIncome::TYPE_INCOME)->sum('amount');
                    $sumExpense = (clone $subExpQ)->where('type', \App\Models\ExpenseAndIncome::TYPE_EXPENSE)->sum('amount');

                    $totals[$viewKey][$currKey][$field] += ($sumDebt + $sumIncome - $sumExpense);
                }
            }
        }
        return $totals;
    }
}
