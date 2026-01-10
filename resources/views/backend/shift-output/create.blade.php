<x-backend.layouts.main title="{{ 'Смена махсулотини яратиш: '. ucfirst($shift->title) }}">
    <div class="shift-output-create">
        <x-backend.shift-output.form
            :method="'POST'"
            :sections="$sections"
            :shifts="$shifts"
            :shift="$shift"
            :stages="$stages"
            :workers="$workers"
            :action="url('admin/shift/' . $shift->id . '/outputs')"
        />
    </div>
</x-backend.layouts.main>
