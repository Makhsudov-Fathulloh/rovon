<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class File extends Model
{
    use HasFactory;

    protected $table = 'file';

    protected $fillable = [
        'name',
        'title',
        'description',
        'path',
        'caption',
        'extension',
        'mime_type',
        'size',
        'date_create',
        'user_id',
    ];

    protected static function booted()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
