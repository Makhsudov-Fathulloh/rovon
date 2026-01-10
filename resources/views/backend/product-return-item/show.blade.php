<x-backend.layouts.main title="{!! 'Маҳсулотни кўриш: ' . ucfirst($productReturnItem->title) !!}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header"></div>
        <div class="card-body">

            <x-backend.action route="product-return-item" :id="$productReturnItem->id" :back="true" :edit="true"
                              :delete="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th>
                    <td>{{ $productReturnItem->id }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $productReturnItem->productReturn->title }}</td>
                </tr>
                <tr>
                    <th>Махсулот</th>
                    <td>{{ $productReturnItem->variation->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Нархи</th>
                    <td><span class="fw-bold text-success">{{ number_format($productReturnItem->price, 0, '', ' ') }}</span> сўм</td>
                </tr>
                <tr>
                    <th>Миқдори</th>
                    <td><span class="fw-bold text-primary">{{ \App\Helpers\CountHelper::format($productReturnItem->count, $productReturnItem->variation->unit, false) }}</span> {{ \App\Services\StatusService::getTypeCount()[$productReturnItem->variation->unit] }}</td>
                </tr>
                <tr>
                    <th>Умумий</th>
                    <td><span class="fw-bold text-info">{{ number_format($productReturnItem->total_price, 0, '', ' ') }}</span> сўм</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $productReturnItem->created_at }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $productReturnItem->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
