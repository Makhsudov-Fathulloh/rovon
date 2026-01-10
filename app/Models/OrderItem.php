<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_item';

    protected $fillable = [
        'order_id',
        'product_variation_id',
        'quantity',
        'price',              // Order valyutasida
        'total_price',        // Order valyutasida
        'price_base',         // UZS
        'total_price_base',   // UZS
    ];


    /**
     * 🔄 Saqlanishdan oldin konvertatsiya
     */
    protected static function booted()
    {
        static::saving(function ($item) {
            $rate = $item->order?->exchange_rate ?? 1;

            $item->total_price = $item->price * $item->quantity;

            // Bazaviy valyuta (UZS) ga konvertatsiya
            $item->price_base = $item->price * $rate;
            $item->total_price_base = $item->total_price * $rate;
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function profitAndLoss()
    {
        return $this->hasOne(ProfitAndLoss::class);
    }
}
