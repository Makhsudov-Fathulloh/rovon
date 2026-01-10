<x-backend.layouts.main title="{{ 'Навбатдаги буюртани янгилаш: ' . ucfirst(optional($pre_order->user)->username) }}">
<div class="pre-order-update">
        <x-backend.pre-order.form
            :method="'PUT'"
            :pre_order="$pre_order"
            :users="$users"
            :action="route('pre-order.update', $pre_order->id)"
        />
    </div>
</x-backend.layouts.main>
