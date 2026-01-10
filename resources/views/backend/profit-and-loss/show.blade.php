<x-backend.layouts.main title="{{ 'Зарарни кўриш: ' . ucfirst($profitAndLoss->variation->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header">
{{--            <h1>{{ $profitAndLoss->variation->title[app()->getLocale()] ?? $profitAndLoss->variation->title }}</h1>--}}
        </div>
        <div class="card-body">

            <x-backend.action route="order" :id="$profitAndLoss->orderItem->order->id" :back="true"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th> <td>{{ $profitAndLoss->id }}</td>
                </tr>
                <tr>
                    <th>Буюртма элементи</th> <td>{{ $profitAndLoss->order_item_id }}</td>
                </tr>
                <tr>
                    <th>Маҳсулот</th> <td>{{ $profitAndLoss->variation->title }}</td>
                </tr>
                <tr>
                    <th>Асл нарх</th> <td>{{ number_format($profitAndLoss->original_price, 0, '', ' ') }} сўм</td>
                </tr>
                <tr>
                    <th>Сотилган нарх</th> <td>{{ number_format($profitAndLoss->sold_price, 0, '', ' ') }} сўм</td>
                </tr>
                @if ($profitAndLoss->profit_amount > 0)
                    <tr>
                        <th>Фойда</th>
                        <td class="text-success fw-bold">
                            {{ number_format($profitAndLoss->profit_amount, 0, '', ' ') }} сўм
                        </td>
                    </tr>
                @elseif ($profitAndLoss->loss_amount > 0)
                    <tr>
                        <th>Зарар</th>
                        <td class="text-danger fw-bold">
                            {{ number_format($profitAndLoss->loss_amount, 0, '', ' ') }} сўм
                        </td>
                    </tr>
                @endif
                <tr>
                    <th>Миқдори</th> <td>{{ number_format($profitAndLoss->count, 0, '', ' ') }} та</td>
                </tr>
                <tr>
                    <th>Умумий зарар</th><td>{{ number_format($profitAndLoss->total_amount, 0, '', ' ') }} сўм</td>
                </tr>
                <tr>
                    <th>Яратилди</th><td>{{ $profitAndLoss->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th><td>{{ $profitAndLoss->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
