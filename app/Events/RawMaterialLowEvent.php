<?php

namespace App\Events;

use App\Models\RawMaterialVariation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RawMaterialLowEvent
{
    use Dispatchable, SerializesModels;

    public RawMaterialVariation $rawMaterialVariation;

    public function __construct(RawMaterialVariation $rawMaterialVariation)
    {
        $this->rawMaterialVariation = $rawMaterialVariation;
    }
}
