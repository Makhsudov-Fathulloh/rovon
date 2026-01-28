<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefectReport extends Model
{
    use HasFactory;

    protected $table = 'defect_report';

    protected $fillable = [
        'organization_id',
        'section_id',
        'shift_id',
        'user_id',
        'stage_id',
        'stage_count',
        'defect_amount',
        'total_defect_amount',
        'defect_type',
        'defect_percent',
    ];

    protected $casts = [
        'started_at' => 'string',
        'ended_at'   => 'string',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
}


