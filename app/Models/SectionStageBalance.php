<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SectionStageBalance extends Model
{
    use HasFactory;

    protected $table = 'section_stage_balance';

    protected $fillable = [
        'organization_id',
        'section_id',
        'stage_id',
        'in_qty',
        'out_qty',
        'balance',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
}
