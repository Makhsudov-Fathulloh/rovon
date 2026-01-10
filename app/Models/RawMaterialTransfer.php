<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialTransfer extends Model
{
    use HasFactory;

    protected $table = 'raw_material_transfer';

    protected $fillable = [
        'organization_id',
        'warehouse_id',
        'section_id',
        'shift_id',
        'shift_output_id',
        'title',
        'sender_id',
        'receiver_id',
        'total_item_price',
        'status',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function items()
    {
        return $this->hasMany(RawMaterialTransferItem::class, 'raw_material_transfer_id');
    }

    public function rawMaterialVariation()
    {
        return $this->hasManyThrough(
            RawMaterialVariation::class,
            RawMaterialTransferItem::class,
            'raw_material_transfer_id',      // RawMaterialTransferItemdagi foreign key
            'id',                            // RawMaterialVariation primary key
            'id',                            // RawMaterialTransfer primary key
            'raw_material_variation_id'      // RawMaterialTransferItemdagi RawMaterialVariation foreign key
        );
    }

    /** 🔁 Transfer umumiy summasi va item sonini qayta hisoblash */
    public function recalculateTotals()
    {
        $this->total_item_price = $this->items()->sum('total_price');
        $this->save();
    }
}
