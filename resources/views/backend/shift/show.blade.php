<x-backend.layouts.main title="{{ 'Сменани кўриш: ' . ucfirst($shift->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-body">

            <x-backend.action route="product" :id="$shift->id" :back="true" :edit="true" editClass="btn btn-primary sm"
                              editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th>
                    <td>{{ $shift->id }}</td>
                </tr>
                <tr>
                    <th>Филиал</th>
                    <td>{{ $shift->section->organization->title }}</td>
                </tr>
                <tr>
                    <th>Бўлим</th>
                    <td>{{ $shift->section->title }}</td>
                </tr>
                <tr>
                    <th>Ҳодимлар</th>
                    <td>
                        @foreach($shift->user as $user)
                            <span class="badge bg-info">{{ $user->username }}</span>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>Статус</th>
                    <td>{{ \App\Services\StatusService::getList()[$shift->status] }}</td>
                </tr>
                <tr>
                    <th>Бошланиш</th>
                    <td>{{ \Carbon\Carbon::parse($shift->started_at)->format('H:i') }}</td>
                </tr>
                <tr>
                    <th>Тугаш</th>
                    <td>{{ \Carbon\Carbon::parse($shift->ended_at)->format('H:i') }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
