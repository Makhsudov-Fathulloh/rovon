<x-backend.layouts.main title="Навбатдаги буюртани яратиш">
    <div class="pre-order-create">
        <x-backend.pre-order.form
            :method="'POST'"
            :users="$users"
            :pre_order="$pre_order"
            :action="route('pre-order.store')"
        />
    </div>
</x-backend.layouts.main>
