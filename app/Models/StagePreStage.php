<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StagePreStage extends Pivot
{
    protected $table = 'stage_pre_stage';

    public $timestamps = false;

    protected $fillable = [
        'stage_id',
        'pre_stage_id',
    ];

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function preStage()
    {
        return $this->belongsTo(Stage::class, 'pre_stage_id');
    }
}
