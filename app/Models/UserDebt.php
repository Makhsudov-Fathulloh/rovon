<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDebt extends Model
{
    use HasFactory;

    protected $table = 'user_debt';

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'currency',
    ];

    /**
     * Foydalanuvchiga bog‘lanish
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


