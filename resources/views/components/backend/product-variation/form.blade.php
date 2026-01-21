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
                                        <label for="code" class="form-label">Код</label>
                                        <input type="text" id="code" name="code" class="form-control"
                                               value="{{ old('code', $productVariation->code ?? '') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="title" class="form-label">Номи</label>
                                        <input type="text" id="title" name="title" class="form-control"
                                               value="{{ old('title', $productVariation->title ?? '') }}">
                                        @error('title')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{--<div class="mb-3">--}}
                                    {{--<label for="subtitle" class="form-label">Субтитр</label>--}}
                                    {{--<input type="text" id="subtitle" name="subtitle" class="form-control"--}}
                                    {{--value="{{ old('subtitle', $productVariation->subtitle ?? '') }}">--}}
                                    {{--</div>--}}

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Тавсифи</label>
                                        <textarea id="description"
                                                  name="description"
                                                  class="form-control ckeditor"
                                                  rows="10">
                                                {{ old('description', $productVariation->description ?? '') }}
                                            </textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5 mb-3">
                                            <label for="body_price" class="form-label">Тан нархи</label>
                                            <input type="text" step="0.01" id="body_price" name="body_price"
                                                   class="form-control filter-numeric-decimal"
                                                   value="{{ old('body_price', \App\Helpers\PriceHelper::format($productVariation->body_price, $productVariation->currency, false) ?? '') }}">
                                            @error('body_price')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-5 mb-3">
                                            <label for="price" class="form-label">Сотиш нархи</label>
                                            <input type="text" step="0.01" id="price" name="price"
                                                   class="form-control filter-numeric-decimal"
                                                   value="{{ old('price', \App\Helpers\PriceHelper::format($productVariation->price, $productVariation->currency, false) ?? '') }}">
                                            @error('price')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="currency" class="form-label">Валюта</label>
                                            <select name="currency" id="currency" class="form-control">
                                                @foreach (\App\Services\StatusService::getCurrency() as $key => $label)
                                                    <option
                                                        value="{{ $key }}" {{ old('currency', $productVariation->currency ?? '') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('currency')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="count" class="form-label">Микдори</label>
                                            <input type="text" id="count" name="count"
                                                   class="form-control filter-numeric-decimal"
                                                   value="{{ old('count', \App\Helpers\CountHelper::format($productVariation->count, $productVariation->unit, false) ?? '') }}">
                                            @error('count')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                           <div class="col-md-4 mb-3">
                                            <label for="min_count" class="form-label">Минимал микдор</label>
                                            @php
                                                $typeMinCount = $productVariation->unit;
                                                $minCount = $productVariation->min_count;

                                                $minCountValue = ($typeMinCount == \App\Services\StatusService::UNIT_KG)
                                                    ? number_format((float)$minCount, 3, '.', '')
                                                    : (int)$minCount;
                                            @endphp
                                            <input type="text" id="min_count" name="min_count"
                                                   class="form-control filter-numeric-decimal"
                                                   value="{{ old('min_count', $minCountValue ?? '') }}">

                                            @error('min_count')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="unit" class="form-label">Ўлчов бирлик</label>
                                            <select name="unit" id="unit" class="form-control" disabled>
                                                @foreach (\App\Services\StatusService::getTypeCount() as $key => $label)
                                                    <option
                                                        value="{{ $key }}" {{ old('unit', $productVariation->unit ?? '') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{--<div class="mb-3">--}}
                                    {{--<label for="type" class="form-label">Тури</label>--}}
                                    {{--<input type="text" id="type" name="type" class="form-control" value="{{ old('type', $productVariation->type ?? '') }}">--}}
                                    {{--</div>--}}
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
                                           value="{{ $productVariation->file ?? '' ? asset('storage/' . $productVariation->file->path) : '' }}">
                                    <input type="file" name="image" id="real-file" style="display: none;" multiple>
                                    @error('image')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if (isset($productVariation) && $productVariation->file)
                                    <div id="holder_1" style="margin-top:15px; max-height:100px;">
                                        <img src="{{ asset('storage/' . $productVariation->file->path) }}"
                                             style="height: 80px;">
                                    </div>
                                @else
                                    <div id="holder_1" style="margin-top:15px; max-height:100px;"></div>
                                @endif
                            </div>
                        </div>

                        @if (Route::currentRouteName() == 'product-variation.edit')
                            <div class="mb-3 mt-3">
                                <label for="product_id">Модел</label>
                                <select class="form-control select2" name="product_id">
                                    <option value="">Select a state ...</option>
                                    @foreach($productDropdown as $id => $name)
                                        <option
                                            value="{{ $id }}" {{ (isset($productVariation) && $productVariation->product_id == $id) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if (Route::currentRouteName() == 'product-variation.create')
                            <div class="mb-3">
                                <label for="slug">Слаг</label>
                                <input type="text" name="slug" class="form-control" placeholder="Автоматик яратилади"
                                       value="{{ old('slug', $productVariation->slug ?? '') }}">
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="top" class="form-label">Toп</label>
                            <select type="number" id="top" name="top" class="form-control">
                                @foreach (\App\Models\ProductVariation::getTopList() as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ old('top', $productVariation->top ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Статус</label>
                            <select name="status" id="status" class="form-control">
                                @foreach (\App\Services\StatusService::getList() as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ old('status', $productVariation->status ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if (Route::currentRouteName() == 'product-variation.create.custom')
                            <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'product-variation.edit')
                            <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


