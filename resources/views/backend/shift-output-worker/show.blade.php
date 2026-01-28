<x-backend.layouts.main title="{{ 'Смена ходимларини кўриш: ' . ucfirst($shiftOutputWorker->user->username) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-body">

            <x-backend.action route="shift-output-worker" :id="$shiftOutputWorker->id" :back="true" :edit="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>ID</th>
                    <td>{{ $shiftOutputWorker->id }}</td>
                </tr>
                <tr>
                    <th>Ходим</th>
                    <td>{{ optional($shiftOutputWorker->user)->username }}</td>
                </tr>
                <tr>
                    <th>Смена</th>
                    <td>{{ optional($shiftOutputWorker->shiftOutput->stage)->title }}</td>
                </tr>
                <tr>
                    <th>Смена махсулоти</th>
                    <td>{{ optional($shiftOutputWorker->shiftOutput->stage)->title }}</td>
                </tr>
                <tr>
                    <th>Микдори</th>
                    <td class="text-success fw-bold">{{ $shiftOutputWorker->stage_count }} та</td>
                </tr>
                <tr>
                    <th>Брак</th>
                    <td class="text-danger fw-bold">{{ $shiftOutputWorker->defect_amount }} кг</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $shiftOutputWorker->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $shiftOutputWorker->updated_at?->format('Y-m-d H:i') }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
