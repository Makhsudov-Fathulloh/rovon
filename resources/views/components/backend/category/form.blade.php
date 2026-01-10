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
                                               value="{{ old('title', $category->title ?? '') }}">
                                        @error('title')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{--                                    <div class="mb-3">--}}
                                    {{--                                        <label for="subtitle" class="form-label">Субтитр</label>--}}
                                    {{--                                        <input type="text" id="subtitle" name="subtitle" class="form-control"--}}
                                    {{--                                               value="{{ old('subtitle', $category->subtitle ?? '') }}">--}}
                                    {{--                                    </div>--}}

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Тавсифи</label>
                                        <textarea id="description"
                                                  name="description"
                                                  class="form-control ckeditor"
                                                  rows="10">
                                                {{ old('description', $category->description ?? '') }}
                                            </textarea>
                                    </div>

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
                                    <button type="button"
                                            class="btn btn-primary open-file-manager">
                                        <i class="fa fa-picture-o"></i>Файл юклаш
                                    </button>
                                </span>
                                    <input id="thumbnail_1" class="form-control" type="text" name="filepath" readonly>
                                    <input type="file" name="image" id="real-file" style="display: none;" multiple>
                                    @error('image')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div id="holder_1" style="margin-top:15px; max-height:100px;"></div>
                            </div>
                            <input type="file" id="real-file" style="display: none;">
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Тури</label>
                            <select name="type" id="type" class="form-control">
                                @foreach (\App\Models\Category::getTypeList() as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ old('type', $category->type ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="parent_id">Ота категория</label>
                            <select class="form-control" name="parent_id">
                                <option value="">Ота категорияни танланг</option>
                                @foreach($categoryDropdown as $id => $name)
                                    <option value="{{ $id }}" {{ $category->parent_id == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if (Route::currentRouteName() == 'category.create')
                            {{--                            <div class="mb-3">--}}
                            {{--                                <label for="slug">Слаг</label>--}}
                            {{--                                <input type="text" name="slug" class="form-control" placeholder="Автоматик яратилади"--}}
                            {{--                                       value="{{ old('slug', $category->slug) }}">--}}
                            {{--                            </div>--}}
                        @endif

                        @if (Route::currentRouteName() == 'category.create')
                            <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'category.edit')
                            <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
