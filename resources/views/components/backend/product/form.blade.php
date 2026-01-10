<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif
    <div class="container-fluid mt-4">
        <div class="row">

            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="tab-content" style="margin: 0">
                            <div class="tab-pane fade show active">
                                <div class="pt-4">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Номи</label>
                                        <input type="text" id="title" name="title" class="form-control"
                                               value="{{ old('title', $product->title ?? '') }}">
                                        @error('title')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{--                                    <div class="mb-3">--}}
                                    {{--                                        <label for="subtitle" class="form-label">Субтитр</label>--}}
                                    {{--                                        <input type="text" id="subtitle" name="subtitle" class="form-control"--}}
                                    {{--                                               value="{{ old('subtitle', $product->subtitle ?? '') }}">--}}
                                    {{--                                    </div>--}}

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Тавсифи</label>
                                        <textarea id="description"
                                                  name="description"
                                                  class="form-control ckeditor"
                                                  rows="10">
                                                {{ old('description', $product->description ?? '') }}
                                            </textarea>
                                    </div>

                                    {{--                                    <div class="mb-3">--}}
                                    {{--                                        <label for="type" class="form-label">Тури</label>--}}
                                    {{--                                        <input type="text" id="type" name="type" class="form-control"--}}
                                    {{--                                               value="{{ old('type', $product->type ?? '') }}">--}}
                                    {{--                                    </div>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="panel-heading">Расм</h3>

                        <div class="panel-body">
                            <div class="form-group" style="margin: 0;">
                                <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary open-file-manager">
                                        <i class="fa fa-picture-o"></i>Файл юклаш
                                    </button>
                                </span>
                                    <input id="thumbnail_1" class="form-control" type="text" name="filepath"
                                           value="{{ $product->file ? asset('storage/' . $product->file->path) : '' }}"
                                           readonly>
                                    <input type="file" name="image" id="real-file" style="display: none;" multiple>
                                    @error('image')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if ($product->file)
                                    <div id="holder_1" style="margin-top:15px; max-height:100px;">
                                        <img src="{{ asset('storage/' . $product->file->path) }}"
                                             style="height: 80px;">
                                    </div>
                                @else
                                    <div id="holder_1" style="margin-top:15px; max-height:100px;"></div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="category_id">Қатегория</label>
                            <select class="form-control" name="category_id">
                                <option value="">Қатегорияни танланг</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ old('category_id', $product->category_id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="warehouse_id">Махсулот омбори</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                <option value="">Махсулот омборини танланг</option>
                                @foreach($warehouses as $id => $title)
                                    <option value="{{ $id }}"
                                        {{ old('warehouse_id', $product->warehouse_id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{--                        @if (Route::currentRouteName() == 'product.create')--}}
                        {{--                            <div class="mb-3">--}}
                        {{--                                <label for="slug">Слаг</label>--}}
                        {{--                                <input type="text" name="slug" class="form-control" placeholder="Автоматик яратилади"--}}
                        {{--                                       value="{{ old('slug', $product->slug ?? '') }}">--}}
                        {{--                            </div>--}}
                        {{--                        @endif--}}

                        <div class="mb-3">
                            <label for="status" class="form-label">Статус</label>
                            <select name="status" id="status" class="form-control">
                                @foreach (\App\Services\StatusService::getList() as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ old('status', $product->status ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if (Route::currentRouteName() == 'product.create')
                            <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'product.edit')
                            <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

