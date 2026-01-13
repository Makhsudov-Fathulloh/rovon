<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organization';

    protected $fillable = [
        'title',
        'description',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user')->withTimestamps();
    }

    public function section()
    {
        return $this->hasMany(Section::class);
    }

    public function warehouse()
    {
        return $this->belongsToMany(Warehouse::class, 'organization_warehouse', 'organization_id', 'warehouse_id')->withTimestamps();
    }
}
