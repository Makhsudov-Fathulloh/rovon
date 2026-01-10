<x-backend.layouts.main title="{{ 'Хомашё трансферини кўриш: ' . ucfirst($rawMaterialTransfer->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header">
            {{--            <h1>{{ $rawMaterialTransfer->title[app()->getLocale()] ?? $rawMaterialTransfer->title }}</h1>--}}
        </div>
        <div class="card-body">

            <x-backend.action route="raw-material" :id="$rawMaterialTransfer->id" :back="true" :edit="true"
                              editClass="btn btn-primary sm" editLabel="Янгилаш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th>
                    <td>{{ $rawMaterialTransfer->id }}</td>
                </tr>
                <tr>
                    <th>Филиал</th>
                    <td>{{ optional($rawMaterialTransfer->organization)->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Омбор</th>
                    <td>{{ optional($rawMaterialTransfer->warehouse)->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Бўлим</th>
                    <td>{{ optional($rawMaterialTransfer->section)->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Смена</th>
                    <td>{{ optional($rawMaterialTransfer->shift)->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $rawMaterialTransfer->title }}</td>
                </tr>
                <tr>
                    <th>Юборувчи</th>
                    <td>{{ $rawMaterialTransfer->sender->username }}</td>
                </tr>
                <tr>
                    <th>Кабулкилувчи</th>
                    <td>{{ $rawMaterialTransfer->receiver->username }}</td>
                </tr>
                <tr>
                    <th>Умумий</th>
                    <td><span class="fw-bold text-info">{{ \App\Helpers\PriceHelper::format($rawMaterialTransfer->total_item_price, optional($rawMaterialTransfer->items->first()->rawMaterialVariation)->currency) }}</span></td>
                </tr>
                <tr>
                    <th>Статус</th>
                    <td>{{ \App\Services\StatusService::getList()[$rawMaterialTransfer->status] }}</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $rawMaterialTransfer->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $rawMaterialTransfer->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
