<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouse';

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());
    }

    public function organization()
    {
        return $this->belongsToMany(Organization::class, 'organization_warehouse', 'warehouse_id', 'organization_id')
            ->withTimestamps();
    }

    public function rawMaterial()
    {
        return $this->hasMany(RawMaterial::class);
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
