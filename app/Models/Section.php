<?php

namespace App\Models;

use App\Scopes\ModeratorScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'section';

    protected $fillable = [
        'organization_id',
        'previous_id',
        'title',
        'description',
        'type',
        'status',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ModeratorScope());
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function prevSection()
    {
        return $this->belongsTo(self::class, 'previous_id');
    }

    public function nextSections()
    {
        return $this->hasMany(self::class, 'previous_id');
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function stages()
    {
        return $this->hasMany(Stage::class);
    }

    public function stage()
    {
        return $this->hasOne(Stage::class);
    }

    public function balances()
    {
        return $this->hasMany(SectionStageBalance::class, 'section_id');
    }
}
