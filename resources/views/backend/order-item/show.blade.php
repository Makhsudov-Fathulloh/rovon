<x-backend.layouts.main title="{{ 'Буюртма турини кўриш: ' . ucfirst($orderItem->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header"></div>
        <div class="card-body">
            <x-backend.action route="order-item" :id="$orderItem->id" :back="true" :edit="true" :delete="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>
            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th> <td>{{ $orderItem->id }}</td>
                </tr>
                <tr>
                    <th>Буюртмачи</th> <td>{{ $orderItem->order->user->username }}</td>
                </tr>
                <tr>
                    <th>Маҳсулот</th> <td>{{ $orderItem->productVariation->title }}</td>
                </tr>
                <tr>
                    <th>Нархи</th> <td><span class="fw-bold text-success">{{ \App\Helpers\PriceHelper::format($orderItem->price, $orderItem->order->currency) }}</span></td>
                </tr>
                <tr>
                    <th>Сони</th> <td><span class="fw-bold text-primary">{{ number_format($orderItem->quantity, 0, '', ' ') }} та</span></td>
                </tr>
                <tr>
                    <th>Умумий</th><td><span class="fw-bold text-info">{{ \App\Helpers\PriceHelper::format($orderItem->total_price, $orderItem->order->currency) }}</span></td>
                </tr>
                <tr>
                    <th>Яратилди</th><td>{{ $orderItem->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th><td>{{ $orderItem->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-backend.layouts.main>
