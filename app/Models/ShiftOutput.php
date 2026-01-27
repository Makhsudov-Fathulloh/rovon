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
        'source_stage_id',
        'source_section_id',
        'stage_count',
        'defect_amount',
    ];

    // protected static function booted()
    // {
    //     static::addGlobalScope(new \App\Scopes\ModeratorScope());

    //     static::creating(function ($output) {

    //         $stage = $output->stage;

    //         foreach ($stage->stageMaterials as $stageMaterial) {

    //             $required = $output->calculateRequired(
    //                 $stageMaterial,
    //                 $output->stage_count,
    //                 $output->defect_amount
    //             );

    //             $variation = $stageMaterial->rawMaterialVariation;

    //             if ($variation->count < $required) {
    //                 throw new \Exception(
    //                     "Хомашё йетарли эмас: {$variation->title}. "
    //                         . "Керак: {$required}, Омборда: {$variation->count}"
    //                 );
    //             }
    //         }
    //     });

    //     static::created(function ($output) {

    //         DB::transaction(function () use ($output) {

    //             $section = $output->stage->section;
    //             $stage   = $output->stage;
    //             $quantity     = $output->stage_count;
    //             $defect  = $output->defect_amount;

    //             $transfer = RawMaterialTransfer::create([
    //                 'organization_id'   => $stage->section->organization->id,
    //                 'warehouse_id'      => $stage->section->organization->warehouse->id,
    //                 'section_id'        => $stage->section_id,
    //                 'shift_id'          => $output->shift_id,
    //                 'shift_output_id'   => $output->id,       // <<< MUHIM
    //                 'title'             => 'Трансфер #' . $output->id,
    //                 'sender_id'         => auth()->id(),
    //                 'receiver_id'       => $stage->section->organization->user_id,
    //                 'total_item_price'  => 0,
    //                 'status'            => StatusService::STATUS_ACTIVE,
    //             ]);

    //             $total = 0;

    //             foreach ($stage->stageMaterials as $stageMaterial) {

    //                 $required = $output->calculateRequired(
    //                     $stageMaterial,
    //                     $output->stage_count,
    //                     $output->defect_amount
    //                 );

    //                 $price = $stageMaterial->rawMaterialVariation->price;
    //                 $itemTotal = $required * $price;

    //                 RawMaterialTransferItem::create([
    //                     'raw_material_transfer_id'  => $transfer->id,
    //                     'raw_material_variation_id' => $stageMaterial->raw_material_variation_id,
    //                     'count'                     => $required,
    //                     'unit'                      => $stageMaterial->unit,
    //                     'price'                     => $price,
    //                     'total_price'               => $itemTotal,
    //                 ]);

    //                 $stageMaterial->rawMaterialVariation->decrementStock($required);
    //                 $total += $itemTotal;
    //             }

    //             $transfer->update(['total_item_price' => $total]);
    //             $transfer->recalculateTotals();

    //             // 🔹 SECTION STAGE BALANCE
    //             $prevStageId = $stage->pre_stage_id;

    //             if ($section->previous_id) {    // 🔹 OLDINGI SECTION

    //                 $prevBalance = SectionStageBalance::firstOrCreate(
    //                     [
    //                         'organization_id' => $section->organization_id,
    //                         'section_id'      => $section->previous_id,
    //                         'stage_id'        => $prevStageId,
    //                     ],
    //                     [
    //                         'in_qty'  => 0,
    //                         'out_qty' => 0,
    //                         'balance' => 0,
    //                     ]
    //                 );

    //                 if ($prevBalance->balance < $quantity) {
    //                     throw new \Exception("Олдинги сеқтиорда(бўлим) йетарли махсулот йўқ");
    //                 }

    //                 $prevBalance->out_qty  += $quantity;
    //                 $prevBalance->balance  -= $quantity;
    //                 $prevBalance->save();
    //             }

    //             // 🔹 JORIY SECTION
    //             $balance = SectionStageBalance::firstOrCreate(
    //                 [
    //                     'organization_id' => $section->organization_id,
    //                     'section_id'      => $section->id,
    //                     'stage_id'        => $stage->id,
    //                 ],
    //                 [
    //                     'in_qty'  => 0,
    //                     'out_qty' => 0,
    //                     'balance' => 0,
    //                 ]
    //             );

    //             $balance->in_qty  += $quantity;
    //             $balance->balance += $quantity;
    //             $balance->save();
    //         });
    //     });

    //     static::updating(function ($output) {

    //         $oldQty  = $output->getOriginal('stage_count');
    //         $oldBrak = $output->getOriginal('defect_amount');

    //         $stage = $output->stage;
    //         $section = $stage->section;

    //         // 🔹 ESKI SECTION BALANSINI QAYTARAMIZ
    //         $prevStageId = $stage->pre_stage_id;

    //         if ($section->previous_id && $prevStageId) {
    //             $prevBalance = SectionStageBalance::firstOrCreate(
    //                 [
    //                     'organization_id' => $section->organization_id,
    //                     'section_id'      => $section->previous_id,
    //                     'stage_id'        => $prevStageId,
    //                 ],
    //                 [
    //                     'in_qty'  => 0,
    //                     'out_qty' => 0,
    //                     'balance' => 0,
    //                 ]
    //             );

    //             $prevBalance->out_qty  -= $oldQty;
    //             $prevBalance->balance  += $oldQty;
    //             $prevBalance->save();
    //         }

    //         // 🔹 JORIY SECTION BALANSINI QAYTARAMIZ
    //         $balance = SectionStageBalance::firstOrCreate(
    //             [
    //                 'organization_id' => $section->organization_id,
    //                 'section_id'      => $section->id,
    //                 'stage_id'        => $stage->id,
    //             ],
    //             [
    //                 'in_qty'  => 0,
    //                 'out_qty' => 0,
    //                 'balance' => 0,
    //             ]
    //         );

    //         $balance->in_qty  -= $oldQty;
    //         $balance->balance -= $oldQty;
    //         $balance->save();

    //         // eski xomashyoni qaytarish
    //         foreach ($stage->stageMaterials as $stageMaterial) {
    //             $oldRequired = $output->calculateRequired($stageMaterial, $oldQty, $oldBrak);
    //             $stageMaterial->rawMaterialVariation->incrementStock($oldRequired);
    //         }

    //         // yangisi yetadimi?
    //         foreach ($stage->stageMaterials as $stageMaterial) {

    //             $newRequired = $output->calculateRequired(
    //                 $stageMaterial,
    //                 $output->stage_count,
    //                 $output->defect_amount
    //             );

    //             $variation = $stageMaterial->rawMaterialVariation;

    //             if ($variation->count < $newRequired) {
    //                 throw new \Exception(
    //                     "Ўзгартириш мумкин эмас: {$variation->title} омборда етмайди. "
    //                         . "Керак: {$newRequired}, Омборда: {$variation->count}"
    //                 );
    //             }
    //         }
    //     });

    //     static::updated(function ($output) {

    //         DB::transaction(function () use ($output) {

    //             $stage = $output->stage;
    //             $section = $stage->section;


    //             // eski transferni o'chirish
    //             RawMaterialTransfer::where('shift_output_id', $output->id)->delete();

    //             // yangi transfer yaratish
    //             $transfer = RawMaterialTransfer::create([
    //                 'organization_id'   => $stage->section->organization->id,
    //                 'warehouse_id'      => $stage->section->organization->warehouse->id,
    //                 'section_id'        => $stage->section_id,
    //                 'shift_id'          => $output->shift_id,
    //                 'shift_output_id'   => $output->id,
    //                 'title'             => 'Трансфер #' . $output->id,
    //                 'sender_id'         => auth()->id(),
    //                 'receiver_id'       => $stage->section->organization->user_id,
    //                 'total_item_price'  => 0,
    //                 'status'            => StatusService::STATUS_ACTIVE,
    //             ]);

    //             $total = 0;

    //             foreach ($stage->stageMaterials as $stageMaterial) {

    //                 $required = $output->calculateRequired(
    //                     $stageMaterial,
    //                     $output->stage_count,
    //                     $output->defect_amount
    //                 );

    //                 $price = $stageMaterial->rawMaterialVariation->price;
    //                 $itemTotal = $required * $price;

    //                 RawMaterialTransferItem::create([
    //                     'raw_material_transfer_id'  => $transfer->id,
    //                     'raw_material_variation_id' => $stageMaterial->raw_material_variation_id,
    //                     'count'                     => $required,
    //                     'unit'                      => $stageMaterial->unit,
    //                     'price'                     => $price,
    //                     'total_price'               => $itemTotal,
    //                 ]);

    //                 $stageMaterial->rawMaterialVariation->decrementStock($required);
    //                 $total += $itemTotal;
    //             }

    //             $transfer->update(['total_item_price' => $total]);
    //             $transfer->recalculateTotals();

    //             // 🔹 OLDINGI SECTION BALANSINI YANGILASH
    //             $quantity = $output->stage_count;
    //             $prevStageId = $stage->pre_stage_id;

    //             if ($section->previous_id && $prevStageId) {
    //                 $prevBalance = SectionStageBalance::firstOrCreate(
    //                     [
    //                         'organization_id' => $section->organization_id,
    //                         'section_id'      => $section->previous_id,
    //                         'stage_id'        => $prevStageId,
    //                     ],
    //                     [
    //                         'in_qty'  => 0,
    //                         'out_qty' => 0,
    //                         'balance' => 0,
    //                     ]
    //                 );

    //                 if ($prevBalance->balance < $quantity) {
    //                     throw new \Exception("Олдинги сеқтиорда(бўлим) йетарли махсулот йўқ");
    //                 }

    //                 $prevBalance->out_qty  += $quantity;
    //                 $prevBalance->balance  -= $quantity;
    //                 $prevBalance->save();
    //             }

    //             // 🔹 JORIY SECTION BALANSINI YANGILASH
    //             $balance = SectionStageBalance::firstOrCreate(
    //                 [
    //                     'organization_id' => $section->organization_id,
    //                     'section_id'      => $section->id,
    //                     'stage_id'        => $stage->id,
    //                 ],
    //                 [
    //                     'in_qty'  => 0,
    //                     'out_qty' => 0,
    //                     'balance' => 0,
    //                 ]
    //             );

    //             $balance->in_qty  += $quantity;
    //             $balance->balance += $quantity;
    //             $balance->save();
    //         });
    //     });

    //     static::deleting(function ($output) {

    //         DB::transaction(function () use ($output) {

    //             $stage = $output->stage;
    //             $section = $stage->section;
    //             $quantity = $output->stage_count;

    //             // transfer va items
    //             $transfer = RawMaterialTransfer::where('shift_output_id', $output->id)->first();

    //             if ($transfer) {
    //                 foreach ($transfer->items as $item) {
    //                     $item->rawMaterialVariation->incrementStock($item->count);
    //                 }
    //                 $transfer->delete();
    //             }

    //             // 🔹 OLDINGI SECTION BALANSINI QAYTARISH
    //             $prevStageId = $stage->pre_stage_id;

    //             if ($section->previous_id && $prevStageId) {
    //                 $prevBalance = SectionStageBalance::firstOrCreate(
    //                     [
    //                         'organization_id' => $section->organization_id,
    //                         'section_id'      => $section->previous_id,
    //                         'stage_id'        => $prevStageId,
    //                     ],
    //                     [
    //                         'in_qty'  => 0,
    //                         'out_qty' => 0,
    //                         'balance' => 0,
    //                     ]
    //                 );

    //                 $prevBalance->out_qty  -= $quantity;
    //                 $prevBalance->balance  += $quantity;
    //                 $prevBalance->save();
    //             }

    //             // 🔹 JORIY SECTION BALANSINI QAYTARISH
    //             $balance = SectionStageBalance::firstOrCreate(
    //                 [
    //                     'organization_id' => $section->organization_id,
    //                     'section_id'      => $section->id,
    //                     'stage_id'        => $stage->id,
    //                 ],
    //                 [
    //                     'in_qty'  => 0,
    //                     'out_qty' => 0,
    //                     'balance' => 0,
    //                 ]
    //             );

    //             $balance->in_qty  -= $quantity;
    //             $balance->balance -= $quantity;
    //             $balance->save();
    //         });
    //     });
    // }

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\ModeratorScope());

        static::creating(function ($output) {
            $stage = $output->stage;

            if ($stage->defect_type === StatusService::DEFECT_RAW_MATERIAL) {
                // 1. Xomashyo yetarliligini tekshirish
                foreach ($stage->stageMaterials as $stageMaterial) {
                    $required = $output->calculateRequired($stageMaterial, $output->stage_count, $output->defect_amount);
                    $variation = $stageMaterial->rawMaterialVariation;

                    if ($variation->count < $required) {
                        throw new \Exception("Хомашё йетарли эмас: {$variation->title}. Омборда: {$variation->count}, Керак: {$required}");
                    }
                }
            }

            // 2. Yarim tayyor mahsulot (balans) tekshiruvi
            $sourceStageId = $output->source_stage_id ?? $stage->section->prevSection?->stages()->where('status', StatusService::STATUS_ACTIVE)->first()?->id;
            if ($sourceStageId) {
                $sourceSectionId = $output->source_section_id ?? $stage->section->previous_id;
                $sourceBalance = SectionStageBalance::where(['section_id' => $sourceSectionId, 'stage_id' => $sourceStageId])->first();

                if (($sourceBalance->balance ?? 0) < $output->stage_count) {
                    $sourceStageTitle = Stage::find($sourceStageId)?->title ?? 'Номаълум';

                    $balance = number_format($sourceBalance->balance, 2, '.', ' ');
                    $missingAmount = number_format($output->stage_count - $sourceBalance->balance, 2, '.', ' ');

                    throw new \Exception(
                        "Маҳсулот йтaрли эмас!\n" .
                            "Босқич: \"{$sourceStageTitle}\". \n" .
                            "Киритилган миқдор: \"{$output->stage_count}\". \n" .
                            "Мавжуд баланс: \"{$balance}\". \n" .
                            "Етишмаётгани: \"{$missingAmount}\"!"
                    );
                }
            }
        });

        static::created(function ($output) {
            DB::transaction(function () use ($output) {
                $stage = $output->stage;
                $section = $stage->section;

                // 1. MANBADAN (BALANSDAN) AYRISH - Ikkala holat uchun ham
                $sourceStageId = $output->source_stage_id ?? $stage->pre_stage_id;
                if ($sourceStageId) {
                    $sourceSectionId = $output->source_section_id ?? $section->previous_id;

                    // Agar brak turi "Oldingi bosqich" bo'lsa, balansdan brakni ham qo'shib ayiramiz
                    $deductionQty = ($stage->defect_type == StatusService::DEFECT_PREVIOUS_STAGE)
                        ? ($output->stage_count + $output->defect_amount)
                        : $output->stage_count;

                    SectionStageBalance::where(['section_id' => $sourceSectionId, 'stage_id' => $sourceStageId])
                        ->update([
                            'balance' => DB::raw("balance - $deductionQty"),
                            'out_qty' => DB::raw("out_qty + $deductionQty")
                        ]);
                }

                // 2. XOMASHYO BILAN ISHLASH - Faqat DEFECT_RAW_MATERIAL bo'lsa
                if ($stage->defect_type == StatusService::DEFECT_RAW_MATERIAL) {
                    $transfer = RawMaterialTransfer::create([
                        'organization_id' => $section->organization_id,
                        'warehouse_id'    => $section->organization->rawWarehouse()->first()->id,
                        'section_id'      => $stage->section_id,
                        'shift_id'        => $output->shift_id,
                        'shift_output_id' => $output->id,
                        'title'           => 'Трансфер #' . $output->id,
                        'sender_id'       => auth()->id(),
                        'receiver_id'     => $section->organization->users()->first()->id,
                        'status'          => StatusService::STATUS_ACTIVE,
                    ]);

                    $totalPrice = 0;
                    foreach ($stage->stageMaterials as $mat) {
                        // Bu yerda calculateRequired ichida brak hisobga olingan
                        $required = $output->calculateRequired($mat, $output->stage_count, $output->defect_amount);
                        $price = $mat->rawMaterialVariation->price;

                        RawMaterialTransferItem::create([
                            'raw_material_transfer_id' => $transfer->id,
                            'raw_material_variation_id' => $mat->raw_material_variation_id,
                            'count' => $required,
                            'unit' => $mat->unit,
                            'price' => $price,
                            'total_price' => $required * $price,
                        ]);

                        $mat->rawMaterialVariation->decrementStock($required);
                        $totalPrice += ($required * $price);
                    }
                    $transfer->update(['total_item_price' => $totalPrice]);
                }

                // 3. JORIY BOSQICh BALANSINI OSHIRISH (Tayyor mahsulot kirimi)
                $currBal = SectionStageBalance::firstOrCreate([
                    'organization_id' => $section->organization_id,
                    'section_id'      => $section->id,
                    'stage_id'        => $stage->id,
                ]);
                $currBal->increment('balance', $output->stage_count);
                $currBal->increment('in_qty', $output->stage_count);
            });
        });

        // static::updating(function ($output) {
        //     $stage = $output->stage;
        //     $section = $stage->section;

        //     // 3.1 Eski xomashyolarni omborga qaytarish (vaқтинча омборга қайтарамиз)
        //     $oldTransfer = RawMaterialTransfer::where('shift_output_id', $output->id)->first();
        //     if ($oldTransfer) {
        //         foreach ($oldTransfer->items as $item) {
        //             $item->rawMaterialVariation->increment('count', $item->count);
        //         }
        //     }

        //     // 3.2 Yangi xomashyo yetadimi? (Exception билан)
        //     foreach ($stage->stageMaterials as $stageMaterial) {
        //         $required = $output->calculateRequired(
        //             $stageMaterial,
        //             $output->stage_count,
        //             $output->defect_amount
        //         );
        //         $variation = $stageMaterial->rawMaterialVariation;

        //         if ($variation->count < $required) {
        //             $currentStock = number_format($variation->count, 3, '.', ' ');
        //             $needed = number_format($required, 3, '.', ' ');

        //             throw new \Exception(
        //                 "Янгилашда хатолик: Хомашё йетарли эмас!\n" .
        //                     "Хомашё: \"{$variation->title}\".\n" .
        //                     "Омборда мавжуд: \"{$currentStock}\".\n" .
        //                     "Керакли миқдор: \"{$needed}\"."
        //             );
        //         }
        //     }

        //     // 3.3 Eski manba balansini qaytarish
        //     $oldSrcStageId = $output->getOriginal('source_stage_id') ?? $stage->pre_stage_id;
        //     if ($oldSrcStageId) {
        //         $oldSrcSectionId = $output->getOriginal('source_section_id') ?? $section->previous_id;
        //         $oldPrevBal = SectionStageBalance::where(['section_id' => $oldSrcSectionId, 'stage_id' => $oldSrcStageId])->first();
        //         if ($oldPrevBal) {
        //             $oldPrevBal->increment('balance', $output->getOriginal('stage_count'));
        //             $oldPrevBal->decrement('out_qty', $output->getOriginal('stage_count'));
        //         }
        //     }

        //     // 3.4 Joriy balansdan eski miqdorni ayirish
        //     $currBal = SectionStageBalance::where(['section_id' => $section->id, 'stage_id' => $stage->id])->first();
        //     if ($currBal) {
        //         $currBal->decrement('balance', $output->getOriginal('stage_count'));
        //         $currBal->decrement('in_qty', $output->getOriginal('stage_count'));
        //     }

        //     // 3.5 ЯНГИ МАНБА БАЛАНСИНИ ТЕКШИРИШ (Янгиланган миқдор учун)
        //     $sourceStageId = $output->source_stage_id ?? $oldSrcStageId;
        //     if ($sourceStageId) {
        //         $sourceSectionId = $output->source_section_id ?? ($section->previous_id ?? null);
        //         $sourceBalance = SectionStageBalance::where(['section_id' => $sourceSectionId, 'stage_id' => $sourceStageId])->first();

        //         if (($sourceBalance->balance ?? 0) < $output->stage_count) {
        //             $sourceStageTitle = Stage::find($sourceStageId)?->title ?? 'Номаълум';
        //             $formattedBalance = number_format(($sourceBalance->balance ?? 0), 0, '', ' ');
        //             $missing = number_format(($output->stage_count - ($sourceBalance->balance ?? 0)), 0, '', ' ');

        //             throw new \Exception(
        //                 "Янгилашда хатолик: Маҳсулот етарли эмас!\n" .
        //                     "Манба босқич: \"{$sourceStageTitle}\".\n" .
        //                     "Янги миқдор: \"{$output->stage_count}\".\n" .
        //                     "Мавжуд баланс: \"{$formattedBalance}\".\n" .
        //                     "Етишмаётгани: \"{$missing}\"!"
        //             );
        //         }
        //     }
        // });

        // static::updated(function ($output) {
        //     DB::transaction(function () use ($output) {
        //         $stage = $output->stage;
        //         $section = $stage->section;

        //         // 4.1 Eski transferni o'chirish (Xomashyo updatingda qaytarilgan)
        //         RawMaterialTransfer::where('shift_output_id', $output->id)->delete();

        //         // 4.2 Yangi transfer va xomashyo ayirish (Created kabi)
        //         $transfer = RawMaterialTransfer::create([
        //             'organization_id' => $section->organization_id,
        //             'warehouse_id'    => $section->organization->rawWarehouse()->first()->id,
        //             'section_id'      => $stage->section_id,
        //             'shift_id'        => $output->shift_id,
        //             'shift_output_id' => $output->id,
        //             'title'           => 'Янгиланган трансфер #' . $output->id,
        //             'sender_id'         => auth()->id(),
        //             'receiver_id'       => $stage->section->organization->users()->first()->id,
        //             'status'          => StatusService::STATUS_ACTIVE,
        //         ]);

        //         $totalPrice = 0;

        //         foreach ($stage->stageMaterials as $stageMaterial) {

        //             $required = $output->calculateRequired(
        //                 $stageMaterial,
        //                 $output->stage_count,
        //                 $output->defect_amount
        //             );

        //             $price = $stageMaterial->rawMaterialVariation->price;

        //             RawMaterialTransferItem::create([
        //                 'raw_material_transfer_id'  => $transfer->id,
        //                 'raw_material_variation_id' => $stageMaterial->raw_material_variation_id,
        //                 'count'                     => $required,
        //                 'unit'                      => $stageMaterial->unit,
        //                 'price'                     => $price,
        //                 'total_price'               => $required * $price,
        //             ]);

        //             $stageMaterial->rawMaterialVariation->decrementStock($required);
        //             $totalPrice += ($required * $price);
        //         }
        //         $transfer->update(['total_item_price' => $totalPrice]);
        //         $transfer->recalculateTotals();

        //         // 4.3 Yangi manba balansini ayirish
        //         $newSrcStageId = $output->source_stage_id ?? $stage->pre_stage_id;
        //         if ($newSrcStageId) {
        //             $newSrcSectionId = $output->source_section_id ?? $section->previous_id;
        //             $newPrevBal = SectionStageBalance::where([
        //                 'organization_id' => $section->organization_id,
        //                 'section_id' => $newSrcSectionId,
        //                 'stage_id' => $newSrcStageId
        //             ])->first();

        //             $newPrevBal->decrement('balance', $output->stage_count);
        //             $newPrevBal->increment('out_qty', $output->stage_count);
        //         }

        //         // 4.4 Joriy balansni yangilash
        //         $currBal = SectionStageBalance::firstOrCreate([
        //             'organization_id' => $section->organization_id,
        //             'section_id'      => $section->id,
        //             'stage_id'        => $stage->id,
        //         ]);
        //         $currBal->increment('balance', $output->stage_count);
        //         $currBal->increment('in_qty', $output->stage_count);
        //     });
        // });

        // static::updating(function ($output) {
        //     $stage = $output->stage;

        //     // Farqni hisoblaymiz (Yangi miqdor - Eski miqdor)
        //     $diffQty = $output->stage_count - $output->getOriginal('stage_count');
        //     $diffDefect = $output->defect_amount - $output->getOriginal('defect_amount');

        //     // 1. Xomashyo tekshiruvi (faqat ko'payayotgan bo'lsa)
        //     if ($stage->defect_type === StatusService::DEFECT_RAW_MATERIAL && ($diffQty > 0 || $diffDefect > 0)) {
        //         foreach ($stage->stageMaterials as $mat) {
        //             $requiredNow = $output->calculateRequired($mat, $output->stage_count, $output->defect_amount);
        //             $wasRequired = $output->calculateRequired($mat, $output->getOriginal('stage_count'), $output->getOriginal('defect_amount'));
        //             $needMore = $requiredNow - $wasRequired;

        //             if ($mat->rawMaterialVariation->count < $needMore) {
        //                 throw new \Exception("Xomashyo yetarli emas: {$mat->rawMaterialVariation->title}");
        //             }
        //         }
        //     }

        //     // 2. Balans tekshiruvi
        //     $sourceStageId = $output->source_stage_id ?? $stage->pre_stage_id;
        //     if ($sourceStageId) {
        //         $sourceSectionId = $output->source_section_id ?? $stage->section->previous_id;
        //         $sourceBalance = SectionStageBalance::where(['section_id' => $sourceSectionId, 'stage_id' => $sourceStageId])->first();

        //         $deductionNow = ($stage->defect_type == StatusService::DEFECT_PREVIOUS_STAGE)
        //             ? ($output->stage_count + $output->defect_amount) : $output->stage_count;
        //         $deductionWas = ($stage->defect_type == StatusService::DEFECT_PREVIOUS_STAGE)
        //             ? ($output->getOriginal('stage_count') + $output->getOriginal('defect_amount')) : $output->getOriginal('stage_count');

        //         $diffDeduction = $deductionNow - $deductionWas;

        //         if (($sourceBalance->balance ?? 0) < $diffDeduction) {
        //             throw new \Exception("Manba balansida yetarli mahsulot yo'q!");
        //         }
        //     }
        // });

        static::updating(function ($output) {
            $stage = $output->stage;

            // 1. Xomashyo tekshiruvi (faqat xomashyo bo'limi bo'lsa)
            if ($stage->defect_type === StatusService::DEFECT_RAW_MATERIAL) {
                foreach ($stage->stageMaterials as $mat) {
                    $variation = $mat->rawMaterialVariation;

                    $wasRequired = $output->calculateRequired($mat, $output->getOriginal('stage_count'), $output->getOriginal('defect_amount'));
                    $newRequired = $output->calculateRequired($mat, $output->stage_count, $output->defect_amount);

                    $diff = $newRequired - $wasRequired;

                    // Agar yangi miqdor ko'p bo'lsa, ombor tahlili
                    if ($diff > 0 && $variation->count < $diff) {
                        throw new \Exception(
                            "Янгилашда хатолик: Хомашё йетарли эмас!\n" .
                                "Хомашё: \"{$variation->title}\".\n" .
                                "Омборда мавжуд: \"{$variation->count}\".\n" .
                                "Керакли миқдор: \"{$diff}\"."
                        );
                    }
                }
            }

            // 2. Balans tekshiruvi (Oldingi bosqichdan kelayotgan bo'lsa)
            $sourceStageId = $output->source_stage_id ?? $stage->pre_stage_id;
            if ($sourceStageId) {
                $sourceSectionId = $output->source_section_id ?? $stage->section->previous_id;
                $sourceBalance = SectionStageBalance::where(['section_id' => $sourceSectionId, 'stage_id' => $sourceStageId])->first();

                $deductionNow = ($stage->defect_type == StatusService::DEFECT_PREVIOUS_STAGE)
                    ? ($output->stage_count + $output->defect_amount) : $output->stage_count;
                $deductionWas = ($stage->defect_type == StatusService::DEFECT_PREVIOUS_STAGE)
                    ? ($output->getOriginal('stage_count') + $output->getOriginal('defect_amount')) : $output->getOriginal('stage_count');

                $diffDeduction = $deductionNow - $deductionWas;

                if ($diffDeduction > 0 && ($sourceBalance->balance ?? 0) < $diffDeduction) {
                    throw new \Exception("Янгилашда хатолик: Манба балансида етарли маҳсулот йўқ!");
                }
            }
        });

        static::updated(function ($output) {
            DB::transaction(function () use ($output) {
                $stage = $output->stage;
                $section = $stage->section;

                // A. ESKI HOLATNI QAYTARISH (Rollback)
                // 1. Balansni qaytarish (source)
                if ($output->getOriginal('source_stage_id')) {
                    $wasDeduction = ($stage->defect_type == StatusService::DEFECT_PREVIOUS_STAGE)
                        ? ($output->getOriginal('stage_count') + $output->getOriginal('defect_amount'))
                        : $output->getOriginal('stage_count');

                    SectionStageBalance::where([
                        'section_id' => $output->getOriginal('source_section_id'),
                        'stage_id' => $output->getOriginal('source_stage_id')
                    ])->update([
                        'balance' => DB::raw("balance + $wasDeduction"),
                        'out_qty' => DB::raw("out_qty - $wasDeduction")
                    ]);
                }
                // 2. Xomashyo va Transferni qaytarish
                $oldTransfer = RawMaterialTransfer::where('shift_output_id', $output->id)->first();
                if ($oldTransfer) {
                    foreach ($oldTransfer->items as $item) {
                        $item->rawMaterialVariation->incrementStock($item->count);
                    }
                    $oldTransfer->items()->delete();
                    $oldTransfer->delete();
                }
                // 3. Joriy balansni kamaytirish
                SectionStageBalance::where(['stage_id' => $stage->id])
                    ->decrement('balance', $output->getOriginal('stage_count'));
                SectionStageBalance::where(['stage_id' => $stage->id])
                    ->decrement('in_qty', $output->getOriginal('stage_count'));


                // B. YANGI HOLATNI QO'LLASH (Yangi transfer va balans)
                $sourceStageId = $output->source_stage_id;
                if ($sourceStageId) {
                    $newDeduction = ($stage->defect_type == StatusService::DEFECT_PREVIOUS_STAGE)
                        ? ($output->stage_count + $output->defect_amount) : $output->stage_count;

                    SectionStageBalance::where(['section_id' => $output->source_section_id, 'stage_id' => $sourceStageId])
                        ->update([
                            'balance' => DB::raw("balance - $newDeduction"),
                            'out_qty' => DB::raw("out_qty + $newDeduction")
                        ]);
                }

                if ($stage->defect_type == StatusService::DEFECT_RAW_MATERIAL) {
                    // Yangi transfer yaratish
                    $transfer = RawMaterialTransfer::create([
                        'organization_id' => $section->organization_id,
                        'warehouse_id'    => $section->organization->rawWarehouse()->first()->id,
                        'section_id'      => $stage->section_id,
                        'shift_id'        => $output->shift_id,
                        'shift_output_id' => $output->id,
                        'title'           => 'Трансфер (Янгиланган) #' . $output->id,
                        'sender_id'       => auth()->id(),
                        'receiver_id'     => $section->organization->users()->first()->id,
                        'status'          => StatusService::STATUS_ACTIVE,
                    ]);

                    $totalPrice = 0;
                    foreach ($stage->stageMaterials as $mat) {
                        $required = $output->calculateRequired($mat, $output->stage_count, $output->defect_amount);
                        $price = $mat->rawMaterialVariation->price;

                        RawMaterialTransferItem::create([
                            'raw_material_transfer_id' => $transfer->id,
                            'raw_material_variation_id' => $mat->raw_material_variation_id,
                            'count' => $required,
                            'unit' => $mat->unit,
                            'price' => $price,
                            'total_price' => $required * $price,
                        ]);
                        $mat->rawMaterialVariation->decrementStock($required);
                        $totalPrice += ($required * $price);
                    }
                    $transfer->update(['total_item_price' => $totalPrice]);
                }

                // Joriy balansga qo'shish
                $currBal = SectionStageBalance::where(['stage_id' => $stage->id])->first();
                $currBal->increment('balance', $output->stage_count);
                $currBal->increment('in_qty', $output->stage_count);
            });
        });

        static::deleted(function ($output) {
            DB::transaction(function () use ($output) {
                $stage = $output->stage;

                // 1. Manba balansini qaytarish
                if ($output->source_stage_id) {
                    $deduction = ($stage->defect_type == StatusService::DEFECT_PREVIOUS_STAGE)
                        ? ($output->stage_count + $output->defect_amount) : $output->stage_count;

                    SectionStageBalance::where(['section_id' => $output->source_section_id, 'stage_id' => $output->source_stage_id])
                        ->update([
                            'balance' => DB::raw("balance + $deduction"),
                            'out_qty' => DB::raw("out_qty - $deduction")
                        ]);
                }

                // 2. Xomashyoni omborga qaytarish
                $transfer = RawMaterialTransfer::where('shift_output_id', $output->id)->first();
                if ($transfer) {
                    foreach ($transfer->items as $item) {
                        $item->rawMaterialVariation->incrementStock($item->count);
                    }
                    $transfer->items()->delete();
                    $transfer->delete();
                }

                // 3. Joriy balansdan ayirish
                SectionStageBalance::where(['stage_id' => $stage->id])
                    ->update([
                        'balance' => DB::raw("balance - $output->stage_count"),
                        'in_qty' => DB::raw("in_qty - $output->stage_count")
                    ]);
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


    // Smena bo'yicha jami stage_count va defect_amount ni hisoblaydi
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
