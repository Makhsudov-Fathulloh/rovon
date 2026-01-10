<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductLog extends Model
{
    use HasFactory;

    protected $table = 'product_log';

    protected $fillable = [
        'product_variation_id',
        'old_count',
        'added_count',
        'new_count',
        'user_id',
        'action',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());

        parent::boot();
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
