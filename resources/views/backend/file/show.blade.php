<x-backend.layouts.main title="{{ 'Файлни кўриш: ' . ucfirst($product->title) }}">

    <div class="card">
        <div class="card-header">
{{--            <h1>{{ $product->title[app()->getLocale()] ?? $product->title }}</h1>--}}
        </div>
        <div class="card-body">
            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-primary">Янгилаш</a>

            <form action="{{ route('product.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger">Ўчириш</button>
            </form>
            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Photo</th>
                    <td>
                        @if($product->file)
                            <img style="width: 300px; object-fit: contain" src="{{ asset('storage/' . $product->file->path) }}" alt="{{ $product->title }}">
                        @else
                            no image
                        @endif
{{--                        {!! $product->file ? '<img style="width: 300px; object-fit: contain" src="' . asset('storage/' . $product->file->path) . '" alt="' . e($product->title) . '">'--}}
{{--                        : 'no image' !!}--}}
                    </td>
                </tr>
                <tr>
                    <th>ID</th> <td>{{ $product->id }}</td>
                </tr>
                <tr>
                    <th>Parent ID</th> <td>{{ $product->parent_id ?? 'null' }}</td>
                </tr>
                <tr>
                    <th>Title</th> <td>{{ $product->title[app()->getLocale()] ?? $product->title }}</td>
                </tr>
                <tr>
                    <th>Subtitle</th> <td>{{ $product->subtitle ?? 'null'}}</td>
                </tr>
                <tr>
                    <th>Description</th> <td>{{ $product->description ?? 'null' }}</td>
                </tr>
                <tr>
                    <th>Slug</th><td>{{ $product->slug }}</td>
                </tr>
                <tr>
                    <th>User ID</th><td>{{ $product->user_id }}</td>
                </tr>
                <tr>
                    <th>Created At</th><td>{{ $product->created_at }}</td>
                </tr>
                <tr>
                    <th>Updated At</th><td>{{ $product->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
