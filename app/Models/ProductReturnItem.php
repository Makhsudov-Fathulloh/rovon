<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReturnItem extends Model
{
    protected $table = 'product_return_item';

    protected $fillable = [
        'product_return_id',
        'product_variation_id',
        'count',
        'price',
        'total_price'
    ];

    public function productReturn()
    {
        return $this->belongsTo(ProductReturn::class, 'product_return_id');
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}
