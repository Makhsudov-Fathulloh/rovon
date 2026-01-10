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
                        <div class="col-md-11 mb-3">
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

