<x-backend.layouts.main title="{{ 'Филиални кўриш: ' . ucfirst($organization->title) }}">

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

            <x-backend.action route="product" :id="$organization->id" :back="true" :edit="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th>
                    <td>{{ $organization->id }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $organization->title }}</td>
                </tr>
                <tr>
                    <th>Тавсифи</th>
                    <td>{!! $organization->description !!}</td>
                </tr>
                <tr>
                    <th>Ҳодим</th>
                    <td>{{ $organization->user->username }}</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $organization->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $organization->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
