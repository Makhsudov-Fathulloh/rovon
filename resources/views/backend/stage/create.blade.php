<x-backend.layouts.main title="{{ 'Бўлим махсулотини яратиш' }}">
    <div class="stage-create">
        <x-backend.stage.form
            :method="'POST'"
            :organizations="$organizations"
            :rawMaterialVariations="$rawMaterialVariations"
            :stage="$stage"
            :action="route('stage.store')"
        />
    </div>
</x-backend.layouts.main>
