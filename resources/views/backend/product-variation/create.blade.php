<x-backend.layouts.main title="{!! 'Маҳсулот яратиш: ' . ucfirst($product->title) !!}">
    <div class="product_variation-create">
        <x-backend.product-variation.form
            :method="'POST'"
            :productVariation="$productVariation"
            :action="url('admin/product/' . $product->id . '/variations')"
        />
    </div>
</x-backend.layouts.main>
