<x-backend.layouts.main title="{!! 'Хомашё трансфери таҳрирлаш: ' . ucfirst($rawMaterialTransfer->title) !!}">
    <x-backend.raw-material-transfer.update-form
        :method="'PUT'"
        :organizations="$organizations"
        :warehouses="$warehouses"
        :sections="$sections"
        :shifts="$shifts"
        :users="$users"
        :rawMaterials="$rawMaterials"
        :rawMaterialTransfer="$rawMaterialTransfer"
        :usdRate="$usdRate"
        :action="route('raw-material-transfer.update', $rawMaterialTransfer->id)"
    />
</x-backend.layouts.main>
