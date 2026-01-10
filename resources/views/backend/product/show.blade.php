<x-backend.layouts.main title="{{ 'Маҳсулот турини кўриш: ' . ucfirst($product->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header">
            {{--<h1>{{ $product->title[app()->getLocale()] ?? $product->title }}</h1>--}}
        </div>
        <div class="card-body">

            <x-backend.action route="product" :id="$product->id" :back="true" :edit="true"
                              editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Расм</th>
                    <td>
                        @if(optional($product->file)->path)
                            <img style="width: 300px; object-fit: contain"
                                 src="{{ asset('storage/' . $product->file->path) }}" alt="{{ $product->title }}">
                        @endif
                        {{--{!! $product->file ? '<img style="width: 300px; object-fit: contain" src="' . asset('storage/' . $product->file->path) . '" alt="' . e($product->title) . '">'--}}
                        {{--: 'no image' !!}--}}
                    </td>
                </tr>
                <tr>
                    <th>Id</th>
                    <td>{{ $product->id }}</td>
                </tr>
                <tr>
                    <th>Омбор</th>
                    <td>{{ optional($product->warehouse)->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $product->title }}</td>
                </tr>
                {{--<tr><th>Subtitle</th> <td>{{ $product->subtitle}}</td></tr>--}}
                <tr>
                    <th>Тавсифи</th>
                    <td>{!! $product->description !!}</td>
                </tr>
                <tr>
                    <th>Ҳодим</th>
                    <td>{{ $product->user->username }}</td>
                </tr>
                <tr>
                    <th>Категория</th>
                    <td>{{ $product->category->title }}</td>
                </tr>
                {{--<tr><th>Тури</th><td>{{ $product->type }}</td></tr>--}}
                <tr>
                    <th>Статус</th>
                    <td>{{ \App\Services\StatusService::getList()[$product->status] }}</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $product->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $product->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
