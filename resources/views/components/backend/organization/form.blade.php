<style>
     /* Dropdown optionlari bir xil rangda, hover uchun quyuqroq */
    .moderator-select2 .select2-results__option {
        background-color: #0d6efd;
        color: #fff;
    }

    .moderator-select2 .select2-results__option--highlighted {
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
                                               value="{{ old('title', $organization->title ?? '') }}">
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
                                                {{ old('description', $organization->description ?? '') }}
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
                        {{-- <div class="col-md-11 mb-3">
                            <label for="user_id">Жавобгар ходим</label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">Жавобгар ходимни танланг</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('user_id', $organization->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }} ({{ \App\Services\PhoneFormatService::uzPhone($user->phone) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <div class="col-md-11 mb-3">
                            <label for="users">Жавобгар ходимлар</label>
                            <select name="users[]" id="users" class="form-control moderator-select2" multiple required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ in_array($user->id, old('users', $organization->users->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                                        {{ $user->username }}
                                    </option>
                                @endforeach
                            </select>
                            @error('users')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        @if (Route::currentRouteName() == 'organization.create')
                            <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'organization.edit')
                            <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $('.moderator-select2').select2({
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
        templateResult: function (user) {
            return user.text;
        },
        templateSelection: function (user) {
            return user.text;
        },
        matcher: function (params, data) {
            if (!data.id) return data;

            var selected = $('#user_id').val() || [];
            if (selected.includes(data.id)) return null; // tanlangan optionni dropdowndan yashirish

            if ($.trim(params.term) === '') return data;

            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) return data;

            return null;
        }
    });
</script>
