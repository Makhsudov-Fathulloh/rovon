<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    protected $table = 'product_return';

    protected $fillable = [
        'expense_id',
        'title',
        'total_amount',
        'currency',
        'rate',
        'user_id'
    ];

    public function items()
    {
        return $this->hasMany(ProductReturnItem::class);
    }

    public function expense()
    {
        return $this->belongsTo(ExpenseAndIncome::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
