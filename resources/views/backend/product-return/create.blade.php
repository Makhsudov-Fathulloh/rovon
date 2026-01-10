<x-backend.layouts.main title="{{ 'Махсулотни қайтариш' }}">
    <div class="product-return-create">
        <x-backend.product-return.form
            :method="'POST'"
            :variations="$variations"
            :action="route('product-return.store')" />
    </div>
</x-backend.layouts.main>
