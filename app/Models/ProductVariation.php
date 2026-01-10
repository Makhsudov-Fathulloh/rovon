<?php

namespace App\Models;

use App\Services\StatusService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class

ProductVariation extends Model
{
    use HasFactory;
    const TOP_ACTIVE = 1;
    const TOP_INACTIVE = 0;

    protected $table = 'product_variation';

    protected $fillable = [
        'product_id',
        'code',
        'title',
        'subtitle',
        'description',
        'image',
        'count',
        'min_count',
        'unit',
        'body_price',
        'price',
        'currency',
        'rate',
        'price_uzs',
        'total_price',
        'type',
        'top',
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
            $usdRate = ExchangeRates::where('currency', 'USD')->value('rate') ?? 12800;
            $this->rate = $usdRate;
            $this->price_uzs = $this->price * $this->rate;
        } else {
            $this->rate = 1;
            $this->price_uzs = $this->price;
        }

        $this->total_price = $this->count * $this->price_uzs;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'image');
    }

    public function profitAndLoss()
    {
        return $this->hasMany(ProfitAndLoss::class);
    }

    public function logs()
    {
        return $this->hasMany(ProductLog::class);
    }

    public static function getTopList()
    {
        return [
            self::TOP_INACTIVE => 'Not top',
            self::TOP_ACTIVE => 'TOP',
        ];
    }

    public function decrementStock(int $qty): void
    {
        if ($this->count < $qty) {
            throw new \RuntimeException("Омборда етарли маҳсулот мавжуд эмас ({$this->title})");
        }

        $this->count -= $qty;
        $this->recalculateTotalPrice();
        $this->save();

        // Agar limitdan pastga tushsa notify eventini chaqiramiz:
        if ($this->count <= $this->min_count) {
            event(new \App\Events\ProductLowEvent($this));
        }
    }

    public function incrementStock(int $qty): void
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
