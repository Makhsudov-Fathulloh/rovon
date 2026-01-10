<x-backend.layouts.main title="{{ 'Бўлим махсулотини янгилаш: ' . ucfirst($stage->title) }}">
    <div class="stage-update">
        <x-backend.stage.form
            :stage="$stage"
            :organizations="$organizations"
            :sections="$sections"
            :rawMaterialVariations="$rawMaterialVariations"
            :method="'PUT'"
            :action="route('stage.update', $stage->id)"
        />
    </div>
</x-backend.layouts.main>
