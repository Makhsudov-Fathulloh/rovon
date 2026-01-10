<x-backend.layouts.main title="{{ 'Хомашё яратиш: ' . ucfirst($rawMaterial->title) }}">
    <div class="raw_material_variation-create">
        <x-backend.raw-material-variation.form
            :method="'POST'"
            :rawMaterialVariation="$rawMaterialVariation"
            :action="url('admin/raw-material/' . $rawMaterial->id . '/variations')"
        />
    </div>
</x-backend.layouts.main>


