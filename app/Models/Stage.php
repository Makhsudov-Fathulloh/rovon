<?php

namespace App\Models;

use App\Models\ShiftOutput;
use App\Models\StageMaterial;
use App\Services\StatusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stage extends Model
{
    use HasFactory;

    protected $table = 'stage';

    protected $fillable = [
        'section_id',
        'title',
        'description',
        'price',
        'defect_type',
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

    public function preStages()
    {
        return $this->belongsToMany(self::class, 'stage_pre_stage', 'stage_id', 'pre_stage_id');
    }

    public function nextStages()
    {
        return $this->belongsToMany(self::class, 'stage_pre_stage', 'pre_stage_id', 'stage_id');
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


    public function getIndexAttribute()
    {
        static $index = 0;
        static $lastId = null;

        if ($this->id !== $lastId) {
            $index++;
            $lastId = $this->id;
        }

        return $index;
    }
    public function getFullTitleAttribute()
    {
        $section = $this->section->title;

        return $section ? "{$this->title} ({$section})" : $this->title;
    }

    public function getMonthlyCountAttribute()
    {
        return ShiftOutputWorker::whereHas('shiftOutput', function($q) {
            $q->where('stage_id', $this->id);
        })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('stage_count');
    }

    public function getMonthlyDefectAttribute()
    {
        return ShiftOutputWorker::whereHas('shiftOutput', function($q) {
            $q->where('stage_id', $this->id);
        })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('defect_amount');
    }

    public function getMonthlyDefectRawAttribute()
    {
        if ($this->defect_type !== StatusService::DEFECT_RAW_MATERIAL) {
            return 0;
        }

        return $this->shiftOutputs()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('defect_amount');
    }

    public function getMonthlyDefectPrevAttribute()
    {
        if ($this->defect_type !== StatusService::DEFECT_PREVIOUS_STAGE) {
            return 0;
        }

        return $this->shiftOutputs()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('defect_amount');
    }
}
