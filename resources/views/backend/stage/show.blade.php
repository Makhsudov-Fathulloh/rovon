<x-backend.layouts.main title="{{ 'Бўлим махсулотини кўриш: ' . ucfirst($stage->title) }}">

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

            <x-backend.action route="stage" :id="$stage->id" :back="true" :edit="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th>
                    <td>{{ $stage->id }}</td>
                </tr>
                <tr>
                    <th>Филиал</th>
                    <td>{{ $stage->section->organization->title }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $stage->section->title }}</td>
                </tr>
                <tr>
                    <th>Тавсифи</th>
                    <td>{!! $stage->description !!}</td>
                </tr>
                <tr>
                    <th>Таркиби</th>
                    <td>
                        @foreach($stage->stageMaterials as $material)
                            <span class="badge bg-info">{{ $material->rawMaterialVariation->code . ' (' . $material->rawMaterialVariation->title . ')' }}</span>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>Нархи</th>
                    <td class="text-success fw-bold">{{ $stage->price }} сўм</td>
                </tr>
                <tr>
                    <th>Статус</th>
                    <td>{{ \App\Services\StatusService::getList()[$stage->status] }}</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $stage->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $stage->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
