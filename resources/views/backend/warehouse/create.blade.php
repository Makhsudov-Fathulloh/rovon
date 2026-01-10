<x-backend.layouts.main title="{{ 'Омбор яратиш' }}">
    <div class="warehouse-create">
        <x-backend.warehouse.form
            :method="'POST'"
            :organizations="$organizations"
            :warehouse="$warehouse"
            :action="route('warehouse.store')"
        />
    </div>
</x-backend.layouts.main>
