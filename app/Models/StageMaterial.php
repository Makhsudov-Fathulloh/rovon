<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageMaterial extends Model
{
    use HasFactory;

    protected $table = 'stage_material';

    protected $fillable = [
        'stage_id',
        'raw_material_variation_id',
        'count',
        'unit',
    ];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function rawMaterialVariation()
    {
        return $this->belongsTo(RawMaterialVariation::class);
    }
}
