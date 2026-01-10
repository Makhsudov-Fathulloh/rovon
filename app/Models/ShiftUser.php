<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftUser extends Model
{
    use HasFactory;

    protected $table = 'shift_user';

    protected $fillable = [
        'shift_output_id',
        'user_id',
    ];

    public function shiftOutput()
    {
        return $this->belongsTo(ShiftOutput::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
