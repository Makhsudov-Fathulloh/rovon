<x-backend.layouts.main title="{{ 'Смена махсулотини кўриш: ' . ucfirst($shiftOutput->shift->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-body">

            <x-backend.action route="shift-output" :id="$shiftOutput->id" :back="true" :edit="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>ID</th>
                    <td>{{ $shiftOutput->id }}</td>
                </tr>
                <tr>
                    <th>Филиал</th>
                    <td>{{ optional($shiftOutput->shift->section->organization)->title }}</td>
                </tr>
                <tr>
                    <th>Бўлим</th>
                    <td>{{ optional($shiftOutput->shift->section)->title }}</td>
                </tr>
                <tr>
                    <th>Смена</th>
                    <td>{{ optional($shiftOutput->shift)->title }}</td>
                </tr>
                <tr>
                    <th>Махсулот</th>
                    <td>{{ optional($shiftOutput->stage)->title }}</td>
                </tr>
                <tr>
                    <th>Микдори</th>
                    <td class="text-success fw-bold">{{ $shiftOutput->stage_count }} та</td>
                </tr>
                <tr>
                    <th>Брак</th>
                    <td class="text-danger fw-bold">{{ $shiftOutput->defect_amount }} кг</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $shiftOutput->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $shiftOutput->updated_at?->format('Y-m-d H:i') }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
