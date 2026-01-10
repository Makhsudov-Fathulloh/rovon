<?php

namespace App\Models;

use App\Models\ShiftOutput;
use App\Models\StageMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stage extends Model
{
    use HasFactory;

    protected $table = 'stage';

    protected $fillable = [
        'section_id',
        'pre_stage_id',
        'title',
        'description',
        'price',
        'status',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function preStage()
    {
        return $this->belongsTo(self::class, 'pre_stage_id');
    }

    public function shiftOutputs()
    {
        return $this->hasMany(ShiftOutput::class);
    }

    public function stageMaterials()
    {
        return $this->hasMany(StageMaterial::class)->with('rawMaterialVariation');
    }

    public function balances()
    {
        return $this->hasMany(SectionStageBalance::class, 'stage_id');
    }
}
