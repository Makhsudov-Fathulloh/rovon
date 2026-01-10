<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftOutputWorker extends Model
{
    use HasFactory;

    protected $table = 'shift_output_worker';

    protected $fillable = [
        'shift_output_id',
        'user_id',
        'stage_count',
        'defect_amount',
        'price',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());
    }

    public function shiftOutput()
    {
        return $this->belongsTo(ShiftOutput::class, 'shift_output_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
