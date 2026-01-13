<x-backend.layouts.main title="{{ 'Буюртма яратиш' }}">
    <div class="order-create">
        <x-backend.order.form
            :method="'POST'"
            :users="$users"
            :clientRoleId="$clientRoleId"
            :variations="$variations"
            :defaultUserId="$defaultUserId"
            :currentCurrency="$currentCurrency"
            :currencyLabel="$currencyLabel"
            :oldItems="$oldItems"
            :order="$order"
            :totalPriceValue="$totalPriceValue"
            :cashPaidValue="$cashPaidValue"
            :cardPaidValue="$cardPaidValue"
            :transferPaidValue="$transferPaidValue"
            :bankPaidValue="$bankPaidValue"
            :totalPaidValue="$totalPaidValue"
            :remainingDebtValue="$remainingDebtValue"
            :action="route('order.store')"
        />
    </div>
</x-backend.layouts.main>
