<x-backend.layouts.main title="{{ 'Қатегорияни кўриш: ' . ucfirst($productReturn->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-body">

            <x-backend.action route="product-return" :id="$productReturn->id" :back="true" :edit="true" editClass="btn btn-primary sm" editLabel="Янгилаш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th> <td>{{ $productReturn->id }}</td>
                </tr>
                <tr>
                    <th>Номи</th> <td>{{ $productReturn->title[app()->getLocale()] ?? $productReturn->title }}</td>
                </tr>
                <tr>
                    <th>Харажат номи</th> <td>{{ optional($productReturn->expense)->title }}</td>
                </tr>
                <tr>
                    <th>Микдори</th>
                    <td><span class="fw-bold text-danger">{{ \App\Helpers\PriceHelper::format($productReturn->total_amount, $productReturn->currency, false) }}</span> {{ \App\Services\StatusService::getCurrency()[$productReturn->currency] }}</td>
                </tr>
                <tr>
                    <th>Ҳодим</th><td>{{ optional($productReturn->user)->username }}</td>
                </tr>
                <tr>
                    <th>Яратилди</th><td>{{ $productReturn->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th><td>{{ $productReturn->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
