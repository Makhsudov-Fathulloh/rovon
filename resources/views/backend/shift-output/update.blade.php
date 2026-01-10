<x-backend.layouts.main title="Смена махсулотини янгилаш: {!! $shiftOutput->stage->title .  ' (' .$shiftOutput->shift->title. ')' !!}">
    <div class="shift-output-update">
        <x-backend.shift-output.form2
            :sections="[]"
            :shifts="[]"
            :shift="$shiftOutput->shift"
            :shiftOutput="$shiftOutput"
            :stage="$stage"
            :workers="$workers"
            :oldInputs="$oldInputs"
            :workerDefects="$workerDefects"
            :action="route('shift-output.update', $shiftOutput->id)"
            :method="'PUT'"
        />
    </div>
</x-backend.layouts.main>
