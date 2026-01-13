<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganizationUser extends Pivot
{
    protected $table = 'organization_user';

    protected $fillable = [
        'organization_id',
        'user_id',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
