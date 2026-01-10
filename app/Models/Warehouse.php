<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouse';

    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'status',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
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
