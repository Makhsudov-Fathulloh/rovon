<x-backend.layouts.main title="{{ 'Маҳсулотни кўриш: ' . ucfirst($productVariation->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header"></div>
        <div class="card-body">

            <x-backend.action route="product-variation" :id="$productVariation->id" :back="true" :edit="true"
                              :delete="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Расм</th>
                    <td>
                        @if(optional($productVariation->file)->path)
                            <img style="width: 300px; object-fit: contain"
                                 src="{{ asset('storage/' . $productVariation->file->path) }}"
                                 alt="{{ $productVariation->title }}">
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Id</th>
                    <td>{{ $productVariation->id }}</td>
                </tr>
                <tr>
                    <th>Код</th>
                    <td>{{ $productVariation->code }}</td>
                </tr>
                <tr>
                    <th>Модел</th>
                    <td>{{ $productVariation->product->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $productVariation->title[app()->getLocale()] ?? $productVariation->title }}</td>
                </tr>
                <tr>
                    <th>Тавсифи</th>
                    <td>{{ $productVariation->description }}</td>
                </tr>
                <tr>
                    <th>Тан нархи</th>
                    <td><span class="fw-bold">{{ \App\Helpers\PriceHelper::format($productVariation->body_price, $productVariation->currency, false) }}</span> {{ \App\Services\StatusService::getCurrency()[$productVariation->currency] }}</td>
                </tr>
                <tr>
                    <th>Нархи</th>
                    <td><span class="fw-bold text-success">{{ \App\Helpers\PriceHelper::format($productVariation->price, $productVariation->currency, false) }}</span> {{ \App\Services\StatusService::getCurrency()[$productVariation->currency] }}</td>
                </tr>
                <tr>
                    <th>Сони</th>
                    <td><span class="fw-bold text-primary">{{ \App\Helpers\CountHelper::format($productVariation->count, $productVariation->unit, false) }}</span> {{ \App\Services\StatusService::getTypeCount()[$productVariation->unit] }}</td>
                </tr>
                 <tr>
                    <th>Минимал микдор</th>
                    <td><span class="fw-bold text-danger">{{ \App\Helpers\CountHelper::format($productVariation->min_count, $productVariation->unit, false) }}</span> {{ \App\Services\StatusService::getTypeCount()[$productVariation->unit] }}</td>
                </tr>
                <tr>
                    <th>Умумий</th>
                    <td><span class="fw-bold text-info">{{ \App\Helpers\PriceHelper::format($productVariation->total_price, $productVariation->currency, false) }}</span> {{ \App\Services\StatusService::getCurrency()[$productVariation->currency] }}</td>
                </tr>
                {{--<tr>--}}
                {{--<th>Toп</th>--}}
                {{--<td>{{ \App\Models\ProductVariation::getTopList()[$productVariation->top] }}</td>--}}
                {{--</tr>--}}
                <tr>
                    <th>Статус</th>
                    <td>{{ \App\Services\StatusService::getList()[$productVariation->status] }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $productVariation->created_at }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $productVariation->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
