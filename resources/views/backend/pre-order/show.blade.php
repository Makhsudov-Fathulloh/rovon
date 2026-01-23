<x-backend.layouts.main title="{{ 'Буюртмани кўриш: ' . ucfirst($pre_order->user->username) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-body">

            <x-backend.action route="pre-order" :id="$pre_order->id" :back="true" editClass="btn btn-primary sm" editLabel="Янгилаш" class="custom-btn"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th>
                    <td>{{ $pre_order->id }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $pre_order->title }}</td>
                </tr>
                <tr>
                    <th>Тавсифи</th>
                    <td>{{ $pre_order->description }}</td>
                </tr>
                <tr>
                    <th>Сони</th>
                    <td>{{ number_format($pre_order->count, 0, '', ' ') }} хил</td>
                </tr>
                <tr>
                    <th>Клиент</th>
                    <td>{{ $pre_order->user->username }}</td>
                </tr>
                <tr>
                    <th>Буюртмачи</th>
                    <td>{{ $pre_order->customer->username }}</td>
                </tr>
                <tr>
                    <th>Статус</th>
                    <td>{{ \App\Models\PreOrder::getStatusList()[$pre_order->status]}}</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $pre_order->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $pre_order->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
