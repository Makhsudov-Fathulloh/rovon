<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDebt extends Model
{
    use HasFactory;

    public const SOURCE_ORDER  = 1;
    public const SOURCE_MANUAL = 2;

    protected $table = 'user_debt';

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'currency',
        'source',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
