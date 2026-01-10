<x-backend.layouts.main title="{{ 'Буюртмани кўриш: ' . ucfirst($order->user->username) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header"></div>
        <div class="card-body">

            <x-backend.action route="order" :id="$order->id" :back="true" :edit="true" :delete="true"
                              editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"
                              class="custom-btn"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th>
                    <td>{{ $order->id }}</td>
                </tr>
                <tr>
                    <th>Клиент</th>
                    <td>{{ $order->user->username }}</td>
                </tr>
                <tr>
                    <th>Статус</th>
                    <td>{{ \App\Models\Order::getStatusList()[$order->status]}}</td>
                </tr>
                <tr>
                    <th>Умумий нарх</th>
                    <td>
                        <span class="fw-bold text-info">
                            {{ \App\Helpers\PriceHelper::format($order->total_price, $order->currency) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Жами тўланган сумма</th>
                    <td>
                        <span class="fw-bold text-success">
                            {{ \App\Helpers\PriceHelper::format($order->total_amount_paid, $order->currency) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Нақд</th>
                    <td>
                        <span>
                            {{ \App\Helpers\PriceHelper::format($order->cash_paid, $order->currency) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Карта (терминал)</th>
                    <td>
                        <span>
                            {{ \App\Helpers\PriceHelper::format($order->card_paid, $order->currency) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Ўтказма</th>
                    <td>
                        <span>
                            {{ \App\Helpers\PriceHelper::format($order->transfer_paid, $order->currency) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Ҳисоб рақам</th>
                    <td>
                        <span>
                            {{ \App\Helpers\PriceHelper::format($order->bank_paid, $order->currency) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Қолган қарз</th>
                    <td>
                        <span class="fw-bold text-danger">
                            {{ \App\Helpers\PriceHelper::format($order->remaining_debt, $order->currency) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Сотувчи</th>
                    <td>{{ $order->seller->username }}</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $order->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $order->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
