<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganizationWarehouse extends Pivot
{
    public $timestamps = false;

    protected $table = 'organization_warehouse';

    protected $fillable = [
        'organization_id',
        'warehouse_id',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
