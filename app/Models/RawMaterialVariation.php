<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Services\StatusService;
use App\Models\ExchangeRates;

class RawMaterialVariation extends Model
{
    use HasFactory;

    protected $table = 'raw_material_variation';

    protected $fillable = [
        'raw_material_id',
        'code',
        'title',
        'subtitle',
        'description',
        'image',
        'count',
        'min_count',
        'unit',
        'price',
        'currency',
        'rate',
        'price_uzs',
        'total_price',
        'type',
        'slug',
        'status',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());

        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->title, '-');
            $count = static::where('slug', 'like', "{$model->slug}%")->count();
            if ($count > 0) {
                $model->slug .= '-' . ($count + 1);
            }

            $model->currency ??= 1;
            $model->rate ??= 1;
        });

        static::saving(function ($variation) {
            if ($variation->currency == 2) {
                $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');
                $variation->rate = $usdRate;
            } else {
                $variation->rate = 1;
            }

            $variation->price_uzs = $variation->currency == 2
                ? $variation->price * $variation->rate
                : $variation->price;

            $variation->total_price = $variation->count * $variation->price_uzs;

            $variation->status = $variation->count > 0
                ? StatusService::STATUS_ACTIVE
                : StatusService::STATUS_INACTIVE;
        });
    }

    public function recalculateTotalPrice(): void
    {
        if ($this->currency == 2) {
            $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');
            $this->rate = $usdRate;
            $this->price_uzs = $this->price * $this->rate;
        } else {
            $this->rate = 1;
            $this->price_uzs = $this->price;
        }

        $this->total_price = $this->count * $this->price_uzs;
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function stageMaterials()
    {
        return $this->hasMany(StageMaterial::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'image');
    }

    public function logs()
    {
        return $this->hasMany(MaterialLog::class);
    }

    public function decrementStock(float $qty): void
    {
        if ($this->count < $qty) {
            throw new \RuntimeException("Омборда етарли хомашё мавжуд эмас ({$this->title})");
        }

        $this->count -= $qty;
        $this->recalculateTotalPrice();
        $this->save();

        // Agar limitdan pastga tushsa notify eventini chaqiramiz:
        if ($this->count <= $this->min_count) {
            event(new \App\Events\RawMaterialLowEvent($this));
        }
    }

    public function incrementStock(float $qty): void
    {
        $this->count += $qty;
        $this->recalculateTotalPrice();
        $this->save();
    }

    public static function checkLowStock(): array
    {
        return static::whereColumn('count', '<=', 'min_count')
            ->get(['id', 'code', 'title', 'count', 'unit'])
            ->map(function ($item) {
                $count = \App\Helpers\CountHelper::format($item->count, $item->unit) ?? '';

                return "{$item->code} - {$item->title} - {$count}";
            })
            ->toArray();
    }
}
