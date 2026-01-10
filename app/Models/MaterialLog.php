<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MaterialLog extends Model
{
    use HasFactory;

    protected $table = 'material_log';

    protected $fillable = [
        'raw_material_variation_id',
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

    public function rawMaterialVariation()
    {
        return $this->belongsTo(RawMaterialVariation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
