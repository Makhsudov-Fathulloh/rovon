<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialTransferItem extends Model
{
    use HasFactory;

    protected $table = 'raw_material_transfer_item';

    protected $fillable = [
        'raw_material_transfer_id',
        'raw_material_variation_id',
        'count',
        'unit',
        'price',
        'total_price',
    ];

    protected static function booted()
    {
        // 🔹 Yaratilganda
        static::creating(function ($item) {
            $variation = $item->rawMaterialVariation;

            if (!$variation) {
                throw new \RuntimeException("Хомашё топилмади!");
            }

            // ❌ Yetarli xomashyo yo‘qmi?
            if ($variation->count < $item->count) {
                throw new \RuntimeException("Омборда йетарлм хомашё йок ({$variation->title})!");
            }

            // 💰 Narx va umumiy summa
            $item->price = $variation->price;
            $item->total_price = $item->count * $item->price;

            // ✅ Skladdan ayrish
            $variation->decrementStock($item->count);
        });

        // 🔹 O‘chirilganda — miqdorni qaytarish
        static::deleted(function ($item) {
            $item->rawMaterialVariation?->incrementStock($item->count);
        });

        // 🔹 Yangilanganida (count o‘zgarsa)
        static::updating(function ($item) {
            $variation = $item->rawMaterialVariation;
            $oldQty = $item->getOriginal('count');
            $newQty = $item->count;

            if ($newQty > $oldQty) {
                $diff = $newQty - $oldQty;
                if ($variation->count < $diff) {
                    throw new \RuntimeException("Йетарлм хомашё йок ({$variation->title})!");
                }
                $variation->decrementStock($diff);
            } elseif ($newQty < $oldQty) {
                $variation->incrementStock($oldQty - $newQty);
            }

            $item->total_price = $item->price * $item->count;
        });
    }

    public function rawMaterialTransfer()
    {
        return $this->belongsTo(RawMaterialTransfer::class, 'raw_material_transfer_id');
    }

    public function rawMaterialVariation()
    {
        return $this->belongsTo(RawMaterialVariation::class, 'raw_material_variation_id');
    }
}
