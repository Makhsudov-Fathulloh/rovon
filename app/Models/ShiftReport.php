<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftReport extends Model
{
    const SHIFT_OPEN = 1;
    const SHIFT_CLOSE = 2;

    protected $table = 'shift_report';

    protected $fillable = [
        'report_date',
        'organization_id',
        'section_id',
        'shift_id',
        'stage_product',
        'defect_amount',
        'status',
    ];

    protected $casts = [
        'stage_product' => 'array',
    ];


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

    public function isOpen()
    {
        return $this->status === $this::SHIFT_OPEN;
    }

    public function isClose()
    {
        return $this->status === $this::SHIFT_CLOSE;
    }
}
