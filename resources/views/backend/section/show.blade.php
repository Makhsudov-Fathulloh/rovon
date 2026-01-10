<x-backend.layouts.main title="{{ 'Бўлимни кўриш: ' . ucfirst($section->title) }}">

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

            <x-backend.action route="product" :id="$section->id" :back="true" :edit="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th>
                    <td>{{ $section->id }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $section->title }}</td>
                </tr>
                <tr>
                    <th>Тавсифи</th>
                    <td>{!! $section->description !!}</td>
                </tr>
                <tr>
                    <th>Ҳодим</th>
                    <td>{{ $section->organization->tilte }}</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $section->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $section->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
