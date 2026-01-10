<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = [
        'warehouse_id',
        'title',
        'subtitle',
        'description',
        'image',
        'user_id',
        'category_id',
        'type',
        'slug',
        'status',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());

        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->user_id = Auth::id();
            }
        });

        static::creating(function ($model) {
            $model->slug = Str::slug($model->title, '-');
            $count = static::where('slug', 'like', "{$model->slug}%")->count();
            if ($count > 0) {
                $model->slug .= '-' . ($count + 1);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'image');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public static function getDropdownList($category_id = null)
    {
        if ($category_id) {
            return static::where('category_id', $category_id)->pluck('title', 'id')->toArray();
        }

        return static::all()->pluck('title', 'id')->toArray();
    }
}
