<x-backend.layouts.main title="{{ 'Буюртма турини яратиш: ' . ucfirst($order->user->username) }}">
    <div class="order-item-create">
        <x-backend.order-item.form
            :method="'POST'"
            :order="$order"
            :variations="$variations"
            :currency="$currency"
            :exchangeRate="$exchangeRate"
            :items="$items"
            :action="url('admin/order/' . $order->id . '/items')"
        />
    </div>
</x-backend.layouts.main>
