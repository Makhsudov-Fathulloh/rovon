<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shift';

    public $timestamps = false;

    protected $fillable = [
        'section_id',
        'title',
        'description',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'string',
        'ended_at'   => 'string',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());
    }

    public function getOrganizationAttribute()
    {
        return $this->section?->organization;
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'shift_user', 'shift_id', 'user_id')->withTimestamps();
    }

    public function shiftOutputs()
    {
        return $this->hasMany(ShiftOutput::class);
    }
}
