<x-backend.layouts.main title="{{ 'Бўлимни янгилаш: ' . ucfirst($shift->title) }}">
    <div class="shift-update">
        <x-backend.shift.form
            :shift="$shift"
            :organizations="$organizations"
            :sections="$sections"
            :users="$users"
            :method="'PUT'"
            :action="route('shift.update', $shift->id)"
        />
    </div>
</x-backend.layouts.main>
