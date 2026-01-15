<x-backend.layouts.main title="{{ 'Хомашёни турини кўриш: ' . ucfirst($rawMaterial->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-body">
            <x-backend.action route="raw-material" :id="$rawMaterial->id" :back="true" :edit="true" editClass="btn btn-primary sm" editLabel="Янгилаш" deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Расм</th>
                    <td>
                        @if(optional($rawMaterial->file)->path)
                            <img style="width: 300px; object-fit: contain"
                                 src="{{ asset('storage/' . $rawMaterial->file->path) }}"
                                 alt="{{ $rawMaterial->title }}">
                        @endif
                        {{-- {!! $rawMaterial->file ? '<img style="width: 300px; object-fit: contain" src="' . asset('storage/' . $rawMaterial->file->path) . '" alt="' . e($rawMaterial->title) . '">' : 'no image' !!} --}}
                    </td>
                </tr>
                <tr>
                    <th>Id</th>
                    <td>{{ $rawMaterial->id }}</td>
                </tr>
                <tr>
                    <th>Филиал</th>
                    <td>
                        <span class="badge bg-info">{{ $rawMaterial->warehouse->organization->pluck('title')->join(', ') }}</span>
                    </td>
                </tr>
                <tr>
                    <th>Омбор</th>
                    <td>{{ optional($rawMaterial->warehouse)->title ?? ' ' }}</td>
                </tr>
                <tr>
                    <th>Номи</th>
                    <td>{{ $rawMaterial->title }}</td>
                </tr>
                {{-- <tr>
                    <th>Subtitle</th> <td>{{ $rawMaterial->subtitle}}</td>
                </tr> --}}
                <tr>
                    <th>Тавсифи</th>
                    <td>{!! $rawMaterial->description !!}</td>
                </tr>
                <tr>
                    <th>Ҳодим</th>
                    <td>{{ $rawMaterial->user->username }}</td>
                </tr>
                <tr>
                    <th>Категория</th>
                    <td>{{ $rawMaterial->category->title }}</td>
                </tr>
                {{-- <tr>
                    <th>Тури</th><td>{{ $rawMaterial->type }}</td>
                </tr> --}}
                <tr>
                    <th>Статус</th>
                    <td>{{ \App\Services\StatusService::getList()[$rawMaterial->status] }}</td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $rawMaterial->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $rawMaterial->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
