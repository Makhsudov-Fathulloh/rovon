<?php

namespace App\Models;

use App\Services\StatusService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organization';

    protected $fillable = [
        'title',
        'description',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user')->withTimestamps();
    }

    public function section()
    {
        return $this->hasMany(Section::class);
    }

    public function warehouse()
    {
        return $this->belongsToMany(Warehouse::class, 'organization_warehouse', 'organization_id', 'warehouse_id')->withTimestamps();
    }

    public function allWarehouse()
    {
        return $this->warehouses()->where('type', StatusService::TYPE_ALL);
    }

    public function sparePartWarehouse()
    {
        return $this->warehouse()->where('type', StatusService::TYPE_SPARE_PART) ??
            $this->warehouse()->where('type', StatusService::TYPE_ALL);
    }

    public function rawWarehouse()
    {
        $hasRaw = $this->warehouse()->where('type', StatusService::TYPE_RAW_MATERIAL)->exists();

        if ($hasRaw) {
            return $this->warehouse()->where('type', StatusService::TYPE_RAW_MATERIAL);
        }

        return $this->warehouse()->where('type', StatusService::TYPE_ALL);
    }
    public function productWarehouse()
    {
        return $this->warehouse()->where('type', StatusService::TYPE_PRODUCT) ??
            $this->warehouse()->where('type', StatusService::TYPE_ALL);
    }
}
