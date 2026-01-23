<x-backend.layouts.main title="{{ 'Буюртма турини кўриш: ' . ucfirst($preOrderItem->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-body">
            <x-backend.action route="pre-order-item" :id="$preOrderItem->id" :back="true" editClass="btn btn-primary sm"/>
            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Расм</th>
                    <td>
                        @if(optional($preOrderItem->productVariation->file)->path)
                            <img style="width: 300px; object-fit: contain"
                                 src="{{ asset('storage/' . $productVariation->file->path) }}"
                                 alt="{{ $productVariation->title }}">
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Id</th> <td>{{ $preOrderItem->id }}</td>
                </tr>
                <tr>
                    <th>Буюртма</th> <td>{{ $preOrderItem->preOrder->title }}</td>
                </tr>
                <tr>
                    <th>Код</th> <td>{{ $preOrderItem->code }}</td>
                </tr>
                <tr>
                    <th>Маҳсулот</th> <td>{{ $preOrderItem->title }}</td>
                </tr>
                <tr>
                    <th>Сони</th> <td><span class="fw-bold text-primary">{{ number_format($preOrderItem->count, 0, '', ' ') }} та</span></td>
                </tr>
                <tr>
                    <th>Яратилди</th><td>{{ $preOrderItem->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th><td>{{ $preOrderItem->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-backend.layouts.main>
