<x-backend.layouts.main title="{!! 'Бўлимни янгилаш: ' . ucfirst($section->title) !!}">
    <div class="section-update">
        <x-backend.section.form
            :organizations="$organizations"
            :sections="$sections"
            :section="$section"
            :method="'PUT'"
            :action="route('section.update', $section->id)"
        />
    </div>
</x-backend.layouts.main>
