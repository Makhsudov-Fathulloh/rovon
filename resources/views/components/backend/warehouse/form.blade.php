<style>
     /* Dropdown optionlari bir xil rangda, hover uchun quyuqroq */
    .warehouse-select2 .select2-results__option {
        background-color: #0d6efd;
        color: #fff;
    }

    .warehouse-select2 .select2-results__option--highlighted {
        background-color: #0b5ed7 !important;
        color: #fff !important;
    }
</style>

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
                                               value="{{ old('title', $warehouse->title ?? '') }}">
                                        @error('title')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Тавсифи</label>
                                        <textarea id="description"
                                                  name="description"
                                                  class="form-control ckeditor"
                                                  rows="10">
                                                {{ old('description', $warehouse->description ?? '') }}
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
                        <div class="col-md-11 mb-3">
                            <label for="organization">Филиал</label>

                            {{-- <select name="organization_id" id="organization_id" class="form-control" required>
                                <option value="">Филиални танланг</option>
                                @foreach($organizations as $id => $title)
                                    <option value="{{ $id }}"
                                        {{ old('organization_id', $warehouse->organization_id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select> --}}

                             <select name="organization_id[]" id="organization_id" class="form-control warehouse-select2" multiple required>
                                @foreach($organizations as $id => $title)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, old('organization', $warehouse->organization->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('organization_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-11 mb-3">
                            <label for="status">Статус</label>
                            <select id="status" name="status" class="form-control" required>
                                @foreach(\App\Services\StatusService::getList() as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('status', $warehouse->status ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if (Route::currentRouteName() == 'warehouse.create')
                            <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'warehouse.edit')
                            <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $('.warehouse-select2').select2({
        placeholder: "Барчаси",
        allowClear: true,
        minimumInputLength: 2,
        language: {
            inputTooShort: function () {
                return "Камида 2 та белги киритинг";
            },
            noResults: function () {
                return "Натижа топилмади";
            }
        },
        templateResult: function (data) {
            return data.text;
        },
        templateSelection: function (data) {
            return data.text;
        },
        matcher: function (params, data) {
            if (!data.id) return data;

            var selected = $('#organization_id').val() || [];
            if (selected.includes(data.id)) return null;

            if ($.trim(params.term) === '') return data;

            if (data.text.toLowerCase().includes(params.term.toLowerCase())) {
                return data;
            }

            return null;
        }

    });
</script>
