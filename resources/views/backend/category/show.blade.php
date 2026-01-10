<x-backend.layouts.main title="{{ 'Қатегорияни кўриш: ' . ucfirst($category->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header">
{{--            <h1>{{ $category->title[app()->getLocale()] ?? $category->title }}</h1>--}}
        </div>
        <div class="card-body">

            <x-backend.action route="category" :id="$category->id" :back="true" :edit="true" editClass="btn btn-primary sm" editLabel="Янгилаш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Photo</th>
                    <td>
                        @if(optional($category->file)->path)
                            <img style="width: 300px; object-fit: contain" src="{{ asset('storage/' . $category->file->path) }}" alt="{{ $category->title }}">
                        @else

                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Id</th> <td>{{ $category->id }}</td>
                </tr>
                <tr>
                    <th>Ота категория</th> <td>{{ $category->parent_id }}</td>
                </tr>
                <tr>
                    <th>Номи</th> <td>{{ $category->title[app()->getLocale()] ?? $category->title }}</td>
                </tr>
{{--                <tr>--}}
{{--                    <th>Subtitle</th> <td>{{ $category->subtitle }}</td>--}}
{{--                </tr>--}}
                <tr>
                    <th>Тавсифи</th> <td>{{ $category->description }}</td>
                </tr>
                <tr>
                    <th>Ҳодим</th><td>{{ $category->user->username }}</td>
                </tr>
                <tr>
                    <th>Яратилди</th><td>{{ $category->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th><td>{{ $category->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
