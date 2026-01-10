<x-backend.layouts.main title="{{ 'Модел яратиш' }}">
    <div class="product-create">
        <x-backend.product.form
            :method="'POST'"
            :warehouses="$warehouses"
            :product="$product"
            :categories="$categories"
            :action="route('product.store')"
        />
    </div>
</x-backend.layouts.main>
