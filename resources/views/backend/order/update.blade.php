<x-backend.layouts.main title="{{ 'Буюртма янгилаш: ' . ucfirst($order->user->username) }}">
    <div class="order-update">
        <x-backend.order.form
            :users="$users"
            :clientRoleId="$clientRoleId"
            :order="$order"
            :variations="$variations"
            :method="'PUT'"
            :currentCurrency="$currentCurrency"
            :currencyLabel="$currencyLabel"
            :oldItems="$oldItems"
            :totalPriceValue="$totalPriceValue"
            :totalPaidValue="$totalPaidValue"
            :remainingDebtValue="$remainingDebtValue"
            :cashPaidValue="$cashPaidValue"
            :cardPaidValue="$cardPaidValue"
            :transferPaidValue="$transferPaidValue"
            :bankPaidValue="$bankPaidValue"
            :action="route('order.update', $order->id)"
        />
    </div>
</x-backend.layouts.main>
