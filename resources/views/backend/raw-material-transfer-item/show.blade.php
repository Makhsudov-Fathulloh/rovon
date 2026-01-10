<x-backend.layouts.main title="{{ 'Хомашё трансферини кўриш: ' . ucfirst($rawMaterialTransferItem->rawMaterialTransfer->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header">
            {{--            <h1>{{ $rawMaterialTransferItem->title[app()->getLocale()] ?? $rawMaterialTransferItem->title }}</h1>--}}
        </div>
        <div class="card-body">

            <x-backend.action route="raw-material" :id="$rawMaterialTransferItem->id" :back="true" :edit="true"
                              editClass="btn btn-primary sm" editLabel="Янгилаш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th>
                    <td>{{ $rawMaterialTransferItem->id }}</td>
                </tr>
                <tr>
                    <th>Трансфер</th>
                    <td>{{ optional($rawMaterialTransferItem->rawMaterialTransfer)->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Хомашё</th>
                    <td>{{ optional($rawMaterialTransferItem->rawMaterialVariation)->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Нархи</th>
                    <td><span class="fw-bold text-success">{{ \App\Helpers\PriceHelper::format($rawMaterialTransferItem->price, $rawMaterialTransferItem->rawMaterialVariation->currency) }}</span></td>
                </tr>
                <tr>
                    <th>Микдори</th>
                    <td><span class="fw-bold text-primary">{{ \App\Helpers\CountHelper::format($rawMaterialTransferItem->count, $rawMaterialTransferItem->unit) }}</span></td>
                </tr>
                <tr>
                    <th>Умумий</th>
                    <td><span class="fw-bold text-info">{{ \App\Helpers\PriceHelper::format($rawMaterialTransferItem->total_price, $rawMaterialTransferItem->rawMaterialVariation->currency) }}</span></td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $rawMaterialTransferItem->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $rawMaterialTransferItem->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
