<x-backend.layouts.main title="{{ 'Смена яратиш' }}">
    <div class="shift-create">
        <x-backend.shift.form
            :method="'POST'"
            :organizations="$organizations"
            :sections="$sections"
            :users="$users"
            :shift="$shift"
            :action="route('shift.store')"
        />
    </div>
</x-backend.layouts.main>
