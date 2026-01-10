<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRates extends Model
{
    protected $fillable = [
        'currency',
        'rate'
    ];
}
