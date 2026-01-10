<x-backend.layouts.main title="{{ 'Хомашёни кўриш: ' . ucfirst($rawMaterialVariation->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header"></div>
        <div class="card-body">

            <x-backend.action route="raw-material-variation" :id="$rawMaterialVariation->id" :back="true" :edit="true"
                              :delete="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Расм</th>
                    <td>
                        @if(optional($rawMaterialVariation->file)->path)
                            <img style="width: 300px; object-fit: contain"
                                 src="{{ asset('storage/' . $rawMaterialVariation->file->path) }}"
                                 alt="{{ $rawMaterialVariation->title }}">
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Id</th>
                    <td>{{ $rawMaterialVariation->id }}</td>
                </tr>
                <tr>
                    <th>Код</th>
                    <td>{{ $rawMaterialVariation->code }}</td>
                </tr>
                <tr>
                    <th>Хомашё тури</th>
                    <td>{{ optional($rawMaterialVariation->rawMaterial)->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $rawMaterialVariation->title[app()->getLocale()] ?? $rawMaterialVariation->title }}</td>
                </tr>
                <tr>
                    <th>Тавсифи</th>
                    <td>{{ $rawMaterialVariation->description }}</td>
                </tr>
                <tr>
                    <th>Нархи</th>
                    <td><span class="fw-bold text-success">{{ \App\Helpers\PriceHelper::format($rawMaterialVariation->price, $rawMaterialVariation->currency, false) }}</span> {{ \App\Services\StatusService::getCurrency()[$rawMaterialVariation->currency] }}</td>
                </tr>
                <tr>
                    <th>Микдори</th>
                    <td><span class="fw-bold text-primary">{{ \App\Helpers\CountHelper::format($rawMaterialVariation->count, $rawMaterialVariation->unit, false) }}</span> {{ \App\Services\StatusService::getTypeCount()[$rawMaterialVariation->unit] }}</td>
                </tr>
                <tr>
                    <th>Минимал микдор</th>
                    <td><span class="fw-bold text-danger">{{ \App\Helpers\CountHelper::format($rawMaterialVariation->min_count, $rawMaterialVariation->unit, false) }}</span> {{ \App\Services\StatusService::getTypeCount()[$rawMaterialVariation->unit] }}</td>
                </tr>
                <tr>
                    <th>Умумий</th>
                    <td><span class="fw-bold text-info">{{ \App\Helpers\PriceHelper::format($rawMaterialVariation->total_price, $rawMaterialVariation->currency, false) }}</span> {{ \App\Services\StatusService::getCurrency()[$rawMaterialVariation->currency] }}</td>
                </tr>
                <tr>
                    <th>Статус</th>
                    <td>{{ \App\Services\StatusService::getList()[$rawMaterialVariation->status] }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $rawMaterialVariation->created_at }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $rawMaterialVariation->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
