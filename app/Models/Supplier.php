<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';

    protected $fillable = [
        'title',
        'address',
        'phone',
        'status',
        'currency',
        'rate'
    ];

    public function items()
    {
        return $this->hasMany(SupplierItem::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
    }
}
