<?php

namespace App\Events;

use App\Models\ProductVariation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductLowEvent
{
    use Dispatchable, SerializesModels;

    public ProductVariation $productVariation;

    public function __construct(ProductVariation $productVariation)
    {
        $this->productVariation = $productVariation;
    }
}
