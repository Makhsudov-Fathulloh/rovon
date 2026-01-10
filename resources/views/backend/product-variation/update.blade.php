<x-backend.layouts.main title="{!! 'Маҳсулотни янгилаш: ' . ucfirst($productVariation->title) !!}">
    <div class="product_variation-update">
        <x-backend.product-variation.form
            :productVariation="$productVariation"
            :productDropdown="$productDropdown"
            :method="'PUT'"
            :action="route('product-variation.update', $productVariation->id)"
        />
    </div>
</x-backend.layouts.main>
