<x-backend.layouts.main title="{{ 'Брак ҳисоботини кўриш: ' . ucfirst($defectReport->stage->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header">
            {{--            <h1>{{ $section->title[app()->getLocale()] ?? $section->title }}</h1>--}}
        </div>
        <div class="card-body">

            <x-backend.action route="shift-output" :id="$defectReport->id" :back="true" :edit="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>ID</th>
                    <td>{{ $defectReport->id }}</td>
                </tr>
                <tr>
                    <th>Филиал</th>
                    <td>{{ optional($defectReport->shift->organization)->title }}</td>
                </tr>
                <tr>
                    <th>Бўлим</th>
                    <td>{{ optional($defectReport->shift->section)->title }}</td>
                </tr>
                <tr>
                    <th>Смена</th>
                    <td>{{ optional($defectReport->shift)->title }}</td>
                </tr>
                <tr>
                    <th>Махсулот</th>
                    <td>{{ optional($defectReport->stage)->title }}</td>
                </tr>
                <tr>
                    <th>Микдори</th>
                    <td class="text-success fw-bold">{{ $defectReport->stage_count }} та</td>
                </tr>
                <tr>
                    <th>Умумий</th>
                    <td class="text-danger fw-bold">{{ $defectReport->total_defect_amount }} кг</td>
                </tr>
                <tr>
                    <th>Ортикча</th>
                    <td class="text-danger fw-bold">{{ $defectReport->defect_amount }} кг</td>
                </tr>
                <tr>
                    <th>%</th>
                    <td class="text-info fw-bold">{{ $defectReport->defect_percent }} %</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $defectReport->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $defectReport->updated_at?->format('Y-m-d H:i') }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
