<x-backend.layouts.main title="{{ 'Бўлим яратиш' }}">
    <div class="section-create">
        <x-backend.section.form
            :method="'POST'"
            :organizations="$organizations"
            :sections="$sections"
            :section="$section"
            :action="route('section.store')"
        />
    </div>
</x-backend.layouts.main>
