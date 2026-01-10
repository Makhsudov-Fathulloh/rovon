<x-backend.layouts.main title="{{ 'Омборни янгилаш: ' . ucfirst($warehouse->title) }}">
    <div class="warehouse-update">
        <x-backend.warehouse.form
            :organizations="$organizations"
            :warehouse="$warehouse"
            :method="'PUT'"
            :action="route('warehouse.update', $warehouse->id)"
        />
    </div>
</x-backend.layouts.main>
