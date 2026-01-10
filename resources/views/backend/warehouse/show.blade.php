<x-backend.layouts.main title="{{ 'Филиални кўриш: ' . ucfirst($warehouse->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header">
            {{--            <h1>{{ $organization->title[app()->getLocale()] ?? $organization->title }}</h1>--}}
        </div>
        <div class="card-body">

            <x-backend.action route="product" :id="$warehouse->id" :back="true" :edit="true"
                              editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th>
                    <td>{{ $warehouse->id }}</td>
                </tr>
                <tr>
                    <th>Филиал</th>
                    <td>{{ optional($warehouse->organization)->title }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $warehouse->title }}</td>
                </tr>
                <tr>
                    <th>Тавсифи</th>
                    <td>{!! $warehouse->description !!}</td>
                </tr>
                <tr>
                    <th>Статус</th>
                    <td>{{ \App\Services\StatusService::getList()[$warehouse->status]}}</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $warehouse->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $warehouse->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
