<x-backend.layouts.main title="{{ 'Қайтишни янгилаш: ' . ucfirst($productReturn->title) }}">
    <div class="product-return-update">
        <x-backend.product-return.form
            :method="'PUT'"
            :variations="$variations"
            :productReturn="$productReturn"
            :action="route('product-return.update', $productReturn->id)" />
    </div>
</x-backend.layouts.main>
