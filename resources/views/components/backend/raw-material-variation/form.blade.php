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
                                               value="{{ old('code', $rawMaterialVariation->code ?? '') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="title" class="form-label">Номи</label>
                                        <input type="text" id="title" name="title" class="form-control"
                                               value="{{ old('title', $rawMaterialVariation->title ?? '') }}">
                                        @error('title')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

{{--                                    <div class="mb-3">--}}
{{--                                        <label for="subtitle" class="form-label">Субтитр</label>--}}
{{--                                        <input type="text" id="subtitle" name="subtitle" class="form-control"--}}
{{--                                               value="{{ old('subtitle', $rawMaterialVariation->subtitle ?? '') }}">--}}
{{--                                    </div>--}}

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Тавсифи</label>
                                        <textarea id="description"
                                                  name="description"
                                                  class="form-control ckeditor"
                                                  rows="10">
                                                {{ old('description', $rawMaterialVariation->description ?? '') }}
                                            </textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="price" class="form-label">Нархи</label>
                                            <input type="text" step="0.01" id="price" name="price"
                                                   class="form-control filter-numeric"
                                                   value="{{ old('price', \App\Helpers\PriceHelper::format($rawMaterialVariation->price, $rawMaterialVariation->currency, false) ?? '') }}">
                                            @error('price')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="currency" class="form-label">Валюта</label>
                                            <select name="currency" id="currency" class="form-control">
                                                @foreach (\App\Services\StatusService::getCurrency() as $key => $label)
                                                    <option
                                                        value="{{ $key }}" {{ old('currency', $rawMaterialVariation->currency ?? '') == $key ? 'selected' : '' }}>
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
                                            @php
                                                $typeCount = $rawMaterialVariation->unit;
                                                $count = $rawMaterialVariation->count;

                                                $countValue = ($typeCount == \App\Services\StatusService::UNIT_KG)
                                                    ? number_format((float)$count, 3, '.', '')
                                                    : (int)$count;
                                            @endphp
                                            <input type="text" id="count" name="count"
                                                   class="form-control filter-numeric-decimal"
                                                   value="{{ old('count', $countValue ?? '') }}">
                                            @error('count')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="min_count" class="form-label">Минимал микдор</label>
                                            @php
                                                $typeMinCount = $rawMaterialVariation->unit;
                                                $minCount = $rawMaterialVariation->min_count;

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
                                            <label for="unit" class="form-label">Тури</label>
                                            <select name="unit" id="unit" class="form-control">
                                                @foreach (\App\Services\StatusService::getTypeCount() as $key => $label)
                                                    <option
                                                        value="{{ $key }}" {{ old('unit', $rawMaterialVariation->unit ?? '') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{--<div class="mb-3">--}}
                                    {{--<label for="type" class="form-label">Тури</label>--}}
                                    {{--<input type="text" id="type" name="type" class="form-control" value="{{ old('type', $rawMaterialVariation->type ?? '') }}">--}}
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
                                           value="{{ $rawMaterialVariation->file ?? '' ? asset('storage/' . $rawMaterialVariation->file->path) : '' }}">
                                    <input type="file" name="image" id="real-file" style="display: none;" multiple>
                                    @error('image')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if (isset($rawMaterialVariation) && $rawMaterialVariation->file)
                                    <div id="holder_1" style="margin-top:15px; max-height:100px;">
                                        <img src="{{ asset('storage/' . $rawMaterialVariation->file->path) }}"
                                             style="height: 80px;">
                                    </div>
                                @else
                                    <div id="holder_1" style="margin-top:15px; max-height:100px;"></div>
                                @endif
                            </div>
                        </div>

                        @if (Route::currentRouteName() == 'raw-material-variation.edit')
                            <div class="mb-3 mt-3">
                                <label for="raw_material_id">Хомашё тури</label>
                                <select class="form-control select2" name="raw_material_id">
                                    <option value="">Select a state ...</option>
                                    @foreach($rawMaterialDropdown as $id => $name)
                                        <option
                                            value="{{ $id }}" {{ (isset($rawMaterialVariation) && $rawMaterialVariation->raw_material_id == $id) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('raw_material_id')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if (Route::currentRouteName() == 'raw-material-variation.create')
                            <div class="mb-3">
                                <label for="slug">Слаг</label>
                                <input type="text" name="slug" class="form-control" placeholder="Автоматик яратилади"
                                       value="{{ old('slug', $rawMaterialVariation->slug ?? '') }}">
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="status" class="form-label">Статус</label>
                            <select name="status" id="status" class="form-control">
                                @foreach (\App\Services\StatusService::getList() as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ old('status', $rawMaterialVariation->status ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if (Route::currentRouteName() == 'raw-material-variation.create.custom')
                            <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'raw-material-variation.edit')
                            <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


{{--<form action="{{ $action }}" method="POST" enctype="multipart/form-data">--}}
{{--    @csrf--}}
{{--    @if ($method === 'PUT')--}}
{{--        @method('PUT')--}}
{{--    @endif--}}

{{--    <div class="container-fluid mt-4">--}}
{{--        <div class="row">--}}
{{--            <div class="col-md-8">--}}
{{--                <div class="card shadow">--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="pt-4">--}}

{{--                            --}}{{-- Код --}}
{{--                            <div class="mb-3">--}}
{{--                                <label for="code" class="form-label">Код</label>--}}
{{--                                <input type="text" id="code" name="code" class="form-control"--}}
{{--                                       value="{{ old('code', $rawMaterialVariation->code ?? '') }}">--}}
{{--                            </div>--}}

{{--                            --}}{{-- Номи --}}
{{--                            <div class="mb-3">--}}
{{--                                <label for="title" class="form-label">Номи</label>--}}
{{--                                <input type="text" id="title" name="title" class="form-control"--}}
{{--                                       value="{{ old('title', $rawMaterialVariation->title ?? '') }}">--}}
{{--                                @error('title')--}}
{{--                                <div class="text-danger small">{{ $message }}</div>--}}
{{--                                @enderror--}}
{{--                            </div>--}}

{{--                            --}}{{-- Тавсифи --}}
{{--                            <div class="mb-3">--}}
{{--                                <label for="description" class="form-label">Тавсифи</label>--}}
{{--                                <textarea id="description"--}}
{{--                                          name="description"--}}
{{--                                          class="form-control ckeditor"--}}
{{--                                          rows="10">{{ old('description', $rawMaterialVariation->description ?? '') }}</textarea>--}}
{{--                            </div>--}}

{{--                            <div class="row">--}}
{{--                                --}}{{-- Нархи --}}
{{--                                <div class="col-md-8 mb-3">--}}
{{--                                    <label for="price" class="form-label">Нархи</label>--}}
{{--                                    <input type="text" step="0.01" id="price" name="price"--}}
{{--                                           class="form-control filter-numeric"--}}
{{--                                           value="{{ old('price', $rawMaterialVariation->price ?? '') }}">--}}
{{--                                    @error('price')--}}
{{--                                    <div class="text-danger small">{{ $message }}</div>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

{{--                                --}}{{-- Валюта --}}
{{--                                <div class="col-md-4 mb-3">--}}
{{--                                    <label for="currency" class="form-label">Валюта</label>--}}
{{--                                    <select name="currency" id="currency" class="form-control">--}}
{{--                                        @foreach (\App\Services\StatusService::getCurrency() as $key => $label)--}}
{{--                                            <option--}}
{{--                                                value="{{ $key }}" {{ old('currency', $rawMaterialVariation->currency ?? '') == $key ? 'selected' : '' }}>--}}
{{--                                                {{ $label }}--}}
{{--                                            </option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    @error('currency')--}}
{{--                                    <div class="text-danger small">{{ $message }}</div>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            --}}{{-- Миқдори ва тури --}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-8 mb-3">--}}
{{--                                    <label for="count" class="form-label">Миқдори</label>--}}

{{--                                    @php--}}
{{--                                        $typeCount = $rawMaterialVariation->unit ?? 1;--}}
{{--                                        $count = $rawMaterialVariation->count ?? 0;--}}
{{--                                        $countValue = ($typeCount == 1)--}}
{{--                                            ? number_format((float)$count, 3, '.', '')--}}
{{--                                            : (int)$count;--}}
{{--                                    @endphp--}}

{{--                                    <input type="text" id="count" name="count"--}}
{{--                                           class="form-control filter-numeric-decimal"--}}
{{--                                           value="{{ old('count', $countValue) }}">--}}
{{--                                    @error('count')--}}
{{--                                    <div class="text-danger small">{{ $message }}</div>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

{{--                                <div class="col-md-4 mb-3">--}}
{{--                                    <label for="unit" class="form-label">Тури</label>--}}
{{--                                    <select name="unit" id="unit" class="form-control">--}}
{{--                                        @foreach (\App\Services\StatusService::getTypeCount() as $key => $label)--}}
{{--                                            <option--}}
{{--                                                value="{{ $key }}" {{ old('unit', $rawMaterialVariation->unit ?? '') == $key ? 'selected' : '' }}>--}}
{{--                                                {{ $label }}--}}
{{--                                            </option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}{{-- O‘ng panel --}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card shadow">--}}
{{--                    <div class="card-body">--}}
{{--                        <h3 class="panel-heading">Расм</h3>--}}

{{--                        --}}{{-- Расм танlash --}}
{{--                        <div class="form-group mb-3">--}}
{{--                            <div class="input-group">--}}
{{--                                <button type="button" class="btn btn-primary open-file-manager">--}}
{{--                                    <i class="fa fa-picture-o"></i> Файл юклаш--}}
{{--                                </button>--}}
{{--                                <input id="thumbnail_1" class="form-control" type="text" name="filepath"--}}
{{--                                       value="{{ $rawMaterialVariation->file ? asset('storage/' . $rawMaterialVariation->file->path) : '' }}">--}}
{{--                                <input type="file" name="image" id="real-file" style="display: none;" multiple>--}}
{{--                            </div>--}}
{{--                            @if (isset($rawMaterialVariation) && $rawMaterialVariation->file)--}}
{{--                                <div id="holder_1" style="margin-top:15px; max-height:100px;">--}}
{{--                                    <img src="{{ asset('storage/' . $rawMaterialVariation->file->path) }}"--}}
{{--                                         style="height: 80px;">--}}
{{--                                </div>--}}
{{--                            @endif--}}
{{--                            @error('image')--}}
{{--                            <div class="text-danger small">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}

{{--                        --}}{{-- Raw Material tanlash (update da) --}}
{{--                        @if (Route::currentRouteName() == 'raw-material-variation.edit')--}}
{{--                            <div class="mb-3 mt-3">--}}
{{--                                <label for="raw_material_id">Хомашё тури</label>--}}
{{--                                <select class="form-control select2" name="raw_material_id">--}}
{{--                                    @foreach($rawMaterialDropdown as $id => $name)--}}
{{--                                        <option--}}
{{--                                            value="{{ $id }}" {{ (isset($rawMaterialVariation) && $rawMaterialVariation->raw_material_id == $id) ? 'selected' : '' }}>--}}
{{--                                            {{ $name }}--}}
{{--                                        </option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                                @error('raw_material_id')--}}
{{--                                <div class="text-danger small">{{ $message }}</div>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        --}}{{-- Слаг --}}
{{--                        @if (Route::currentRouteName() == 'raw-material-variation.create')--}}
{{--                            <div class="mb-3">--}}
{{--                                <label for="slug">Слаг</label>--}}
{{--                                <input type="text" name="slug" class="form-control"--}}
{{--                                       placeholder="Автоматик яратилади"--}}
{{--                                       value="{{ old('slug', $rawMaterialVariation->slug ?? '') }}">--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        --}}{{-- Статус --}}
{{--                        <div class="mb-3">--}}
{{--                            <label for="status" class="form-label">Статус</label>--}}
{{--                            <select name="status" id="status" class="form-control">--}}
{{--                                @foreach (\App\Services\StatusService::getList() as $key => $label)--}}
{{--                                    <option--}}
{{--                                        value="{{ $key }}" {{ old('status', $rawMaterialVariation->status ?? '') == $key ? 'selected' : '' }}>--}}
{{--                                        {{ $label }}--}}
{{--                                    </option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}

{{--                        <button type="submit" class="btn btn-success">--}}
{{--                            {{ Route::currentRouteName() == 'raw-material-variation.edit' ? 'Янгилаш' : 'Сақлаш' }}--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</form>--}}
