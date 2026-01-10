<x-backend.layouts.main title="{{ 'Хомашё турини яратиш' }}">
    <div class="raw-material-create">
        <x-backend.raw-material.form
            :method="'POST'"
            :warehouses="$warehouses"
            :rawMaterial="$rawMaterial"
            :categories="$categories"
            :action="route('raw-material.store')"
        />
    </div>
</x-backend.layouts.main>
