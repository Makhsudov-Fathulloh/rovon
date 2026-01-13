<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ShiftUser extends Pivot
{
    protected $table = 'shift_user';

    protected $fillable = [
        'shift_id',
        'user_id',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
