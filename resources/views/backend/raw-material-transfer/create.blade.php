<x-backend.layouts.main title="Хомашё трансфери яратиш">
    <x-backend.raw-material-transfer.create-form
        :method="'POST'"
        :organizations="$organizations"
        :warehouses="$warehouses"
        :sections="$sections"
        :shifts="$shifts"
        :users="$users"
        :rawMaterials="$rawMaterials"
        :usdRate="$usdRate"
        :action="route('raw-material-transfer.store')"
    />
</x-backend.layouts.main>
