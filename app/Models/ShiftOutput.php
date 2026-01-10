<?php

namespace App\Models;

use App\Services\StatusService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShiftOutput extends Model
{
    use HasFactory;

    protected $table = 'shift_output';

    protected $fillable = [
        'shift_id',
        'stage_id',
        'stage_count',
        'defect_amount',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());

        /*
      |--------------------------------------------------------------------------
      | 1) CREATE — YARATILISHIDAN OLDIN XOMASHYO YETARLIMI?
      |--------------------------------------------------------------------------
      */
        static::creating(function ($output) {

            $stage = $output->stage;

            foreach ($stage->stageMaterials as $stageMaterial) {

                $required = $output->calculateRequired(
                    $stageMaterial,
                    $output->stage_count,
                    $output->defect_amount
                );

                $variation = $stageMaterial->rawMaterialVariation;

                if ($variation->count < $required) {
                    throw new \Exception(
                        "Хомашё йетарли эмас: {$variation->title}. "
                            . "Керак: {$required}, Омборда: {$variation->count}"
                    );
                }
            }
        });

        /*
      |--------------------------------------------------------------------------
      | 2) CREATED — XOMASHYO AYIRAMIZ VA TRANSFER YARATAMIZ
      |--------------------------------------------------------------------------
      */
        static::created(function ($output) {

            DB::transaction(function () use ($output) {

                $section = $output->stage->section;
                $stage   = $output->stage;
                $quantity     = $output->stage_count;
                $defect  = $output->defect_amount;

                $transfer = RawMaterialTransfer::create([
                    'organization_id'   => $stage->section->organization->id,
                    'warehouse_id'      => $stage->section->organization->warehouse->id,
                    'section_id'        => $stage->section_id,
                    'shift_id'          => $output->shift_id,
                    'shift_output_id'   => $output->id,       // <<< MUHIM
                    'title'             => 'Трансфер #' . $output->id,
                    'sender_id'         => auth()->id(),
                    'receiver_id'       => $stage->section->organization->user_id,
                    'total_item_price'  => 0,
                    'status'            => StatusService::STATUS_ACTIVE,
                ]);

                $total = 0;

                foreach ($stage->stageMaterials as $stageMaterial) {

                    $required = $output->calculateRequired(
                        $stageMaterial,
                        $output->stage_count,
                        $output->defect_amount
                    );

                    $price = $stageMaterial->rawMaterialVariation->price;
                    $itemTotal = $required * $price;

                    RawMaterialTransferItem::create([
                        'raw_material_transfer_id'  => $transfer->id,
                        'raw_material_variation_id' => $stageMaterial->raw_material_variation_id,
                        'count'                     => $required,
                        'unit'                      => $stageMaterial->unit,
                        'price'                     => $price,
                        'total_price'               => $itemTotal,
                    ]);

                    $stageMaterial->rawMaterialVariation->decrementStock($required);
                    $total += $itemTotal;
                }

                $transfer->update(['total_item_price' => $total]);
                $transfer->recalculateTotals();

                // 🔹 SECTION STAGE BALANCE
                $prevStageId = $stage->pre_stage_id;

                if ($section->previous_id) {    // 🔹 OLDINGI SECTION

                    $prevBalance = SectionStageBalance::firstOrCreate(
                        [
                            'organization_id' => $section->organization_id,
                            'section_id'      => $section->previous_id,
                            'stage_id'        => $prevStageId,
                        ],
                        [
                            'in_qty'  => 0,
                            'out_qty' => 0,
                            'balance' => 0,
                        ]
                    );

                    if ($prevBalance->balance < $quantity) {
                        throw new \Exception("Олдинги сеқтиорда(бўлим) йетарли махсулот йўқ");
                    }

                    $prevBalance->out_qty  += $quantity;
                    $prevBalance->balance  -= $quantity;
                    $prevBalance->save();
                }

                // 🔹 JORIY SECTION
                $balance = SectionStageBalance::firstOrCreate(
                    [
                        'organization_id' => $section->organization_id,
                        'section_id'      => $section->id,
                        'stage_id'        => $stage->id,
                    ],
                    [
                        'in_qty'  => 0,
                        'out_qty' => 0,
                        'balance' => 0,
                    ]
                );

                $balance->in_qty  += $quantity;
                $balance->balance += $quantity;
                $balance->save();
            });
        });

        /*
      |--------------------------------------------------------------------------
      | 3) UPDATING — ESKI SARFNI QAYTARAMIZ, YANGISIGA YETADIMI TEKSHIRAMIZ
      |--------------------------------------------------------------------------
      */
        static::updating(function ($output) {

            $oldQty  = $output->getOriginal('stage_count');
            $oldBrak = $output->getOriginal('defect_amount');

            $stage = $output->stage;
            $section = $stage->section;

            // 🔹 ESKI SECTION BALANSINI QAYTARAMIZ
            $prevStageId = $stage->pre_stage_id;

            if ($section->previous_id && $prevStageId) {
                $prevBalance = SectionStageBalance::firstOrCreate(
                    [
                        'organization_id' => $section->organization_id,
                        'section_id'      => $section->previous_id,
                        'stage_id'        => $prevStageId,
                    ],
                    [
                        'in_qty'  => 0,
                        'out_qty' => 0,
                        'balance' => 0,
                    ]
                );

                $prevBalance->out_qty  -= $oldQty;
                $prevBalance->balance  += $oldQty;
                $prevBalance->save();
            }

            // 🔹 JORIY SECTION BALANSINI QAYTARAMIZ
            $balance = SectionStageBalance::firstOrCreate(
                [
                    'organization_id' => $section->organization_id,
                    'section_id'      => $section->id,
                    'stage_id'        => $stage->id,
                ],
                [
                    'in_qty'  => 0,
                    'out_qty' => 0,
                    'balance' => 0,
                ]
            );

            $balance->in_qty  -= $oldQty;
            $balance->balance -= $oldQty;
            $balance->save();

            // eski xomashyoni qaytarish
            foreach ($stage->stageMaterials as $stageMaterial) {
                $oldRequired = $output->calculateRequired($stageMaterial, $oldQty, $oldBrak);
                $stageMaterial->rawMaterialVariation->incrementStock($oldRequired);
            }

            // yangisi yetadimi?
            foreach ($stage->stageMaterials as $stageMaterial) {

                $newRequired = $output->calculateRequired(
                    $stageMaterial,
                    $output->stage_count,
                    $output->defect_amount
                );

                $variation = $stageMaterial->rawMaterialVariation;

                if ($variation->count < $newRequired) {
                    throw new \Exception(
                        "Ўзгартириш мумкин эмас: {$variation->title} омборда етмайди. "
                            . "Керак: {$newRequired}, Омборда: {$variation->count}"
                    );
                }
            }
        });

        /*
      |--------------------------------------------------------------------------
      | 4) UPDATED — ESKI TRANSFERNI O‘CHIRIB, YANGISINI YOZAMIZ
      |--------------------------------------------------------------------------
      */
        static::updated(function ($output) {

            DB::transaction(function () use ($output) {

                $stage = $output->stage;
                $section = $stage->section;


                // eski transferni o'chirish
                RawMaterialTransfer::where('shift_output_id', $output->id)->delete();

                // yangi transfer yaratish
                $transfer = RawMaterialTransfer::create([
                    'organization_id'   => $stage->section->organization->id,
                    'warehouse_id'      => $stage->section->organization->warehouse->id,
                    'section_id'        => $stage->section_id,
                    'shift_id'          => $output->shift_id,
                    'shift_output_id'   => $output->id,
                    'title'             => 'Трансфер #' . $output->id,
                    'sender_id'         => auth()->id(),
                    'receiver_id'       => $stage->section->organization->user_id,
                    'total_item_price'  => 0,
                    'status'            => StatusService::STATUS_ACTIVE,
                ]);

                $total = 0;

                foreach ($stage->stageMaterials as $stageMaterial) {

                    $required = $output->calculateRequired(
                        $stageMaterial,
                        $output->stage_count,
                        $output->defect_amount
                    );

                    $price = $stageMaterial->rawMaterialVariation->price;
                    $itemTotal = $required * $price;

                    RawMaterialTransferItem::create([
                        'raw_material_transfer_id'  => $transfer->id,
                        'raw_material_variation_id' => $stageMaterial->raw_material_variation_id,
                        'count'                     => $required,
                        'unit'                      => $stageMaterial->unit,
                        'price'                     => $price,
                        'total_price'               => $itemTotal,
                    ]);

                    $stageMaterial->rawMaterialVariation->decrementStock($required);
                    $total += $itemTotal;
                }

                $transfer->update(['total_item_price' => $total]);
                $transfer->recalculateTotals();

                // 🔹 OLDINGI SECTION BALANSINI YANGILASH
                $quantity = $output->stage_count;
                $prevStageId = $stage->pre_stage_id;

                if ($section->previous_id && $prevStageId) {
                    $prevBalance = SectionStageBalance::firstOrCreate(
                        [
                            'organization_id' => $section->organization_id,
                            'section_id'      => $section->previous_id,
                            'stage_id'        => $prevStageId,
                        ],
                        [
                            'in_qty'  => 0,
                            'out_qty' => 0,
                            'balance' => 0,
                        ]
                    );

                    if ($prevBalance->balance < $quantity) {
                        throw new \Exception("Олдинги сеқтиорда(бўлим) йетарли махсулот йўқ");
                    }

                    $prevBalance->out_qty  += $quantity;
                    $prevBalance->balance  -= $quantity;
                    $prevBalance->save();
                }

                // 🔹 JORIY SECTION BALANSINI YANGILASH
                $balance = SectionStageBalance::firstOrCreate(
                    [
                        'organization_id' => $section->organization_id,
                        'section_id'      => $section->id,
                        'stage_id'        => $stage->id,
                    ],
                    [
                        'in_qty'  => 0,
                        'out_qty' => 0,
                        'balance' => 0,
                    ]
                );

                $balance->in_qty  += $quantity;
                $balance->balance += $quantity;
                $balance->save();
            });
        });

        /*
      |--------------------------------------------------------------------------
      | 5) DELETING — TRANSFERNI TOPIB, XOMASHYONI QAYTARAMIZ
      |--------------------------------------------------------------------------
      */
        static::deleting(function ($output) {

            DB::transaction(function () use ($output) {

                $stage = $output->stage;
                $section = $stage->section;
                $quantity = $output->stage_count;

                // transfer va items
                $transfer = RawMaterialTransfer::where('shift_output_id', $output->id)->first();

                if ($transfer) {
                    foreach ($transfer->items as $item) {
                        $item->rawMaterialVariation->incrementStock($item->count);
                    }
                    $transfer->delete();
                }

                // 🔹 OLDINGI SECTION BALANSINI QAYTARISH
                $prevStageId = $stage->pre_stage_id;

                if ($section->previous_id && $prevStageId) {
                    $prevBalance = SectionStageBalance::firstOrCreate(
                        [
                            'organization_id' => $section->organization_id,
                            'section_id'      => $section->previous_id,
                            'stage_id'        => $prevStageId,
                        ],
                        [
                            'in_qty'  => 0,
                            'out_qty' => 0,
                            'balance' => 0,
                        ]
                    );

                    $prevBalance->out_qty  -= $quantity;
                    $prevBalance->balance  += $quantity;
                    $prevBalance->save();
                }

                // 🔹 JORIY SECTION BALANSINI QAYTARISH
                $balance = SectionStageBalance::firstOrCreate(
                    [
                        'organization_id' => $section->organization_id,
                        'section_id'      => $section->id,
                        'stage_id'        => $stage->id,
                    ],
                    [
                        'in_qty'  => 0,
                        'out_qty' => 0,
                        'balance' => 0,
                    ]
                );

                $balance->in_qty  -= $quantity;
                $balance->balance -= $quantity;
                $balance->save();
            });
        });
    }


    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function shiftOutputWorkers()
    {
        return $this->hasMany(ShiftOutputWorker::class);
    }

    public function calculateRequired($material, $productQty, $brak)
    {
        // 'count' - 1 ta product uchun sarf (stageMaterial.count)
        $main = $material->count * $productQty;

        // barcha recipe miqdorlari yig‘indisi (count bo'yicha)
        $totalRecipe = $this->stage->stageMaterials->sum('count');

        // brak ulushi bo‘yicha
        $ratio = $material->count / $totalRecipe;

        $brakUsed = $brak * $ratio;

        return $main + $brakUsed;
    }

    /**
     * Smena bo'yicha jami stage_count va defect_amount ni hisoblaydi
     */
    public static function getShiftReport($shiftId)
    {
        $shift = Shift::with('shiftOutputs')->find($shiftId);

        if (!$shift) {
            return null; // yoki xato xabar
        }

        $totalStageCount = $shift->shiftOutputs->sum('stage_count');
        $totalDefectAmount = $shift->shiftOutputs->sum('defect_amount');

        return [
            'shift_title' => $shift->title,
            'total_stage_count' => $totalStageCount,
            'total_defect_amount' => $totalDefectAmount,
        ];
    }
}
