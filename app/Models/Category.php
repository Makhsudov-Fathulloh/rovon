<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    const TYPE_SPARE_PART = 1;
    const TYPE_RAW_MATERIAL = 2;
    const TYPE_PRODUCT = 3;

    protected $table = 'category';

    protected $fillable = [
        'parent_id',
        'title',
        'subtitle',
        'description',
        'image',
        'slug',
        'type',
        'user_id',
    ];

    protected static function booted()
    {
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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

//    public function file()
//    {
//        return $this->belongsTo(File::class, 'image');
//    }

    public function file()
    {
        return $this->hasOne(File::class, 'id', 'image');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public static function getDropdownList($id = null, $type = null)
    {
        $query = static::query();

        if ($id) {
            return $query->where('parent_id', $id)->orderBy('created_at')->pluck('title', 'id')->toArray();
        }

        if ($type !== null) {
            $query->where('type', $type)->orderBy('created_at')->pluck('title', 'id')->toArray();
        }

        return $query->orderBy('created_at')->pluck('title', 'id')->toArray();
    }


    public static function getSubCategoryDropdownList($cat_id)
    {
        return static::where('parent_id', $cat_id)
            ->get(['id', 'title as title'])
            ->toArray();
    }

    public static function getTypeList()
    {
        return [
            self::TYPE_SPARE_PART => 'Эхтиёт кисм',
            self::TYPE_RAW_MATERIAL => 'Хомашё',
            self::TYPE_PRODUCT => 'Маҳсулот',
        ];
    }
}
