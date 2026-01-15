<x-backend.layouts.main title="{!! 'Хомашёни янгилаш: ' . ucfirst($rawMaterialVariation->title) !!}">
    <div class="raw_material_variation-update">
        <x-backend.raw-material-variation.form
            :rawMaterialVariation="$rawMaterialVariation"
            :rawMaterialDropdown="$rawMaterialDropdown"
            :method="'PUT'"
            :action="route('raw-material-variation.update', $rawMaterialVariation->id)"
        />
    </div>
</x-backend.layouts.main>
