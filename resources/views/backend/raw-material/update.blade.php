<x-backend.layouts.main title="{!! 'Хомашё турини янгилаш: ' . ucfirst($rawMaterial->title) !!}">
    <div class="raw-material-update">
        <x-backend.raw-material.form
            :warehouses="$warehouses"
            :rawMaterial="$rawMaterial"
            :categories="$categories"
            :method="'PUT'"
            :action="route('raw-material.update', $rawMaterial->id)"
        />
    </div>
</x-backend.layouts.main>
