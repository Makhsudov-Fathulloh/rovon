<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreOrderItem extends Model
{
    protected $table = 'pre_order_item';

    protected $fillable = [
        'pre_order_id',
        'product_variation_id',
        'code',
        'count',
        'unit',
    ];

    public function preOrder()
    {
        return $this->belongsTo(PreOrder::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function scopeFilter($q, array $filters)
    {
        return $q
            ->when($filters['status'] ?? null, fn($qq, $s) => $qq->where('status', $s))
            ->when($filters['code'] ?? null, fn($qq, $code) => $qq->where('code', 'like', "%$code%"))
            ->when($filters['title'] ?? null, fn($qq, $title) => $qq->where('title', 'like', "%$title%"))
            ->when($filters['customer_id'] ?? null, fn($qq, $cid) => $qq->where('customer_id', $cid));
    }
}
