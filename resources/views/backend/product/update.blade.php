<x-backend.layouts.main title="{{ 'Моделни янгилаш: ' . ucfirst($product->title) }}">
    <div class="product-update">
        <x-backend.product.form
            :warehouses="$warehouses"
            :product="$product"
            :categories="$categories"
            :method="'PUT'"
            :action="route('product.update', $product->id)"
        />
    </div>
</x-backend.layouts.main>
