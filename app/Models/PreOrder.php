<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreOrder extends Model
{
    const STATUS_NEW = 1;
    const STATUS_INPROGRESS = 2;
    const STATUS_DONE = 3;
    const STATUS_CANCELLED = 4;

    protected $table = 'pre_order';

    protected $fillable = [
        'title',
        'description',
        'count',
        'unit',
        'user_id', // Client
        'customer_id',
        'status',
    ];

    public function preOrderItems()
    {
        return $this->hasMany(PreOrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_NEW => 'Янги буюртма',
            self::STATUS_INPROGRESS => 'Жараёнда',
            self::STATUS_DONE  => 'Якунланган',
            self::STATUS_CANCELLED  => 'Бекор килинган',
        ];
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NEW        => 'badge bg-primary',
            self::STATUS_INPROGRESS => 'badge bg-info',
            self::STATUS_DONE       => 'badge bg-success',
            self::STATUS_CANCELLED  => 'badge bg-danger',
            default => 'badge bg-light text-dark',
        };
    }

    public function scopeFilter($q, array $filters)
    {
        return $q
            ->when($filters['status'] ?? null, fn($qq, $s) => $qq->where('status', $s))
            ->when($filters['code'] ?? null, fn($qq, $code) => $qq->where('product_code', 'like', "%$code%"))
            ->when($filters['title'] ?? null, fn($qq, $title) => $qq->where('product_title', 'like', "%$title%"))
            ->when($filters['customer_id'] ?? null, fn($qq, $cid) => $qq->where('customer_id', $cid));
    }
}
